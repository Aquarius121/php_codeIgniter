<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from within
// the admin area to build newsrooms

load_controller('cli/auto_create_nr/base');

class Build_NRs_Controller extends Auto_Create_NR_Base { 

	public function index($prweb_company_ids_list = null)
	{
		set_time_limit(3000);

		$prweb_company_ids = unserialize($prweb_company_ids_list);

		if (empty($prweb_company_ids) || !is_array($prweb_company_ids) || !count($prweb_company_ids))
			return false;

		$id_list = sql_in_list($prweb_company_ids);

		$sql = "SELECT id
				FROM ac_nr_prweb_company
				WHERE id IN ({$id_list})
				AND ISNULL(NULLIF(company_id, 0))
				LIMIT 1";

		while (1)
		{
			$result = $this->db->query($sql);
			
			if (!$result->num_rows()) break;
		
			$comp = Model_PRWeb_Company::from_db($result);
			if (!$comp) break;

			$this->build($comp->id);
		}
	}

	protected function build($id = null)
	{
		set_time_limit(3000);

		if (!$id)
			return false;

		$comp = Model_PRWeb_Company::find($id);
		$c_data = Model_PRWeb_Company_Data::find($id);
		$prweb_cat = Model_PRWeb_Category::find($comp->prweb_category_id);

		$comp->name = $this->sanitize($comp->name);
		$c_data->short_description = $this->sanitize($c_data->short_description);
		$c_data->about_company = $this->sanitize($c_data->about_company);
		
		$newsroom = Model_Newsroom::create(1, $comp->name);		
		$newsroom->save();

		$nr_custom = new Model_Newsroom_Custom();
		$company_profile = new Model_Company_Profile();
		$nr_custom->company_id = $newsroom->company_id;

		$comp->company_id = $newsroom->company_id;
		$comp->save();

		// fetching and setting the logo
		if (!empty($c_data->logo_image_path) && $c_data->is_logo_valid)
		{
			$logo_file = "logo";
			$logo_url = $c_data->logo_image_path;
			@copy($logo_url, $logo_file);

			if (Image::is_valid_file($logo_file))
			{
				// import the logo image into the system
				$logo_im = LEGACY_Image::import("logo", $logo_file);
				 
				// assign to the new company and save
				$logo_im->company_id = $newsroom->company_id;
				$logo_im->save();
				 
				// set it to use the new logo image and save
				$nr_custom->logo_image_id = $logo_im->id;
			}
		}

		$nr_custom->use_white_header = 1;
		
		$nr_custom->save();

		if ($newsroom->company_id)
		{
			$sql = "UPDATE nr_content
					SET company_id = ?
					WHERE company_id = 0 
					AND id IN (
						SELECT content_id
						FROM nr_pb_prweb_pr
						WHERE prweb_company_id = ?)";

			$this->db->query($sql, array($newsroom->company_id, $c_data->prweb_company_id));
		}

		// Now importing the PR images to our system
		$prefixes = Model_PB_PRWeb_PR::__prefixes('pb', 'pb_prweb_pr');
		$sql = "SELECT c.*, {$prefixes} 
				FROM nr_content c
				INNER JOIN nr_pb_prweb_pr pb
				ON pb.content_id = c.id
				WHERE pb.is_cover_image_imported = 0
				AND pb.cover_image_url IS NOT NULL
				AND c.company_id = ?
				ORDER BY c.id
				LIMIT 1";

		while (1)
		{
			if (!$result = Model_Content::from_sql($sql, array($newsroom->company_id), 
				array('pb_prweb_pr' => 'Model_PB_PRWeb_PR')))
				break;


			if (!empty($result->pb_prweb_pr->cover_image_url))
			{
				$cover_im_file = "cover";
				$cover_im_url = $result->pb_prweb_pr->cover_image_url;
				if (@copy($cover_im_url, $cover_im_file))
				{
					if (Image::is_valid_file($cover_im_file))
					{
						// import the logo image into the system
						$cover_im = LEGACY_Image::import("cover", $cover_im_file);
						 
						// assign to the new company and save
						$cover_im->company_id = $result->company_id;
						$cover_im->save();

						// set it to use the new logo image and save
						$result->cover_image_id = $cover_im->id;
						$result->save();
					}
				}
				$result->pb_prweb_pr->is_cover_image_imported = 1;
				$result->pb_prweb_pr->save();
			}
		}

		$company_profile->company_id = $newsroom->company_id;
		$company_profile->address_street = value_or_null($c_data->address);
		//$company_profile->address_apt_suite = value_or_null($c_data->address_apt_suite);
		$company_profile->address_city = value_or_null($c_data->city);
		$company_profile->address_state = value_or_null($c_data->state);
		$company_profile->address_zip = value_or_null($c_data->zip);
		$company_profile->website = value_or_null($c_data->website);
		$company_profile->phone = value_or_null($c_data->phone);
		$company_profile->summary = value_or_null($c_data->short_description);
		$company_profile->description = value_or_null($c_data->about_company);
		$company_profile->address_country_id = value_or_null($c_data->country_id);
		$company_profile->beat_id = value_or_null($prweb_cat->newswire_beat_id);

		$this->nr_build_socials($company_profile, $c_data);
		
		$company_profile->soc_rss = value_or_null($c_data->blog_rss);

		$company_profile->save();

		$this->update_company_source($newsroom->company_id, Model_Company::SOURCE_PRWEB);
		$this->generate_token($newsroom->company_id);
	}
}

?>
