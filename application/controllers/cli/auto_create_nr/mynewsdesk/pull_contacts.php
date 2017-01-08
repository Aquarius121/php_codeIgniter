<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch 
// the MyNewsDesk contacts on demand

load_controller('cli/auto_create_nr/base');

class Pull_Contacts_Controller extends Auto_Create_NR_Base {

	public function index($company_id)
	{
		if (empty($company_id))
			return;

		$cnt = 1;
		
		$sql = "SELECT cd.newsroom_url, cd.mynewsdesk_company_id,
				c.company_id
				FROM ac_nr_mynewsdesk_company c
				INNER JOIN ac_nr_mynewsdesk_company_data cd
				ON cd.mynewsdesk_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_contacts_fetched = 0
				ORDER BY cd.mynewsdesk_company_id 
				LIMIT 1";
		
		$result = $this->db->query($sql);
		if (!$result->num_rows()) return;
		
		$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
		if (!$c_data) return;

		$this->get($c_data);

		$c_data->is_contacts_fetched = 1;
		$c_data->save();

		
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

		if ($nr_crawled = Model_MyNewsDesk_NR_Contact_Crawled::find($c_data->mynewsdesk_company_id))
		{
			$m_c_data = Model_MyNewsDesk_Company_Data::find($c_data->mynewsdesk_company_id);
			$m_c_data->is_contacts_fetched = 1;
			$m_c_data->save();
			return;
		}
		else
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
			$is_contact_added = $this->add_company_contact($contact, $c_data->mynewsdesk_company_id, $c_data->company_id);
			$num_contacts_added += (int) $is_contact_added;
		}

		$nr_crawled->num_contacts_added = $num_contacts_added;
		$nr_crawled->save();
		
	}

	protected function add_company_contact($contact, $mynewsdesk_company_id, $company_id)
	{
		$newsroom = Model_Newsroom::find($company_id);

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
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array('email', $email);

		$is_contact_added = 0;
		if (!empty($name) && !empty($email) && !$c_contact = Model_Company_Contact::find($criteria))
		{
			$c_contact = new Model_Company_Contact();
			$c_contact->company_id = $company_id;
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
			$c_contact->source = Model_Company_Contact::SOURCE_MYNEWSDESK;
			$c_contact->save();

			if ($is_press_contact)
			{
				$newsroom->company_contact_id = $c_contact->id;
				$newsroom->save();
			}

			if (!empty($image_url))
			{
				$cover_file = "contact";
				@copy($image_url, $cover_file);

				if (Image::is_valid_file($cover_file))
				{
					// import the logo image into the system
					$contact_im = Quick_Image::import("contact", $cover_file);
					 
					// assign to the new company and save
					$contact_im->company_id = $company_id;
					$contact_im->save();
					 
					// set it to use the new logo image and save
					$c_contact->image_id = $contact_im->id;
					$c_contact->save();
				}
			}

			

			$is_contact_added = 1;

			sleep(2);
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
