<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/process_results_image_variant_trait');
load_controller('shared/process_results_company_name_trait');

class Alert_Controller extends CLI_Base {

	use Beanstalk_Queue_Trait;
	use Process_Results_Image_Variant_Trait;
	use Process_Results_Company_Name_Trait;

	protected $trace_enabled = false;
	protected $trace_time = true;

	const MIN_WAIT_TIME = Model_Insights_Alert::MIN_WAIT_TIME;
	const MAX_RESULTS = 50;

	public function __construct()
	{
		parent::__construct();		

		static::$_JOB_QUEUE = 'insights-alert';
		static::$_JOB_QUEUE_LENGTH = 1000;
		static::$_NUM_WORKERS = 1;

		set_time_limit(0);
	}

	public function index()
	{
		if ($this->process_count() > 1) return;
		if (!$this->init()) return;

		$last_id = 0;
		$queue_10_percent = ceil(static::$_JOB_QUEUE_LENGTH / 10);
		$wait_time = escape_and_quote(Date::seconds(-static::MIN_WAIT_TIME));
		$sql = "SELECT * FROM nr_insights_alert
			WHERE is_enabled = 1 AND date_sent < {$wait_time}
			AND id > ? LIMIT {$queue_10_percent}";

		while (true)
		{
			$dbr = $this->db->query($sql, array($last_id));
			$alerts = Model_Insights_Alert::from_db_all($dbr);
			if (!$alerts) break;

			foreach ($alerts as $alert)
			{
				$this->add_to_queue($alert->serialize(true));
				$last_id = max($alert->id, $last_id);
			}
		}
	}

	public function work(Beanstalk\Job $job)
	{
		$this->trace_info('new job', $job->id);
		$alert = Model_Insights_Alert::__unserialize($job->body);
		$params = $alert->raw_data_object('params');
		$params->date_from = $alert->date_sent;
		$params->date_to = (string) Date::utc();

		$iCt = new Insights_Search_Content($params);
		$iCtFetch = $iCt->fetch(0, static::MAX_RESULTS);

		$this->trace_info('found results',
			count($iCtFetch->results));	

		if (!count($iCtFetch->results))
			continue;

		$iCtFetch->results = $this->process_results_image_variant($iCtFetch->results, 'cover_image_id', 'cover_image', 'finger');
		$iCtFetch->results = $this->process_results_company_name($iCtFetch->results);
		$this->vd->results = array();
		$this->vd->user = $user = Model_User::find($alert->user_id);
		$this->vd->unsubscribe_url = $this->website_url(sprintf(
			'common/insights/unsubscribe/%d/%s',
			$alert->id, $alert->secret));

		foreach ($iCtFetch->results as $result)
		{
			$_result = new Raw_Data();
			if ($result->cover_image)
				$_result->image = $this->website_url(Stored_Image::url_from_filename($result->cover_image->filename));
			$_result->company = $result->company_name;
			$_result->company_id = $result->company_id;
			$_result->id = $result->id;
			$_result->title = $result->title;
			$_result->summary = trim($this->vd->cut($result->summary, 150));
			$_result->url = $this->website_url($result->url());
			$_result->when = Date::difference_in_words(Date::utc($result->date_publish));
			$this->vd->results[] = $_result;
		}

		$message = $this->load->view_return('cli/insights/alert');

		$email = new Email();
		$email->set_to_email($user->email);
		$email->set_from_email($this->conf('no_reply_email'));
		$email->set_to_name($user->name());
		$email->set_from_name('Newswire');
		$email->set_subject('Newswire Insights: Update');
		$email->set_message($message);
		$email->enable_html();
		$email->set_header('List-Unsubscribe', 
			sprintf('<%s>', $this->vd->unsubscribe_url));

		Mailer::queue($email, Mailer::POOL_TRANSACTIONAL);

		$alert->date_sent = Date::utc();
		$alert->save();
	}

}
