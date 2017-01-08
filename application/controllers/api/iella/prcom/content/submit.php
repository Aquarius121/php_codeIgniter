<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/shared/industry_outreach_mailer');
load_controller('api/iella/prcom/base');

class Submit_Controller extends PRCom_API_Base {
	
	use Industry_Outreach_Mailer_Trait;

	public function index()
	{
		$newswire_user = $this->iella_in->newswire_user;
		$newswire_company = $this->iella_in->newswire_company;
		$user = Model_User::find($newswire_user->id);
		$m_company = Model_Company::find($newswire_company->id);
		$options = Raw_Data::from_object($this->iella_in->options);

		if (!$user || !$m_company || $m_company->user_id != $user->id)
			throw new Exception('virtual user mismatch');

		$session_uuid = $this->iella_in->session_uuid;
		$session_data = Raw_Data::from_object($this->iella_in->session_data);
		$local_file_data = new Raw_Data();
		$local_file_data->images = array();

		if (!$options->outreach_beats_contact_limit)
			$options->outreach_beats_contact_limit = 0;
		if (!$options->outreach_regions_contact_limit)
			$options->outreach_regions_contact_limit = 0;

		if ($options->has_media)
		{
			if ($session_data->stored_file_1)
			{
				$sf = Stored_File::from_file();
				$local_file_data->stored_file_1_id
					= $sf->save_to_db();
			}

			if ($session_data->stored_file_2)
			{
				$sf = Stored_File::from_file();
				$local_file_data->stored_file_2_id
					= $sf->save_to_db();
			}

			// remove the cover image from the set of images
			if ($session_data->cover_image && is_array($session_data->images) && 
				(false !== ($idx = array_search($session_data->cover_image, $session_data->images))))
				array_splice($session_data->images, $idx, 1);

			// import cover image
			if ($session_data->cover_image && 
				isset($this->iella_files[$session_data->cover_image]) &&
				Image::is_valid_file($this->iella_files[$session_data->cover_image]))
			{
				$m_image = Quick_Image::import('cover', $this->iella_files[$session_data->cover_image]);
				$local_file_data->cover_image_id = $m_image->id;
			}

			// import related images
			foreach ((array) $session_data->images as $iella_file)
			{
				if (isset($this->iella_files[$iella_file]) &&
					Image::is_valid_file($this->iella_files[$iella_file]))
				{
					$m_image = Quick_Image::import('related', $this->iella_files[$iella_file]);
					$local_file_data->images[] = $m_image->id;
				}
			}
		}

		if ($m_content = Model_Content::find_uuid($session_uuid)) 
		     $is_new_content = false;
		else $is_new_content = true;
		
		if ($is_new_content)
			$m_content = new Model_Content();
		$m_content->uuid = $session_uuid;
		$m_content->type = Model_Content::TYPE_PR;
		if ($is_new_content)
			$m_content->date_created = Date::$now;
		$m_content->date_publish = $session_data->date_publish;
		$m_content->company_id = $m_company->id;
		$m_content->title = $session_data->title;
		if (!$m_content->is_published)
			$m_content->title_to_slug();
		$m_content->is_premium = 1;
		$m_content->is_credit_locked = (int) $options->convert;
		$m_content->is_draft = (int) $options->is_draft;
		$m_content->cover_image_id = $local_file_data->cover_image_id;
		$m_content->save();

		$m_content->set_images((array) $local_file_data->images);
		$m_content->set_tags(comma_explode($session_data->tags));
		$m_content->set_beats((array) $session_data->outreach_beats);

		if ($is_new_content)
		{
			$m_cvs = new Model_Content_Virtual_Source();
			$m_cvs->content_id = $m_content->id;
			$m_cvs->virtual_source_id = Model_Virtual_Source::ID_PRESSRELEASECOM;
			$m_cvs->remote_uuid = $session_uuid;
			$m_cvs->save();
		}

		if ($is_new_content)
		     $m_content_data = new Model_Content_Data();
		else $m_content_data = Model_Content_Data::find($m_content->id);

		$m_content_data->content_id = $m_content->id;
		$m_content_data->content = $this->vd->pure($session_data->content);
		$m_content_data->summary = $session_data->summary;
		$m_content_data->supporting_quote = $session_data->supporting_quote;
		$m_content_data->supporting_quote_name = $session_data->supporting_quote_name;
		$m_content_data->supporting_quote_title = $session_data->supporting_quote_title;
		$m_content_data->rel_res_pri_link = $session_data->rel_res_pri_link;
		$m_content_data->rel_res_pri_title = $session_data->rel_res_pri_title;
		$m_content_data->rel_res_sec_link = $session_data->rel_res_sec_link;
		$m_content_data->rel_res_sec_title = $session_data->rel_res_sec_title;
		$m_content_data->save();

		if ($is_new_content)
		     $m_pb_pr = new Model_PB_PR();
		else $m_pb_pr = Model_PB_PR::find($m_content->id);
		$m_pb_pr->content_id = $m_content->id;
		$m_pb_pr->location = $session_data->location;
		$m_pb_pr->web_video_provider = Video::PROVIDER_YOUTUBE;
		$m_pb_pr->web_video_id = (new Video_Youtube())->parse_video_id($session_data->web_video_url);
		$m_pb_pr->save();

		$m_content->load_local_data();
		$m_content->load_content_data();

		if ($options->convert && $options->has_prnewswire)
		{	
			if (!Model_Content_Release_Plus::find_content_with_provider($m_content->id, 
					Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE))
			{
				$m_rp = new Model_Content_Release_Plus();
				$m_rp->provider = Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE;
				$m_rp->is_confirmed = 1;
				$m_rp->content_id = $m_content->id;
				$m_rp->save();
			}
		}

		if ($options->convert && $options->has_accesswire)
		{
			if (!Model_Content_Release_Plus::find_content_with_provider($m_content->id, 
					Model_Content_Release_Plus::PROVIDER_ACCESSWIRE))
			{
				$m_rp = new Model_Content_Release_Plus();
				$m_rp->provider = Model_Content_Release_Plus::PROVIDER_ACCESSWIRE;
				$m_rp->is_confirmed = 1;
				$m_rp->content_id = $m_content->id;
				$m_rp->save();
			}
		}

		if ($options->convert && !$m_content->is_published)
		{
			$mo_factory = new Bundled_Media_Outreach_Factory($m_content);
			$mo_factory->set_beats($session_data->outreach_beats);
			$mo_factory->set_regions($session_data->outreach_regions);
			$mo_factory->set_contact_limit_beats($options->outreach_beats_contact_limit);
			$mo_factory->set_contact_limit_regions($options->outreach_regions_contact_limit);
			$m_cbc = $mo_factory->create();

			if ($m_cbc)
			{
				$m_campaign = $mo_factory->get_campaign();
				$m_campaign->sender_email = $this->conf('outreach_email');
				$m_campaign->save();
			}
		}

		$this->iella_out->content_id = $m_content->id;
		$this->iella_out->content_url = $this->website_url($m_content->url());

		$builder = new Stats_URI_Builder();
		$builder->add_content_view($m_content->newsroom(), $m_content);
		$this->iella_out->tracking_uri = $builder->build();
	}

}

?>