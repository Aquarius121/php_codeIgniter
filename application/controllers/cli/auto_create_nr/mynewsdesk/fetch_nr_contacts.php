<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_NR_Contacts_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.newsroom_url, cd.mynewsdesk_company_id
				FROM ac_nr_mynewsdesk_company_data cd
				LEFT JOIN ac_nr_mynewsdesk_nr_contact_crawled w
				ON w.mynewsdesk_company_id = cd.mynewsdesk_company_id
				WHERE w.mynewsdesk_company_id IS NULL 
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				ORDER BY cd.mynewsdesk_company_id 
				LIMIT 1";

		while ($cnt++ <= 30)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			if (!$c_data) break;

			sleep(1);

			$this->get($c_data);
		}
	}

	public function get($c_data)
	{
		if (empty($c_data->newsroom_url))
			return false;

		lib_autoload('simple_html_dom');

		$url = $c_data->newsroom_url;
		
		if (!empty($url))
		{
			if (strlen($url) > 0 && substr($url, strlen($url) - 1, 1) != "/")
				$url = "{$url}/";

			$url = "{$url}contact_people";
		}

		$html = @file_get_html($url);

		$nr_crawled = new Model_MyNewsDesk_NR_Contact_Crawled();
		$nr_crawled->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
		$nr_crawled->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$nr_crawled->is_page_read_success = 0;
			$nr_crawled->save();
			return;
		}

		$nr_crawled->is_page_read_success = 1;

		$num_contacts_added = 0;

		foreach (@$html->find('div[class=row-contacts] div[class=vcard]') as $contact)
		{
			$is_contact_added = $this->add_company_contact($contact, $c_data->mynewsdesk_company_id);
			$num_contacts_added += (int) $is_contact_added;
		}

		$nr_crawled->num_contacts_added = $num_contacts_added;
		$nr_crawled->save();
		
	}

	protected function add_company_contact($contact, $mynewsdesk_company_id)
	{
		$name = $role = $area_of_specialization = $phone = $email = $image_url = 
			$description = $is_press_contact = null;

		$name = @$contact->find('div[class=header] h2[class=newsroom-list-header]', 0)->plaintext;

		foreach ($role_span = @$contact->find('li span[class=role]') as $i => $role_span)
			if ($i == 0)
				$role = @$role_span->plaintext;
			else
				$area_of_specialization = @$role_span->plaintext;
		

		if ($tel = @$contact->find('li[class=tel]', 0))
			$phone = @$tel->find('span[class=value]', 0)->plaintext;
		
		$email = null;
		if ($email_span = @$contact->find('span[class=obfuscated-email]', 0))
		{
			foreach ($email_span->find('span[class=hide]') as $hide_element)
				$hide_element->innertext = "";

			foreach ($email_span->find('span') as $hide_element)
			{
				$text = $hide_element->innertext;
				$email = "{$email}{$text}";
			}

			if (empty($email))
			{
				$em = $email_span->innertext;
				$em = str_replace("&#", "", $em);
				$chars = explode(";", $em);
				foreach ($chars as $ch)
					$email .= chr($ch);
			}
		}

		if (@$contact->find('span[class=press-contact]', 0))
			$is_press_contact = 1;

		if ($contact_div = @$contact->find('div[class=media-wrapper]', 0))
			if ($img = @$contact_div->find('img', 0))
			{
				$img_alt = @$img->alt;
				if (!empty($img_alt) && trim($img_alt) != "User-no-image")
					$image_url = @$img->src;	
			}

		$description = $contact->find('div[class=hidden-small]', 0)->innertext;
		if (!empty($description))
		{
			$description = trim($description);
			$contact_url = @$contact->find('div[class=hidden-small] a', 0)->href;
		}

		$anchors = array();

		foreach(@$contact->find('a') as $element)
		{
			$anchors[] = $element->href;
			if (@$element->title == 'Skype')
				$skype = $element->innertext;
		}

		$socials = $this->extract_socials($anchors);

		if (!empty($socials['soc_fb'])) 
			$facebook = Social_Facebook_Profile::parse_id($socials['soc_fb']);		

		if (!empty($socials['soc_twitter']))
			$twitter = Social_Twitter_Profile::parse_id($socials['soc_twitter']);		

		if (!empty($socials['soc_linkedin']))
			$linkedin = Social_Linkedin_Profile::parse_id($socials['soc_linkedin']);


		if (!empty($contact_url))
			$description = $this->get_description($contact_url);

		$criteria = array();
		$criteria[] = array('company_id', 0);
		$criteria[] = array('email', $email);
		
		$is_contact_added = 0;
		if (!empty($name) && !empty($email) && !$c_contact = Model_Company_Contact::find($criteria))
		{
			$c_contact = new Model_Company_Contact();
			$c_contact->name = $name;
			$c_contact->title = value_or_null($role);
			$c_contact->email = value_or_null($email);
			$c_contact->phone = value_or_null($phone);
			$c_contact->description = value_or_null($description);
			$c_contact->twitter = value_or_null($twitter);
			$c_contact->skype = value_or_null($skype);
			$c_contact->facebook = value_or_null($facebook);
			$c_contact->linkedin = value_or_null($linkedin);
			$c_contact->name_to_slug();
			$c_contact->save();

			$m_mynewsdesk_contact = new Model_MyNewsDesk_Contact();
			$m_mynewsdesk_contact->mynewsdesk_company_id = $mynewsdesk_company_id;
			$m_mynewsdesk_contact->company_contact_id = $c_contact->id;
			$m_mynewsdesk_contact->area_of_specialization = value_or_null($area_of_specialization);
			$m_mynewsdesk_contact->is_press_contact = value_or_null($is_press_contact);
			$m_mynewsdesk_contact->image_url = value_or_null($image_url);
			$m_mynewsdesk_contact->save();
			

			$is_contact_added = 1;
		}

		return $is_contact_added;
	}

	protected function get_description($contact_url)
	{
		if (empty($contact_url))
			return null;

		if (!empty($contact_url) && substr($contact_url, 0, 4) != "http")
			$contact_url = "http://www.mynewsdesk.com{$contact_url}";

		$c_html = @file_get_html($contact_url);
		
		if (empty($c_html))
			return null;

		$description = null;
		
		if ($vcard = @$c_html->find('div[class=vcard]', 0))
			$description = $vcard->find('div[class=markdown]', 0)->innertext;

		if (!empty($description))
			$description = trim($description);
		
		return $description;
	}
	
}

?>
