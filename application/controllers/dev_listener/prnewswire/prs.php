<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class PRs_Controller extends Iella_Base {
	
	public function save()
	{
		$prs = $this->iella_in->prs;
		
		$content_ids = array();
		$nw_prn_pr_ids = array();

		foreach ($prs as $pr_rec)
		{
			if ($m_c = Model_Content::find('title', $pr_rec->title))
			{
				$m_bundle = $m_c->distribution_bundle();
				if ($m_bundle && $m_bundle->has_provider(Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE))
				{
					$nw_prn_pr_ids[] = $pr_rec->id;
					$content_ids[] = $pr_rec->id;
					continue;
				}
			}

			if ($m_prn_pr = Model_PB_PRN_PR::find('dev_site_content_id', $pr_rec->id))
			{
				if (! $m_content = Model_Content::find($m_prn_pr->content_id))
					$m_content = new Model_Content();

				if (! $m_content_data = Model_Content_Data::find($m_prn_pr->content_id))
					$m_content_data = new Model_Content_Data();

				if (! $m_pb_pr = Model_PB_PR::find($m_prn_pr->content_id))
					$m_pb_pr = new Model_PB_PR();

				if (! $m_pb_scraped_c = Model_PB_Scraped_Content::find($m_prn_pr->content_id))
					$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}
			else
			{
				$m_prn_pr = new Model_PB_PRN_PR();
				$m_content = new Model_Content();
				$m_content_data = new Model_Content_Data();
				$m_pb_pr = new Model_PB_PR();
				$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}

			$company_id = $this->get_company_id($pr_rec);

			$m_content->company_id = $company_id;
			$m_content->type = Model_Content::TYPE_PR;
			$m_content->title = $pr_rec->title;
			$m_content->title_to_slug();
			$m_content->date_created = $pr_rec->date_created;
			$m_content->date_publish = $pr_rec->date_publish;
			$m_content->date_updated = $pr_rec->date_updated;

			$m_content->is_published = $pr_rec->is_published;
			$m_content->is_draft = $pr_rec->is_draft;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->is_scraped_content = 1;
			$m_content->is_legacy = $pr_rec->is_legacy;
			$m_content->is_under_review = $pr_rec->is_under_review;
			$m_content->is_approved = $pr_rec->is_approved;
			$m_content->is_rejected = $pr_rec->is_rejected;
			$m_content->is_premium = $pr_rec->is_premium;
			$m_content->is_credit_locked = $pr_rec->is_credit_locked;
			$m_content->is_under_writing = $pr_rec->is_under_writing;
			$m_content->save();

			$m_content_data->content_id = $m_content->id;
			$m_content_data->content = $pr_rec->content;
			$m_content_data->summary = $pr_rec->summary;
			$m_content_data->save();

			$m_pb_pr->content_id = $m_content->id;
			$m_pb_pr->is_distribution_disabled = 1;
			$m_pb_pr->cat_1_id = $pr_rec->cat_1_id;
			$m_pb_pr->is_external = 1;
			$m_pb_pr->source_url = $pr_rec->url;
			$m_pb_pr->save();

			$m_pb_scraped_c->content_id = $m_content->id;
			$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_PRNEWSWIRE;
			$m_pb_scraped_c->source_url = $pr_rec->url;
			$m_pb_scraped_c->save();

			$m_prn_pr->content_id = $m_content->id;
			$m_prn_pr->dev_site_content_id = $pr_rec->id;
			$m_prn_pr->prn_company_id = $pr_rec->prn_pr->prn_company_id;
			$m_prn_pr->raw_data = $pr_rec->prn_pr->raw_data;
			$m_prn_pr->save();

			$beats = $pr_rec->beats;
			if (is_array($beats) && count($beats))
				$m_content->set_beats($beats);

			$this->save_cover_image($m_content, $pr_rec);
			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->iella_out->nw_prn_pr_ids = $nw_prn_pr_ids;
		$this->send();
	}
	
	protected function save_cover_image($m_content, $pr_rec)
	{
		if (empty($pr_rec->cover_image_filename))
			return;

		$cover_image_filename = $pr_rec->cover_image_filename;

		$files = $this->iella_files;
		if ($img_file = $files[$cover_image_filename])
		{
			if (Image::is_valid_file($img_file))
			{
				$m_image = Quick_Image::import('cover', $img_file);
				$m_content->cover_image_id = $m_image->id;
				$m_content->save();
			}
		}
	}

	protected function get_company_id($pr_rec)
	{
		if (!$pr_rec->prn_comp->id)
			return 0;

		$prn_company_id = $pr_rec->prn_comp->id;

		if (!$prn_comp = Model_PRN_Company::find($prn_company_id))
		{
			$prn_comp = new Model_PRN_Company();
			$prn_comp->id = $pr_rec->prn_comp->id;
			$prn_comp->name = $pr_rec->prn_comp->name;
			$prn_comp->prn_url = $pr_rec->prn_comp->prn_url;
			$prn_comp->date_last_pr_submitted = $pr_rec->prn_comp->date_last_pr_submitted;
			$prn_comp->save();
		}

		if ($prn_comp->company_id)
			return $prn_comp->company_id;

		$newsroom = Model_Newsroom::create(1, $pr_rec->prn_comp->name);
		$newsroom->source = Model_Company::SOURCE_PRNEWSWIRE;
		$newsroom->save();

		$nr_custom = new Model_Newsroom_Custom();
		$nr_custom->company_id = $newsroom->company_id;
		
		// fetching and setting the logo
		if (isset($pr_rec->prn_comp->logo_filename))
		{
			$files = $this->iella_files;
			if ($img_file = $files[$pr_rec->prn_comp->logo_filename])
			{
				if (Image::is_valid_file($img_file))
				{
					$m_image = Quick_Image::import('logo', $img_file);
					$m_image->company_id = $newsroom->company_id;
					$m_image->save();

					$nr_custom->logo_image_id = $m_image->id;
				}
			}
		}

		$nr_custom->use_white_header = 1;	
		$nr_custom->save();

		$prn_comp->company_id = $newsroom->company_id;
		$prn_comp->save();

		return $newsroom->company_id;
	}
}