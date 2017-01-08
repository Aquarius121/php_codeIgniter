<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Fetch_Email_Controller extends Auto_Create_NR_Base {
	
	public function index()
	{
		$cnt = 1;
		
		$sql = "SELECT cd.website, cd.mynewsdesk_company_id
				FROM ac_nr_mynewsdesk_company_data cd
				LEFT JOIN ac_nr_mynewsdesk_fetch_email e
				ON e.mynewsdesk_company_id = cd.mynewsdesk_company_id
				LEFT JOIN ac_nr_mynewsdesk_nr_crawled o
				ON o.mynewsdesk_company_id = cd.mynewsdesk_company_id
				WHERE e.mynewsdesk_company_id IS NULL 
				AND NOT ISNULL(NULLIF(website, ''))
				AND (o.mynewsdesk_company_id IS NOT NULL OR
					cd.newsroom_url IS NULL)
				AND cd.is_website_valid = 1
				AND ISNULL(NULLIF(cd.email, ''))
				ORDER BY cd.mynewsdesk_company_id
				LIMIT 1";

		while ($cnt++ <= 3)
		{
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_MyNewsDesk_Company_Data::from_db($result);
			if (!$c_data) break;

			$this->get($c_data);
		}
	}

	public function get($c_data)
	{
		
		if (empty($c_data->website))
			return false;

		lib_autoload('simple_html_dom');
		$url = $c_data->website;
		$html = @file_get_html($url);

		$m_f_email = new Model_MyNewsDesk_Fetch_Email();
		$m_f_email->mynewsdesk_company_id = $c_data->mynewsdesk_company_id;
		$m_f_email->date_fetched = Date::$now->format(Date::FORMAT_MYSQL);

		if (empty($html))
		{
			$m_f_email->is_website_read_success = 0;
			$m_f_email->save();
			return;
		}

		$m_f_email->is_website_read_success = 1;

		// Trying to locate email address directly 
		// on the home page if one exists

		$email = $this->extract_email_address($html);
		
		if (!empty($email))
		{			
			$m_f_email->is_email_fetched = 1;
			$m_f_email->save();
			
			$this->update_mynewsdesk_c_data($c_data->mynewsdesk_company_id, $email);
			return;
		}

		// email not found on home page
		// now reading the contact page url

		$contact_pattern = '/contact/i';
		$contact_page_slug = null;

		foreach($html->find('a') as $element)
		{
			$href = $element->href;
			
			if (preg_match($contact_pattern, $href, $match))
				$contact_page_slug = $href;
		}


		if (!empty($contact_page_slug))
		{
			$contact_page_web_url = $this->find_complete_url($c_data->website, $contact_page_slug);
			$m_f_email->is_contact_page_found = 1;
			$m_f_email->contact_page_slug = $contact_page_slug;
			
			$html = @file_get_html($contact_page_web_url);

			if (!empty($html))
			{
				$m_f_email->is_contact_page_read_success = 1;
				$email = $this->extract_email_address($html);
				if (!empty($email))
				{			
					$m_f_email->is_email_fetched = 1;
					$this->update_mynewsdesk_c_data($c_data->mynewsdesk_company_id, $email);
				}
			}
		}

		$m_f_email->save();
		
	}

	protected function update_mynewsdesk_c_data($mynewsdesk_company_id, $email)
	{
		$c_data = Model_MyNewsDesk_Company_Data::find($mynewsdesk_company_id);
		$c_data->email = $email;
		$c_data->is_email_from_pr_text = 0;
		$c_data->save();
	}

	
}

?>
