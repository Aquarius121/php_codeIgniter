<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class MyNewsDesk_PRs_Controller extends Iella_Base {
	
	public function save()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find('dev_site_content_id', $pr_rec->id))
			{
				if (! $m_content = Model_Content::find($m_mynewsdesk_pr->content_id))
					$m_content = new Model_Content();
				
				if (! $m_content_data = Model_Content_Data::find($m_mynewsdesk_pr->content_id))
					$m_content_data = new Model_Content_Data();

				if (! $m_pb_pr = Model_PB_PR::find($m_mynewsdesk_pr->content_id))
					$m_pb_pr = new Model_PB_PR();

				if (! $m_pb_scraped_c = Model_PB_Scraped_Content::find($m_mynewsdesk_pr->content_id))
					$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}
			else
			{
				$m_mynewsdesk_pr = new Model_PB_MyNewsDesk_Content();
				$m_content = new Model_Content();
				$m_content_data = new Model_Content_Data();
				$m_pb_pr = new Model_PB_PR();
				$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}

			$m_content->company_id = 0;
			$m_content->type = Model_Content::TYPE_PR;
			$m_content->title = $pr_rec->title;
			$m_content->title_to_slug();
			$m_content->date_created = $pr_rec->date_created;
			$m_content->date_publish = $pr_rec->date_publish;
			$m_content->date_updated = $pr_rec->date_updated;
			$m_content->is_published = $pr_rec->is_published;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->is_scraped_content = 1;
			$m_content->is_draft = $pr_rec->is_draft;
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
			$m_pb_pr->save();

			$m_pb_scraped_c->content_id = $m_content->id;
			$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_MYNEWSDESK;
			$m_pb_scraped_c->source_url = $pr_rec->url;
			$m_pb_scraped_c->save();

			$m_mynewsdesk_pr->dev_site_content_id = $pr_rec->id;
			$m_mynewsdesk_pr->content_id = $m_content->id;
			$m_mynewsdesk_pr->url = $pr_rec->url;
			$m_mynewsdesk_pr->cat_id = $pr_rec->cat_id;
			$m_mynewsdesk_pr->country = $pr_rec->country;
			$m_mynewsdesk_pr->company_name = $pr_rec->company_name;
			$m_mynewsdesk_pr->time_string = $pr_rec->time_string;
			$m_mynewsdesk_pr->web_video_provider = $pr_rec->web_video_provider;
			$m_mynewsdesk_pr->web_video_id = $pr_rec->web_video_id;
			$m_mynewsdesk_pr->cover_image_url = $pr_rec->cover_image_url;
			$m_mynewsdesk_pr->mynewsdesk_category_id = $pr_rec->mynewsdesk_category_id;
			$m_mynewsdesk_pr->mynewsdesk_company_id = $pr_rec->mynewsdesk_company_id;
			$m_mynewsdesk_pr->language = $pr_rec->language;
			$m_mynewsdesk_pr->save();

			if ($mynewsdesk_cat = Model_PR_Co_Category::find($pr_rec->mynewsdesk_category_id))
				$m_content->set_beats(array($mynewsdesk_cat->newswire_beat_id));
		
			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}


	public function save_lang()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_mynewsdesk_pr = Model_PB_MyNewsDesk_Content::find('dev_site_content_id', $pr_rec->content_id))
			{
				$m_mynewsdesk_pr->language = $pr_rec->language;
				$m_mynewsdesk_pr->save();
				$content_ids[] = $pr_rec->content_id;
			}
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}	
}

?>
