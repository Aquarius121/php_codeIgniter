<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Newswire_CA_PRs_Controller extends Iella_Base {
	
	public function save()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_newswire_ca_pr = Model_PB_Newswire_CA_PR::find('dev_site_content_id', $pr_rec->id))
			{
				if (! $m_content = Model_Content::find($m_newswire_ca_pr->content_id))
					$m_content = new Model_Content();
				
				if (! $m_content_data = Model_Content_Data::find($m_newswire_ca_pr->content_id))
					$m_content_data = new Model_Content_Data();

				if (! $m_pb_scraped_c = Model_PB_Scraped_Content::find($m_newswire_ca_pr->content_id))
					$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}
			else
			{
				$m_newswire_ca_pr = new Model_PB_Newswire_CA_PR();
				$m_content = new Model_Content();
				$m_content_data = new Model_Content_Data();
				$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}

			$m_content->company_id = 0;
			$m_content->type = Model_Content::TYPE_PR;
			$m_content->title = $pr_rec->title;
			$m_content->slug = $pr_rec->slug;
			$m_content->date_created = $pr_rec->date_created;
			$m_content->date_publish = $pr_rec->date_publish;
			$m_content->date_updated = $pr_rec->date_updated;
			$m_content->is_published = 1;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->is_scraped_content = 1;
			$m_content->is_draft = 0;
			$m_content->is_legacy = 0;
			$m_content->is_under_review = 0;
			$m_content->is_approved = 1;
			$m_content->is_rejected = 0;
			$m_content->is_premium = 1;
			$m_content->is_credit_locked = $pr_rec->is_credit_locked;
			$m_content->is_under_writing = 0;
			$m_content->save();

			$m_content_data->content_id = $m_content->id;
			$m_content_data->content = $pr_rec->content;
			$m_content_data->summary = $pr_rec->summary;
			$m_content_data->save();

			$m_newswire_ca_pr->dev_site_content_id = $pr_rec->id;
			$m_newswire_ca_pr->content_id = $m_content->id;
			$m_newswire_ca_pr->url = $pr_rec->url;
			$m_newswire_ca_pr->cat_id = $pr_rec->cat_id;
			$m_newswire_ca_pr->web_video_provider = $pr_rec->web_video_provider;
			$m_newswire_ca_pr->web_video_id = $pr_rec->web_video_id;
			$m_newswire_ca_pr->cover_image_url = $pr_rec->cover_image_url;
			$m_newswire_ca_pr->newswire_ca_category_id = $pr_rec->newswire_ca_category_id;
			$m_newswire_ca_pr->newswire_ca_company_id = $pr_rec->newswire_ca_company_id;			
			$m_newswire_ca_pr->save();

			$m_pb_scraped_c->content_id = $m_content->id;
			$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_NEWSWIRE_CA;
			$m_pb_scraped_c->source_url = $pr_rec->url;
			$m_pb_scraped_c->save();

			if (!$m_pb_pr = Model_PB_PR::find($m_content->id))
			{
				$m_pb_pr = new Model_PB_PR();
				$m_pb_pr->content_id = $m_content->id;
			}

			$m_pb_pr->is_distribution_disabled = 1;
			$m_pb_pr->save();

			if ($m_newswire_ca_cat = Model_Newswire_CA_Category::find($pr_rec->newswire_ca_category_id))
				$m_content->set_beats(array($m_newswire_ca_cat->newswire_beat_id));
		
			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}	
}

?>

