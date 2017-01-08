<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Contact_Us_URL_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{

		$sql = "SELECT cd.*
				FROM ac_nr_topseos_company_data cd
				LEFT JOIN ac_nr_topseos_fetch_contact_us_url e
				ON e.topseos_company_id = cd.topseos_company_id
				WHERE e.topseos_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))
				ORDER BY cd.topseos_company_id
				LIMIT 1";
		
		while (1)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_TopSeos_Company_Data::from_db($result);
			if (!$c_data) break;

			$this->get($c_data);

			sleep(1);
		}
	}

	public function get($c_data)
	{
		if (empty($c_data->website))
			return false;

		lib_autoload('simple_html_dom');
		$url = $c_data->website;
		$html = @file_get_html($url);

		$m_f_contact = new Model_TopSeos_Fetch_Contact_Us_URL();
		$m_f_contact->topseos_company_id = $c_data->topseos_company_id;
		$m_f_contact->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$m_f_contact->is_website_read_success = 0;
			$m_f_contact->save();
			return;
		}

		$m_f_contact->is_website_read_success = 1;

		// now reading the contact page url

		$contact_pattern = '/contact/i';
		$contact_page_slug = null;

		foreach($html->find('a') as $element)
		{
			$href = $element->href;
			
			if (preg_match($contact_pattern, $href, $match) && !$this->extract_email_address($href))
				$contact_page_slug = $href;
		}


		if (!empty($contact_page_slug))
		{
			$contact_page_web_url = $this->find_complete_url($c_data->website, $contact_page_slug);
			$m_f_contact->is_contact_page_url_found = 1;
			
			$c_data->contact_page_url = $contact_page_web_url;
			$c_data->save();
		}

		$m_f_contact->save();
		
	}

	protected function update_topseos_c_data($topseos_company_id, $email)
	{
		$c_data = Model_TopSeos_Company_Data::find($topseos_company_id);
		$c_data->email = $email;
		$c_data->is_email_from_pr_text = 0;
		$c_data->save();
	}

	
}

?>
