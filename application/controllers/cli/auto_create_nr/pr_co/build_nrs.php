<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// This CLI script is called from within
// the admin area to build newsrooms

load_controller('cli/auto_create_nr/base');

class Build_NRs_Controller extends Auto_Create_NR_Base { 

	public function index($pr_co_company_ids_list = null)
	{
		set_time_limit(3000);

		$pr_co_company_ids = unserialize($pr_co_company_ids_list);

		if (empty($pr_co_company_ids) || !is_array($pr_co_company_ids) || !count($pr_co_company_ids))
			return false;
						
		$id_list = sql_in_list($pr_co_company_ids);

		$sql = "SELECT id
				FROM ac_nr_pr_co_company
				WHERE id IN ({$id_list})
				AND ISNULL(NULLIF(company_id, 0))
				LIMIT 1";

		while (1)
		{
			$result = $this->db->query($sql);
			
			if (!$result->num_rows()) break;
		
			$comp = Model_PR_Co_Company::from_db($result);
			if (!$comp) break;

			$this->build($comp->id);
		}
	}

	protected function build($id = null)
	{
		set_time_limit(3000);

		if (!$id)
			return false;

		$comp = Model_PR_Co_Company::find($id);
		$c_data = Model_PR_Co_Company_Data::find($id);
		$pr_co_cat = Model_PR_Co_Category::find($comp->pr_co_category_id);

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
				$logo_im = Quick_Image::import("logo", $logo_file);
				 
				$logo_im->company_id = $newsroom->company_id;
				$logo_im->save();
				 
				$nr_custom->logo_image_id = $logo_im->id;
			}
		}

		$nr_custom->use_white_header = 1;
		
		$nr_custom->save();

		if ($newsroom->company_id)
		{
			$sql = "UPDATE nr_content
					SET company_id = ?
					WHERE type = ?
					AND company_id = 0 
					AND id IN (
						SELECT content_id
						FROM nr_pb_pr_co_content
						WHERE pr_co_company_id = ?)";

			$this->db->query($sql, array($newsroom->company_id, Model_Content::TYPE_PR,
							$c_data->pr_co_company_id));
		}	

		// Updating cover images for the PRs
		$sql = "SELECT c.*, p.cover_image_url
				FROM nr_content c
				INNER JOIN nr_pb_pr_co_content p
				ON p.content_id = c.id
				WHERE p.pr_co_company_id = ?
				AND p.cover_image_url IS NOT NULL";

		$query = $this->db->query($sql, array($c_data->pr_co_company_id));
		$results = Model_Content::from_db_all($query);

		foreach ($results as $result)
		{
			if (!empty($result->cover_image_url))
			{
				$cover_file = "cover";
				$img_url = $result->cover_image_url;
				@copy($img_url, $cover_file);

				if (Image::is_valid_file($cover_file))
				{
					$pr_im = Quick_Image::import("cover", $cover_file);
					$pr_im->company_id = $newsroom->company_id;
					$pr_im->save();
					 
					$result->cover_image_id = $pr_im->id;
					$result->save();
				}
			}			
		}



		// Updating related images and
		// importing files for the PRs
		$sql = "SELECT c.*, 
				p.content_id AS m_pb_pr_co_content__content_id,
				p.raw_data AS m_pb_pr_co_content__raw_data
				FROM nr_content c
				INNER JOIN nr_pb_pr_co_content p
				ON p.content_id = c.id
				INNER JOIN nr_pb_pr pb
				ON pb.content_id = c.id
				WHERE p.pr_co_company_id = ?
				AND p.raw_data IS NOT NULL";

		$query = $this->db->query($sql, array($c_data->pr_co_company_id));
		$prefixes = array('m_pb_pr_co_content' => 'Model_PB_PR_Co_Content');

		$results = Model_Content::from_db_all($query, $prefixes);

		foreach ($results as $result)
		{
			$image_urls = array();
			$images = array();

			$files = array();

			$raw_data = $result->m_pb_pr_co_content->raw_data();
			$image_urls = @$raw_data->images;
			$files = @$raw_data->files;
			
			if (is_array($files) || count($files))
			{
				$file_num = 0;
				foreach ($files as $file)
				{
					if (!empty($file))
					{
						$file_name = "Download";
						$s_file = basename($file);
						@copy($file, $s_file);
						$stored_file = LEGACY_File::import($s_file);
					}
				}
			}

			if (is_array($image_urls) || count($image_urls))
			{
				foreach ($image_urls as $image_url)
				{
					if (!empty($image_url))
					{
						$related_file = "related";
						@copy($image_url, $related_file);

						if (Image::is_valid_file($related_file))
						{
							$pr_im = Quick_Image::import("related", $related_file);
							$pr_im->company_id = $newsroom->company_id;
							$pr_im->save();
							
							$images[] = $pr_im->id;
						}
					}
				}
			
				if (count($images))
					$result->set_images($images);
			}
		}

		// Updating related files for the PRs
		$sql = "SELECT c.*, 
				p.content_id AS m_pb_pr_co_content__content_id,
				p.raw_data AS m_pb_pr_co_content__raw_data
				FROM nr_pb_pr c
				INNER JOIN nr_pb_pr_co_content p
				ON p.content_id = c.content_id
				WHERE p.pr_co_company_id = ?
				AND p.raw_data IS NOT NULL";

		$query = $this->db->query($sql, array($c_data->pr_co_company_id));
		$prefixes = array('m_pb_pr_co_content' => 'Model_PB_PR_Co_Content');

		$results = Model_PB_PR::from_db_all($query, $prefixes);

		foreach ($results as $result)
		{
			$files = array();
			$raw_data = $result->m_pb_pr_co_content->raw_data();
			$files = @$raw_data->files;
		
			if (is_array($files) || count($files))
			{
				$file_num = 0;
				foreach ($files as $file)
				{
					if (!empty($file))
					{
						$file_name = "Download";
						$s_file = basename($file);
						@copy($file, $s_file);
						$stored_file = LEGACY_File::import($s_file);
						
						if ($file_num == 0)
						{								
							$result->stored_file_id_1 = $stored_file->id;
							$result->stored_file_name_1 = $file_name;
							$result->save();
							$file_num++;
						}
						else
						{
							$result->stored_file_id_2 = $stored_file->id;
							$result->stored_file_name_2 = $file_name;
							$result->save();
						}
												
					}
				}
			}
		}
		
				
		$company_profile->company_id = $newsroom->company_id;
		$company_profile->address_street = value_or_null($c_data->address);
		$company_profile->address_city = value_or_null($c_data->city);
		$company_profile->address_state = value_or_null($c_data->state);
		$company_profile->address_zip = value_or_null($c_data->zip);
		$company_profile->website = value_or_null($c_data->website);
		$company_profile->phone = value_or_null($c_data->phone);
		$company_profile->summary = value_or_null($c_data->short_description);
		$company_profile->description = value_or_null($c_data->about_company);
		$company_profile->address_country_id = value_or_null($c_data->country_id);
		$company_profile->beat_id = value_or_null($pr_co_cat->newswire_beat_id);
		
		$this->nr_build_socials($company_profile, $c_data);

		$company_profile->soc_rss = value_or_null($c_data->blog_rss);

		$company_profile->save();

		$this->update_company_source($newsroom->company_id, Model_Company::SOURCE_PR_CO);
		$this->generate_token($newsroom->company_id);
		
	}
}

?>
