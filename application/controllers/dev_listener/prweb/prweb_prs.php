<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class PRWeb_PRs_Controller extends Iella_Base {
	
	public function save()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_prweb_pr = Model_PB_PRWeb_PR::find('dev_site_content_id', $pr_rec->id))
			{
				if (! $m_content = Model_Content::find($m_prweb_pr->content_id))
					$m_content = new Model_Content();
				
				if (! $m_content_data = Model_Content_Data::find($m_prweb_pr->content_id))
					$m_content_data = new Model_Content_Data();

				if (! $m_pb_scraped_c = Model_PB_Scraped_Content::find($m_prweb_pr->content_id))
					$m_pb_scraped_c = new Model_PB_Scraped_Content();
			}
			else
			{
				$m_prweb_pr = new Model_PB_PRWeb_PR();
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
			$m_content->is_published = $pr_rec->is_published;
			$m_content->is_excluded_from_news_center = 1;
			$m_content->is_scraped_content = 1;
			$m_content->is_draft = 0;
			$m_content->is_legacy = 0;
			$m_content->is_under_review = 0;
			$m_content->is_approved = 1;
			$m_content->is_rejected = 0;
			$m_content->is_premium = 1;
			$m_content->is_credit_locked = $pr_rec->is_credit_locked;
			$m_content->is_under_writing = $pr_rec->is_under_writing;
			$m_content->save();

			$m_content_data->content_id = $m_content->id;
			$m_content_data->content = $pr_rec->content;
			$m_content_data->summary = $pr_rec->summary;
			$m_content_data->save();

			$m_prweb_pr->dev_site_content_id = $pr_rec->id;
			$m_prweb_pr->content_id = $m_content->id;
			$m_prweb_pr->url = $pr_rec->url;
			$m_prweb_pr->cat_id = $pr_rec->cat_id;
			$m_prweb_pr->web_video_provider = $pr_rec->web_video_provider;
			$m_prweb_pr->web_video_id = $pr_rec->web_video_id;
			$m_prweb_pr->cover_image_url = $pr_rec->cover_image_url;
			$m_prweb_pr->prweb_category_id = $pr_rec->prweb_category_id;
			$m_prweb_pr->prweb_company_id = $pr_rec->prweb_company_id;			
			$m_prweb_pr->save();

			$m_pb_scraped_c->content_id = $m_content->id;
			$m_pb_scraped_c->source = Model_PB_Scraped_Content::SOURCE_PRWEB;
			$m_pb_scraped_c->source_url = $pr_rec->url;
			$m_pb_scraped_c->save();


			if (!$m_pb_pr = Model_PB_PR::find($m_content->id))
			{
				$m_pb_pr = new Model_PB_PR();
				$m_pb_pr->content_id = $m_content->id;
			}

			$m_pb_pr->is_distribution_disabled = 1;
			$m_pb_pr->web_video_provider = $pr_rec->web_video_provider;
			$m_pb_pr->web_video_id = $pr_rec->web_video_id;
			$m_pb_pr->save();

			if ($m_prweb_cat = Model_PRWeb_Category::find($pr_rec->prweb_category_id))
				$m_content->set_beats(array($m_prweb_cat->newswire_beat_id));

			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}


	public function save_missing_content()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			$m_prweb_pr = Model_PB_PRWeb_PR::find($pr_rec->live_site_content_id);
			if (!$m_content_data = Model_Content_Data::find($m_prweb_pr->content_id))
			{
				$m_content_data = new Model_Content_Data();
				$m_content_data->content_id = $m_prweb_pr->content_id;
				$m_content_data->summary = $pr_rec->summary;
			}
			

			$m_content_data->content = $pr_rec->content;
			$m_content_data->save();

			$m_prweb_pr->dev_site_content_id = $pr_rec->id;
			$m_prweb_pr->save();

			$content_ids[] = $pr_rec->id;
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}


	public function update_missing_dates()
	{
		$prs_recs = $this->iella_in->prs_recs;

		$content_ids = array();

		foreach ($prs_recs as $pr_rec)
		{
			if ($m_prweb_pr = Model_PB_PRWeb_PR::find('dev_site_content_id', $pr_rec->id))
			{
				if ($m_content = Model_Content::find($m_prweb_pr->content_id))
				{
					$m_content->date_publish = $pr_rec->date_publish;
					$m_content->save();
					$content_ids[] = $pr_rec->id;
				}					
			}
		}
		
		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}

	public function update_pr_body()
	{
		$prweb_pr_recs = $this->iella_in->prweb_pr_recs;

		$content_ids = array();

		$sql = "SELECT cd.*, {{ p.* AS pb_prweb_pr USING Model_PB_PRWeb_PR }}
				FROM nr_pb_prweb_pr p
				INNER JOIN nr_content c
				ON p.content_id = c.id
				INNER JOIN nr_content_data cd
				ON cd.content_id = p.content_id
				LEFT JOIN nr_newsroom nr
				ON nr.company_id = c.company_id
				WHERE p.dev_site_content_id = ?
				AND nr.user_id = 1";

		foreach ($prweb_pr_recs as $prweb_pr_rec)
		{
			$content_ids[] = $prweb_pr_rec->content_id;
			$c_data = Model_Content_Data::from_sql($sql, array($prweb_pr_rec->content_id));

			if (!$c_data)
				continue;

			if (!empty($prweb_pr_rec->content))
				$c_data->content = value_or_null($prweb_pr_rec->content);

			$c_data->supporting_quote = value_or_null($prweb_pr_rec->supporting_quote);
			$c_data->save();

			$c_data->pb_prweb_pr->is_pr_body_refetched = 1;
			$c_data->pb_prweb_pr->save();
		}

		$this->iella_out->success = true;
		$this->iella_out->content_ids = $content_ids;
		$this->send();
	}
}

?>

