<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from 
// within the admin area to fetch 
// the PR_Co contacts on demand

load_controller('cli/auto_create_nr/base');

class Pull_Contacts_Controller extends Auto_Create_NR_Base {

	public function index($company_id)
	{
		if (empty($company_id))
			return;

		$cnt = 1;
		
		$sql = "SELECT cd.newsroom_url, cd.pr_co_company_id,
				c.company_id
				FROM ac_nr_pr_co_company c
				INNER JOIN ac_nr_pr_co_company_data cd
				ON cd.pr_co_company_id = c.id
				WHERE c.company_id = '{$company_id}'
				AND NOT ISNULL(NULLIF(cd.newsroom_url, ''))
				AND cd.is_contacts_fetched = 0
				ORDER BY cd.pr_co_company_id 
				LIMIT 1";
		
		$result = $this->db->query($sql);
		if (!$result->num_rows()) return;
		
		$c_data = Model_PR_Co_Company_Data::from_db($result);
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

		$html = @file_get_html($url);

		if (empty($html))
			return;

		$num_contacts_added = 0;

		foreach (@$html->find('div[id=panel_contacts] ul li, ul[id=pr_contacts] li') as $contact)
		{
			$is_contact_added = $this->add_company_contact($contact, $c_data->pr_co_company_id, $c_data->company_id);
		}
	}

	protected function add_company_contact($contact, $pr_co_company_id, $company_id)
	{
		$newsroom = Model_Newsroom::find($company_id);

		$name = $role = $area_of_specialization = $phone = $email = $image_url = 
			$description = $is_press_contact = null;

		$name = @$contact->find('.contact_name', 0)->plaintext;


		if ($contact_info = @$contact->find('div.contact_info', 0))
		{
			$c_info_text = @$contact_info->plaintext;
			$lines = explode("\n", $c_info_text);
			$role = $lines[0];
		}

		$phone = $this->extract_phone_number($contact->innertext);

		$email = $this->extract_email_address($contact->innertext);

		if ($img = @$contact->find('img.avatar', 0))
			$image_url = @$img->src;	

		$anchors = array();

		foreach(@$contact->find('a') as $element)
			$anchors[] = $element->href;

		if ($skype_anchor = @$contact->find('a.track_event[data-action=click skype link]', 0))
			$skype = $skype_anchor->plaintext;
			
		$socials = $this->extract_socials($anchors);

		if (!empty($socials['soc_fb'])) 
			$facebook = Social_Facebook_Profile::parse_id($socials['soc_fb']);		

		if (!empty($socials['soc_twitter']))
			$twitter = Social_Twitter_Profile::parse_id($socials['soc_twitter']);		

		if (!empty($socials['soc_linkedin']))
			$linkedin = Social_Linkedin_Profile::parse_id($socials['soc_linkedin']);

		$criteria = array();
		$criteria[] = array('company_id', $company_id);
		$criteria[] = array('email', $email);

		$is_contact_added = 0;
		if (!empty($name) && !$c_contact = Model_Company_Contact::find($criteria))
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
			$c_contact->source = Model_Company_Contact::SOURCE_PR_CO;
			$c_contact->save();

			if ($is_press_contact)
			{
				$newsroom->company_contact_id = $c_contact->id;
				$newsroom->save();
			}

			/*
			// not pulling the image as of now
			// because the image is very small
			// and looks bad in our system
			
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
			*/

			$is_contact_added = 1;

			sleep(2);
		}

		return $is_contact_added;
	}
}

?>
