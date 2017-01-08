<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Parse_Full_Profiles_Controller extends CLI_Base {
	
	const FILES_DIR = 'raw/mmi_contacts';
		
	protected $trace_enabled = true;
	protected $trace_time = true;

	public function __construct()
	{
		parent::__construct();
		lib_autoload('php_query');
		lib_autoload('portable_utf8');
	}

	public function index()
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		
		$files_dir = static::FILES_DIR;
		$files = glob("{$files_dir}/profiles/*");
		$count = count($files);

		$params = array();
		$params[] = 'mmi';
		$params[] = 'parse_full_profiles';
		$params[] = 'worker';

		$task = new CI_Background_Task();
		$task->set($params);
		$task->run(4);

		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->useTube('parse_full_profiles');

		foreach ($files as $k => $file)
		{
			$beanstalk->put($file);
			$this->trace_info(sprintf('[%d/%d]', $k, $count), 
				basename($file));

			// make sure that the queue doesn't get too big
			// by sleeping until the queue shrinks
			while (true)
			{
				$stats = $beanstalk->statsTube('parse_full_profiles');
				if ($stats['current-jobs-ready'] < 100) break;
				usleep(50000);
			}
		}

		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
		$beanstalk->put('terminate');
	}

	public function worker()
	{
		set_memory_limit('2048M');
		set_time_limit(0);
		$start = time();

		$beanstalk = new Beanstalk\Client();
		$beanstalk->connect();
		$beanstalk->watch('parse_full_profiles');

		while (true)
		{
			$job = $beanstalk->reserve();
			$job->delete();

			// end of processing signalled
			if ($job->body === 'terminate')
				break;

			if (is_file($job->body))
				$this->parse($job->body);

			// restart every 60 seconds
			if (time()-$start >= 60)
			{
				$params = array();
				$params[] = 'mmi';
				$params[] = 'parse_full_profiles';
				$params[] = 'worker';
				$task = new CI_Background_Task();
				$task->set($params);
				$task->run(1);
				break;
			}
		}
	}

	public function parse($file)
	{
		set_memory_limit('2048M');
		set_time_limit(0);

		$source = to_utf8_3b(file_get_contents($file));
		$doc = phpQuery::newDocumentHTML($source);
		preg_match('#/([0-9]+)$#i', $file, $match);
		$expected_id = (int) $match[1];
		$remote_id = (int) pq('#c', $doc)->val();

		if ($expected_id != $remote_id)
		{
			$this->trace_failure('id mismatch', $expected_id);
			return;
		}

		$data = new stdClass();
		$data->first_name = trim(pq('.primary-info h1 span:not(:last)', $doc)->text());
		$data->last_name = trim(pq('.primary-info h1 span:last', $doc)->text());
		$data->title = trim(pq('.primary-info .sub-title', $doc)->text());
		$data->date_updated = Date::utc(trim(pq('.system-note-modified time', $doc)->attr('datetime')))->__toString();
		$data->phone = trim(pq('.data-section .key-val dt:contains("Phone") + dd p:first', $doc)->text());
		$data->email = trim(pq('.data-section .key-val dt:contains("Email") + dd p:first a', $doc)->text());
		$data->fax = trim(pq('.data-section .key-val dt:contains("Fax") + dd', $doc)->text());
		$data->twitter = Social_Twitter_Profile::parse_id(trim(pq('.data-section .key-val dt:contains("Twitter") + dd a', $doc)->text()));
		$data->linkedin = Social_Linkedin_Profile::parse_id(trim(pq('.data-section .key-val dt:contains("LinkedIn") + dd a', $doc)->attr('href')));
		$data->picture = trim(pq('#contactProfileImg', $doc)->attr('src'));
		
		$data->phone = trim(preg_replace('#Preferred#i', null, $data->phone));
		$data->email = trim(preg_replace('#Preferred#i', null, $data->email));
		$data->fax = trim(preg_replace('#Preferred#i', null, $data->fax));

		$address_dd = pq('.data-section .key-val dt:contains("Address") + dd', $doc);
		$address_doc = phpQuery::newDocumentHTML($address_dd->html());
		pq('span.data-callout', $address_doc)->remove();
		$address_html = $address_doc->html();
		$address_text = preg_replace('#<[^>]+>#i', CRLF, $address_html);
		$address_text = preg_replace('#\s*\r?\n\s*#', CRLF, $address_text);
		$address_lines = preg_split('#(,|\r?\n)#i', $address_text);

		foreach ($address_lines as $k => $v)
			if (preg_match('#[,;\.\-]#', $v) || !$v) unset($address_lines[$k]);
			else $address_lines[$k] = trim($v);
		$address_lines = array_values($address_lines);

		$data->address_country = trim(pq('span.data-callout img', $address_dd, $doc)->attr('title'));
		$data->address_country_flag = trim(pq('span.data-callout img', $address_dd, $doc)->attr('src'));
		$data->address = $address_lines;
		$data->address_locality_id = null;
		$data->address_region_id = null;
		$data->address_locality = null;
		$data->address_region = null;
		$data->address_zip = null;

		foreach ($address_lines as $k => $line)
		{
			$sql = "select * from nr_locality where name like ? limit 1";
			$locality = Model_Locality::from_sql($sql, $line);
			if (!$locality) continue;
			$data->address_locality_id = $locality->id;
			$data->address_locality = $line;
			$data->address_region_id = $locality->region_id;
			break;
		}

		foreach ($address_lines as $k => $line)
		{
			$sql = "select * from nr_region where name like ? or abbr like ? limit 1";
			$region = Model_Region::from_sql($sql, $line, $line);
			if (!$region) continue;
			if ($data->address_region_id && $data->address_region_id != $region->id)
				$data->address_locality_id = null;
			$data->address_region_id = $region->id;
			$data->address_region = $line;
			break;
		}

		foreach ($address_lines as $k => $line)
		{
			if (preg_match('#^[0-9\-\s]+$#', $line))
			{
				$data->address_zip = $line;
				break;
			}
		}

		$outlets_html = trim(pq('.data-section .key-val dt:contains("Outlets") + dd', $doc)->html());
		$outlets_html = preg_replace('#<li>\s*<a#i', '</li><li><a', $outlets_html);
		$outlets_html = preg_replace('#span>\s*</li>#i', 'span></li><li>', $outlets_html);
		$outlets_doc = phpQuery::newDocumentHTML($outlets_html);
		$outlets_li = pq('li', $outlets_doc);

		$data->roles = array();
		$data->companies = array();

		foreach ($outlets_li as $k => $li)
		{
			if ($k % 2 == 0)
			{
				if ($k + 1 === count($outlets_li))
					break;

				$role = pq($li)->text();
				$role = trim(preg_replace('#\s*:\s*$#i', null, $role));
				$data->roles[] = $role;
			}
			else
			{
				$company = pq($li)->text();
				$company = str_replace(utf8_chr('\u00a0'), ' ', $company);
				$company = trim($company);
				$data->companies[] = $company;
			}
		}

		$data->company_name = @$data->companies[0];

		$languages = preg_split('#\s+#', trim(pq('.data-section .key-val dt:contains("Language") + dd', $doc)->text()));
		foreach ($languages as $k => $v) if (!$v) unset($languages[$k]);
		$data->languages = array_values($languages);

		$data->beats = array();
		$beats = pq('.data-section .key-val dt:contains("Beats") + dd li', $doc);
		foreach ($beats as $beat)
		{
			$beat_text = trim(pq($beat)->text());
			$data->beats[] = $beat_text;
		}

		$profile_doc = phpQuery::newDocumentHTML(pq('.section-content.profile', $doc)->html());
		pq('h2:contains("Attended") + .data', $profile_doc)->remove();
		pq('h2', $profile_doc)->remove();
		$data->profile = trim(preg_replace('#\s+#s', ' ', pq($profile_doc)->text()));

		foreach ($data as $k => $v)
			if (!$v) $data->$k = null;

		$mmi_contact = Model_MMI_Contact::find($remote_id);

		if (!$mmi_contact)
		{
			$mmi_contact = new Model_MMI_Contact();
			$mmi_contact->remote_id = $remote_id;
		}

		$mmi_rd = $mmi_contact->raw_data();
		if (!$mmi_rd) $mmi_rd = new stdClass();

		foreach ($data as $k => $v)
			if ($v) $mmi_rd->{$k} = $v;

		$mmi_contact->raw_data($mmi_rd);
		$mmi_contact->save();
		return;
	}
		
}

?>