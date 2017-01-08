<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');

class Detect_Lang_Controller extends Auto_Create_NR_Base { 

	public function prs()
	{
		$cnt = 1;

		$sql = "SELECT p.*, cd.content 
				FROM nr_pb_mynewsdesk_content p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				LEFT JOIN nr_content_data cd
				ON p.content_id = cd.content_id
				WHERE p.mynewsdesk_company_id > 0
				AND p.language IS NULL
				AND c.type = ?
				ORDER BY p.content_id
				LIMIT 1";

		while ($cnt++ <= 50)
		{
			$result = $this->db->query($sql, array(Model_Content::TYPE_PR));
			
			if (!$result->num_rows()) break;
		
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::from_db($result);
			if (!$mynewsdesk_pr) break;

			$this->fetch_lang($mynewsdesk_pr);

			if ($cnt%10 == 0)
				sleep(1);
		}
	}


	protected function fetch_lang($mynewsdesk_pr)
	{
		lib_autoload('detect_language');

		$input_str = $mynewsdesk_pr->content;
		$input_str = strip_tags($input_str);
		if (strlen($input_str) > 200)
			$input_str = substr($input_str, 0, 200);

		if (trim($input_str) == "")
		{
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find($mynewsdesk_pr->content_id);
			$mynewsdesk_pr->language = 'en';
			$mynewsdesk_pr->save();
			return;
		}

		$language_code = Detect_Language::detect($input_str);

		if (!empty($language_code))
		{
			$mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find($mynewsdesk_pr->content_id);
			$mynewsdesk_pr->language = $language_code;
			$mynewsdesk_pr->save();
		}
	}



	public function nr_about_blurb()
	{
		$cnt = 1;

		$sql = "SELECT *
				FROM ac_nr_mynewsdesk_company_data
				WHERE NOT ISNULL(NULLIF(about_company, ''))
				AND about_company_lang IS NULL
				ORDER BY mynewsdesk_company_id DESC
				LIMIT 1";

		while ($cnt++ <= 2000)
		{
			$result = $this->db->query($sql);
			
			if (!$result->num_rows()) break;
		
			$mnd_comp = Model_MyNewsDesk_Company_Data::from_db($result);
			if (!$mnd_comp) break;

			$this->fetch_nr_about_lang($mnd_comp);

			if ($cnt%10 == 0)
				sleep(1);
		}
	}


	protected function fetch_nr_about_lang($mnd_comp)
	{
		lib_autoload('detect_language');

		$input_str = $mnd_comp->about_company;
		$input_str = strip_tags($input_str);
		if (strlen($input_str) > 200)
			$input_str = substr($input_str, 0, 200);

		if (trim($input_str) == "")
		{
			$mnd_comp = Model_MyNewsDesk_Company_Data::find($mnd_comp->mynewsdesk_company_id);
			$mnd_comp->about_company_lang = 'en';
			$mynewsdesk_pr->save();
			return;
		}

		$language_code = Detect_Language::detect($input_str);

		if (!empty($language_code))
		{
			$mnd_comp = Model_MyNewsDesk_Company_Data::find($mnd_comp->mynewsdesk_company_id);
			$mnd_comp->about_company_lang = $language_code;
			$mnd_comp->save();
		}
	}
	
}

?>
