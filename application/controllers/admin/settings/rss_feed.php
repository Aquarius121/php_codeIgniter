<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class RSS_Feed_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;

	public $title = 'RSS Feed';

	public function index($chunk = 1)
	{
		$feedback = new Feedback('alternative-2');
		$feedback->set_title('Attention!');
		$feedback->set_html('These RSS feeds are not intended for distribution.
			Please use <a href="https://www.newswire.com/distribution/generic">this
			feed</a> instead. ');
		$this->use_feedback($feedback);

		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/settings/rss_feed/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);

		// out of bounds so redirect to first
		if ($chunkination->is_out_of_bounds()) 
		{
			$url = 'admin/settings/rss_feed';
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}

	public function edit($id = null)
	{
		$rss = Model_RSS_Feed::find($id);
		if (!$rss) $rss = new Model_RSS_Feed();

		if ($this->input->post('save'))
		{
			$rss->name = $this->input->post('name');
			$rss->title = $this->input->post('title');
			if (!$rss->slug) $rss->title_to_slug();

			$rss->inews_link_text = $this->input->post('inews_link_text');
			$rss->footer_text = $this->vd->pure($this->input->post('footer_text'));
			$rss->is_spin_footer_text = $this->input->post('is_spin_footer_text');
			$rss->item_count = $this->input->post('item_count');
			$rss->is_full_text = $this->input->post('is_full_text');
			$rss->is_include_contact_info = $this->input->post('is_include_contact_info');
			$rss->max_num_of_chars = $this->input->post('max_num_of_chars');
			$rss->min_num_of_chars = $this->input->post('min_num_of_chars');

			$rss->is_show_inews_logo = $this->input->post('is_show_inews_logo');
			$rss->is_all_premium = $this->input->post('is_all_premium');
			$rss->is_show_publish_date = $this->input->post('is_show_publish_date');
			$rss->is_show_logo = $this->input->post('is_show_logo');
			$rss->is_show_related_images = $this->input->post('is_show_related_images');
			$rss->is_include_prs = $this->input->post('is_include_prs');
			$rss->is_include_news = $this->input->post('is_include_news');
			$rss->is_tracking_enabled = $this->input->post('is_tracking_enabled');
			$rss->is_enabled = $this->input->post('is_enabled');

			$rss->save();

			$feedback = new Feedback('success');
			$feedback->set_title('Success!');
			$feedback->set_text('Saved successfully.');
			$this->add_feedback($feedback);
			$this->redirect(gstring('admin/settings/rss_feed'));	
		}

		$this->vd->rss = $rss;
		$this->vd->markers = Model_RSS_Feed::markers();

		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/rss_feed-edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function delete($id)
	{
		if ($this->input->post('confirm'))
		{
			if (!($rss = Model_RSS_Feed::find($id))) return;
			$rss->delete();

			// load feedback message for the user
			$feedback_view = 'admin/settings/partials/rss_delete_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->redirect('admin/settings/rss_feed');
		}

		$this->vd->is_delete = true;
		if (!$this->input->post())
			$this->edit($id);
	}

	protected function fetch_results($chunkination)
	{
		$filter = 1;
		$limit_str = $chunkination->limit_str();		
		$this->vd->filters = array();	
		
		if ($filter_search = $this->input->get('filter_search'))
		{
			$this->create_filter_search($filter_search);
			$filter_fields = array('fs.name', 'fs.url');
			$filter = sql_search_terms($filter_fields, $filter_search);
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM nr_custom_rss_feed 
				WHERE {$filter} 
				ORDER BY name ASC 
				{$limit_str}";

		$query = $this->db->query($sql);
		$results = Model_RSS_Feed::from_db_all($query);
		$chunkination->set_total($this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count);

		return $results;
	}

	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$this->load->view('admin/header');
		$this->load->view('admin/settings/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/settings/rss_feed');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

}

?>