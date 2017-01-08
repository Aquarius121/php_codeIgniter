<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Owler_News_Controller extends Iella_Base {
	
	public function save()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_owler_pr = Model_PB_Owler_News::find('dev_site_content_id', $pr_rec->id))
			{
				if (! $m_content = Model_Content::find($m_owler_pr->content_id))
					$m_content = new Model_Content();
				
				if (! $m_content_data = Model_Content_Data::find($m_owler_pr->content_id))
					$m_content_data = new Model_Content_Data();
			}
			else
			{
				$m_owler_pr = new Model_PB_Owler_News();
				$m_content = new Model_Content();
				$m_content_data = new Model_Content_Data();
			}

			$m_content->company_id = 0;
			$m_content->type = Model_Content::TYPE_NEWS;
			$m_content->title = $pr_rec->title;
			$m_content->slug = null;
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

			$m_owler_pr->dev_site_content_id = $pr_rec->id;
			$m_owler_pr->content_id = $m_content->id;
			$m_owler_pr->url = $pr_rec->url;
			$m_owler_pr->actual_news_url = $pr_rec->actual_news_url;
			$m_owler_pr->date_from_owler = $pr_rec->date_from_owler;
			$m_owler_pr->news_image_path = $pr_rec->news_image_path
			$m_owler_pr->cat_id = $pr_rec->cat_id;
			$m_owler_pr->web_video_provider = $pr_rec->web_video_provider;
			$m_owler_pr->web_video_id = $pr_rec->web_video_id;
			$m_owler_pr->cover_image_url = $pr_rec->cover_image_url;
			$m_owler_pr->owler_category_id = $pr_rec->owler_category_id;
			$m_owler_pr->owler_company_id = $pr_rec->owler_company_id;			
			$m_owler_pr->save();
		
			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}
}

?>

