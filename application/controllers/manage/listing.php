<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Listing_Base extends Manage_Base {
	
	const TYPE_SEARCH = 'search';
	
	protected $listing_section = null;
	protected $listing_sub_section = null;
	protected $listing_chunk_size = 10;

	public function all($chunk = 1)
	{
		$this->listing($chunk, 'all');
	}
	
	public function published($chunk = 1)
	{
		$filter = 'c.is_published = 1';
		$this->listing($chunk, 'published', null, $filter);
	}
	
	public function under_review($chunk = 1)
	{
		$filter = 'c.is_under_review = 1';
		$this->listing($chunk, 'under_review', null, $filter);
	}
	
	public function under_writing($chunk = 1)
	{
		$filter = 'c.is_under_writing = 1';
		$this->listing($chunk, 'under_writing', null, $filter);
	}
	
	public function draft($chunk = 1)
	{
		// exclude under_writing from 
		// draft list even though
		// they are a draft at this stage
		// - this is for user benefit only
		$filter = 'c.is_draft = 1
			AND c.is_published = 0
			AND c.is_under_writing = 0';
			
		$this->listing($chunk, 'draft', null, $filter);
	}
	
	public function scheduled($chunk = 1)
	{
		$filter = 'c.is_published = 0
			AND c.is_under_review = 0
			AND c.is_draft = 0';
			
		$this->listing($chunk, 'scheduled', null, $filter);
	}
	
	protected function process_results_under_writing($results)
	{
		$id_list = array();
		foreach ($results as $result)
		{
			if (!$result->is_under_writing) continue;
			if (!$result->writing_session_id) continue;
			$id_list[] = $result->writing_session_id;
		}
		
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$sql = "SELECT ws.*, wo.status, 
			wo.writer_id, wo.primary_keyword
			FROM nr_writing_session ws 
			LEFT JOIN rw_writing_order wo 
			ON ws.writing_order_id = wo.id
			WHERE ws.id IN ({$id_list_str})";
			
		$db_result = $this->db->query($sql);
		$w_sessions = Model_Writing_Session::from_db_all($db_result);
		
		$indexed_w_sessions = array();		
		foreach ($w_sessions as $w_session)
			$indexed_w_sessions[$w_session->id] = $w_session;
		
		foreach ($results as $result)
		{
			if (!$result->is_under_writing) continue;
			if (!$result->writing_session_id) continue;
			if (isset($indexed_w_sessions[$result->writing_session_id]))
				$result->writing_session = $indexed_w_sessions[$result->writing_session_id];
		}
		
		return $results;
	}
	
	protected function process_results($results)
	{
		$results = $this->process_results_under_writing($results);
		return $results;
	}
	
	protected function listing($chunk, $status, $type = null, $filter = 1, $sql = null)
	{
		$filter_all = 1;
		if ($type === null)
		{
			$type = $this->uri->segment(3);
			if (!Model_Content::is_allowed_type($type))
				show_404();
		}
		
		if ($type !== Model_Content::TYPE_PR
		    && !$this->newsroom->is_active)
		{
			// load feedback message for the user
			$feedback_view = 'manage/publish/partials/feedback/not_active_content_warning';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
		}
		
		if ($type === Model_Content::TYPE_PR)
		{
			$this->vd->pr_credits_premium = Auth::user()->pr_credits_premium();
			$this->vd->pr_credits_basic = Auth::user()->pr_credits_basic();
		}
		
		$company_id = $this->newsroom->company_id;
				
		$this->load->view('manage/header');		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size($this->listing_chunk_size);
		$limit_str = $chunkination->limit_str();
		$additional_tables = null;

		$scrape_src_select = null;
		$scrape_src_join = null;

		$nr_sources[] = Model_Company::SOURCE_PRWEB;
		$nr_sources[] = Model_Company::SOURCE_MARKETWIRED;

		if ($this->newsroom->source == Model_Company::SOURCE_OWLER && ($type == Model_Content::TYPE_NEWS 
			|| $type == Model_Content::TYPE_PR))
		{
			$scrape_src_select = ", o.actual_news_url";
			$scrape_src_join =  "LEFT JOIN nr_pb_owler_news o
								ON o.content_id = c.id";
		}
		elseif (in_array($this->newsroom->source, $nr_sources) && $type == Model_Content::TYPE_PR)
		{
			$scrape_src_select = ", o.url AS actual_news_url";
			$scrape_src_join =  "LEFT JOIN nr_pb_{$this->newsroom->source}_pr o
								ON o.content_id = c.id";
		}

		if ($sql === null)
		{
			// add a filter for the specified content type
			if (Model_Content::is_allowed_type($type))
			{
				$filter = "{$filter} AND c.type = '{$type}'";
				$filter_all = "{$filter_all} AND c.type = '{$type}'";				
			}

			// various writing order statuses that we should prioritize
			$status_sent_to_customer_for_detail_change = Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE;
			$status_sent_to_customer = Model_Writing_Order::STATUS_SENT_TO_CUSTOMER;
			
			// fetch id of valid content with a join 
			// on the writing session that must be connected
			// in order to view an is_under_writing
			$sql = "SELECT SQL_CALC_FOUND_ROWS c.*,
				ws.id AS writing_session_id,
				pc.is_pinned 
				{$scrape_src_select}
				FROM nr_content c 
				LEFT JOIN nr_pinned_content pc
				ON pc.content_id = c.id
				LEFT JOIN nr_writing_session ws
				ON ws.content_id = c.id 
				LEFT JOIN rw_writing_order wo
				ON wo.id = ws.writing_order_id
				{$scrape_src_join}
				WHERE c.company_id = ? AND {$filter} 
				AND (c.is_under_writing = 0
				  OR (ws.content_id IS NOT NULL 
				    AND ws.is_archived = 0)) 
				ORDER BY 
					IF ((ws.id IS NOT NULL AND (wo.status IS NULL
						OR wo.status = '{$status_sent_to_customer_for_detail_change}'
						OR wo.status = '{$status_sent_to_customer}')), 
						1, 0) DESC, 
					c.id DESC
				{$limit_str}";
		}
		
		$query = $this->db->query($sql, array($company_id));
		$results = Model_Content::from_db_all($query);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$section = $this->listing_section;
		if ($this->listing_sub_section)
		     $sub = build_url($this->listing_sub_section, null);
		else $sub = null;

		$url_format   = gstring("manage/{$section}/{$sub}{$type}/{$status}/-chunk-");
		$listing_view = "manage/{$section}/{$sub}{$type}";
		$chunkination->set_url_format($url_format);
		$chunkination->set_total($total_results);
		$this->vd->chunkination = $chunkination;
		
		$this->vd->results = $this->process_results($results);
		$this->load->view($listing_view);
		$this->load->view('manage/footer');
	}
	
}

?>