<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Feed_Common_Trait {

	protected $exclude_virtual_source;

	public function feed_list()
	{
		$offset = (int) $this->iella_in->offset;
		$sql = "SELECT c.* FROM nr_content c
			WHERE c.is_premium = 1 
			AND c.is_published = 1
			AND c.slug IS NOT NULL
			AND c.type = ?
			AND c.company_id > 0
			ORDER BY c.date_publish DESC
			LIMIT {$offset}, 500";

		$dbr = $this->db->query($sql, array(Model_Content::TYPE_PR));
		$results = Model_Content::from_db_all($dbr);
		$results = array_reverse($results);

		foreach ($results as $result)
		{
			// uuid is not used internally with newswire
			// but we can offer a suggestion for other sites
			$result->_uuid = $result->uuid();
			$result->_url = $this->website_url($result->url());
		}

		$this->iella_out->results = $results;
	}

	public function feed_view()
	{
		$content_id = $this->iella_in->content_id;
		$content = Model_Content::find($content_id);
		$this->iella_out->content = $content;
		$content->load_content_data();
		$content->load_local_data();
		$content->_tags = $content->get_tags();
		$content->_uuid = $content->uuid();
		$content->_url = $this->website_url($content->url());
		$content->_beats = $content->get_beats();
		$images = $content->get_images();
		$content->_images = array();

		foreach ($images as $image)
		{
			$filename = $image->variant('original')->filename;
			$sf = Stored_Image::from_stored_filename($filename);
			$content->_images[] = $this->website_url($sf->url());
		}

		if ($content->cover_image_id)
		{
			$image = Model_Image::find($content->cover_image_id);
			$filename = $image->variant('original')->filename;
			$sf = Stored_Image::from_stored_filename($filename);
			$content->_cover_image = $this->website_url($sf->url());
		}

		$builder = new Stats_URI_Builder();
		$builder->add_content_view($content->newsroom(), $content);
		$content->tracking_uri = $builder->build();

		$company = Model_Company::find($content->company_id);
		$company_profile = Model_Company_Profile::find($content->company_id);
		if (!$company_profile) $company_profile = new Raw_Data();
		$newsroom_custom = Model_Newsroom_Custom::find($content->company_id);
		if (!$newsroom_custom) $newsroom_custom = new Raw_Data();

		$this->iella_out->company = new stdClass();
		$this->iella_out->company->id = $company->id;
		$this->iella_out->company->name = $company->name;
		$this->iella_out->company->newsroom = $company->newsroom;
		$this->iella_out->company->soc_twitter = $company_profile->soc_twitter;
		$this->iella_out->company->soc_facebook = $company_profile->soc_facebook;
		$this->iella_out->company->soc_gplus = $company_profile->soc_gplus;
		$this->iella_out->company->soc_youtube = $company_profile->soc_youtube;
		$this->iella_out->company->soc_pinterest = $company_profile->soc_pinterest;
		$this->iella_out->company->soc_linkedin = $company_profile->soc_linkedin;

		if ($newsroom_custom->logo_image_id)
		{
			$image = Model_Image::find($newsroom_custom->logo_image_id);
			$filename = $image->variant('original')->filename;
			$sf = Stored_Image::from_stored_filename($filename);
			$this->iella_out->company->_logo_image = $this->website_url($sf->url());
		}

		$user = $content->owner();
		$this->iella_out->user = new stdClass();
		$this->iella_out->user->id = $user->id;
		$this->iella_out->user->first_name = $user->first_name;
		$this->iella_out->user->last_name = $user->last_name;
		$this->iella_out->user->email = $user->email;
	}

}

?>