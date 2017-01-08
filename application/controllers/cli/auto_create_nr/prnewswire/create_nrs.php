<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/auto_create_nr/base');
class Create_NRs_Controller extends Auto_Create_NR_Base {

	public function index()
	{
		$sql = "SELECT c.*,
				{{ pb.* AS prn_pr USING Model_PB_PRN_PR }},
				{{ pc.* AS prn_comp USING Model_PRN_Company }}
				FROM nr_pb_prn_pr pb
				INNER JOIN ac_nr_prn_company pc
				ON pb.prn_company_id = pc.id
				INNER JOIN nr_content c
				ON pb.content_id = c.id
				WHERE pb.is_nr_assigned = 0
				AND c.date_publish > '2016-07-26'
				ORDER BY c.date_publish DESC
				LIMIT 10";

		$cnt = 1;
		while ($cnt++ <= 5000)
		{
			$results = Model_Content::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $content)
				$this->check_assign_nr($content);
		}
	}

	protected function check_assign_nr($content)
	{
		$this->console($content->slug);

		if ($content->prn_comp->company_id)
		{
			$content->company_id = $content->prn_comp->company_id;
			$content->save();

			$content->prn_pr->is_nr_assigned = 1;
			$content->prn_pr->save();
			return;
		}

		$this->console("LOGO => " . $content->prn_comp->logo_url);
		$this->console("-----------------------------------------");

		$newsroom = Model_Newsroom::create(1, $content->prn_comp->name);		
		$newsroom->source = Model_Company::SOURCE_PRNEWSWIRE;
		$newsroom->save();

		$nr_custom = new Model_Newsroom_Custom();
		$nr_custom->company_id = $newsroom->company_id;
		
		// fetching and setting the logo
		if (!empty($content->prn_comp->logo_url))
		{
			$logo_file = "logo";
			$logo_url = $content->prn_comp->logo_url;
			@copy($logo_url, $logo_file);

			if (Image::is_valid_file($logo_file))
			{
				$logo_im = Quick_Image::import("logo", $logo_file);

				$logo_im->company_id = $newsroom->company_id;
				$logo_im->save();

				$nr_custom->logo_image_id = $logo_im->id;
			}
		}

		$nr_custom->use_white_header = 1;	
		$nr_custom->save();

		$content->prn_comp->company_id = $newsroom->company_id;
		$content->prn_comp->save();

		$content->company_id = $newsroom->company_id;
		$content->save();

		$content->prn_pr->is_nr_assigned = 1;
		$content->prn_pr->save();
	}

	public function fetch_save_logos()
	{
		set_time_limit(86400);
		$sql = "SELECT c.*,
				{{ nc.* AS nr_custom USING Model_Newsroom_Custom }}
				FROM ac_nr_prn_company c
				INNER JOIN nr_newsroom_custom nc
				ON c.company_id = nc.company_id
				WHERE nc.logo_image_id IS NULL
				AND c.is_logo_pulled = 0
				ORDER BY nc.company_id DESC
				LIMIT 5";

		$cnt = 1;

		while ($cnt++ <= 1000)
		{
			$results = Model_PRN_Company::from_sql_all($sql);

			if (!count($results))
				break;

			foreach ($results as $prn_c)
			{
				$this->pull_logo($prn_c);
				$prn_c->is_logo_pulled = 1;
				$prn_c->save();
			}

			$this->inspect($cnt);
		}
	}

	protected function pull_logo($prn_c)
	{
		if (empty($prn_c->logo_url))
			return;

		if (!empty($prn_c->logo_url))
		{
			$logo_file = "logo";
			$logo_url = $prn_c->logo_url;
			@copy($logo_url, $logo_file);

			if (Image::is_valid_file($logo_file))
			{
				$logo_im = Quick_Image::import("logo", $logo_file);

				$logo_im->company_id = $prn_c->company_id;
				$logo_im->save();

				$prn_c->nr_custom->logo_image_id = $logo_im->id;
				$prn_c->nr_custom->save();
			}
		}		
	}	
}