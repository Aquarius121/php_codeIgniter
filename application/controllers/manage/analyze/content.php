<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/listing');
load_controller('manage/analyze/stats_ui_base');

class Content_Controller extends Listing_Base {

	protected $listing_section = 'analyze';
	protected $listing_sub_section = 'content';
	protected $listing_chunk_size = 10;

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Analytics';
		$this->vd->title[] = 'Content Stats';
		$this->listing_type = $this->uri->segment(4);
	}
	
	protected function process_results($results)
	{
		if (!count($results))
			return $results;

		$hashes = array();
		foreach ($results as $result)
		{
			$stats_hash = new Stats_Hash();
			$stats_hash->content = $result->id;
			$hashes[] = $stats_hash->hash();
		}

		$contexts = Stats_Hash::__context_batch($hashes);
		$stats_query = new Stats_Query();
		$summation = $stats_query->hits_summation_batch($contexts);
		$summation = array_values($summation);
		foreach ($results as $k => $result)
			$result->hits = $summation[$k];
		
		return $results;
	}

	public function index($type = null, $status = null, $chunk = 1)
	{
		if ($type === null) $type = Model_Content::TYPE_PR;
		if ($status !== 'published')
			$this->redirect("manage/analyze/content/{$type}/published");
		if (!Model_Content::is_allowed_type($type)) show_404();
		
		$this->vd->is_search = false;
		$filter = 'c.is_published = 1';
		$this->listing($chunk, $status, $type, $filter);
	}
	
	public function view($id = null)
	{
		if (!$id) $this->redirect('manage/analyze/content');
		$company_id = (int) $this->newsroom->company_id;
		$m_content = Model_Content::find($id);
		$this->vd->m_content = $m_content;
		
		if (!$m_content) show_404();
		if ((int) $m_content->company_id !== $company_id)
			$this->denied();
		
		$this->title = $m_content->title;
		$dt_date_publish = Date::utc($m_content->date_publish);
		
		if ($m_content->is_premium && $m_content->is_published)
		{
			$this->vd->dist_count = Model_Distribution_Index::count(array(
				array('content_id', $m_content->id),
				array('date_discovered <= UTC_TIMESTAMP()')));
		}
		
		$stats_hash = new Stats_Hash();
		$stats_hash->action = Stats_URI_Builder::ACTION_IMPRESSION;
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$date_range = Stats_UI_Base::calc_date_range($this);
		$stats_query = new Stats_Query();
		$impressions = $stats_query->hits_over_period_summation($context, 
			$date_range[0], $date_range[1]);
		$this->vd->impressions = $impressions;
		$this->vd->hits_untracked = $this->untracked_views($m_content->id);

		$source_count = 4;
		if ($m_content->is_premium && $m_content->is_published)
			$source_count += 2;

		$this->vd->source_count = $source_count;

		// let the user know they are looking beyond the publish date
		if ($date_range[0] > $dt_date_publish)
		{
			$feedback = $this->load->view_return('manage/analyze/partials/date-publish');
			$this->use_feedback($feedback);
		}

		$this->dist_load_services($m_content->id);
		$this->sources_pie_chart($id, $this->vd->hits_untracked);

		$cbc_contacts = $this->bundled_campaign_contacts($id);
		$this->vd->cbc_contacts = $cbc_contacts;

		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->index('manage/analyze/content/view');
	}

	protected function untracked_views($id)
	{
		$accesswire = Model_Content_Accesswire::find($id);
		if (!$accesswire) return 0;
		return (int) $accesswire->views;
	}

	protected function bundled_campaign_contacts($content_id)
	{
		if (!$content_id) return;
		if (!$m_cbc = Model_Content_Bundled_Campaign::find($content_id)) return;
		if (!$campaign = Model_Campaign::find($m_cbc->campaign_id)) return;

		$campaign->load_content_data();
		$data_contacts = @unserialize($campaign->contacts);

		if (!is_array($data_contacts) || !count($data_contacts)) return;

		$in_data_contacts = sql_in_list($data_contacts);
		$sql = "SELECT c.id, c.first_name, c.last_name, 
				c.email, c.company_name, c.company_id
				FROM nr_contact c WHERE c.id IN ({$in_data_contacts})
				ORDER BY c.first_name ASC, c.last_name ASC";

		$query = $this->db->query($sql);
		$results = Model_Contact::from_db_all($query);
		return $results;
	}

	public function twitter_shares($id) 
	{
		$company_id = (int) $this->newsroom->company_id;
		$m_content = Model_Content::find($id);
		$this->vd->m_content = $m_content;
		
		if (!$m_content) show_404();
		if ((int) $m_content->company_id !== $company_id)
			$this->denied();

		$this->vd->twitter_shares = Social_Twitter_Shares::get($m_content);		
		$this->load->view('manage/analyze/partials/twitter-shares');
	}

	public function facebook_shares($id)
	{
		$company_id = (int) $this->newsroom->company_id;
		$m_content = Model_Content::find($id);
		$this->vd->m_content = $m_content;
		
		if (!$m_content) show_404();
		if ((int) $m_content->company_id !== $company_id)
			$this->denied();

		$this->vd->facebook_shares = Social_Facebook_Shares::get($m_content, $this->newsroom);
		$this->load->view('manage/analyze/partials/facebook-shares');
	}
	
	public function google_results($id)
	{	
		$company_id = (int) $this->newsroom->company_id;
		$m_content = Model_Content::find($id);
		$this->vd->m_content = $m_content;
		
		if (!$m_content) show_404();
		if ((int) $m_content->company_id !== $company_id)
			$this->denied();
			
		$this->load->view('manage/analyze/partials/google-results');
	}

	public function update_google_search_title()
	{
		$content_id = $this->input->post('content_id');
		$content_title = $this->input->post('content_title');

		$company_id = $this->newsroom->company_id;
		$m_content = Model_Content::find($content_id);
		if (!$m_content || $m_content->company_id 
			!= $company_id) die();

		$cgst = Model_Content_Google_Search_Title::find($content_id);
		if (!$cgst) $cgst = new Model_Content_Google_Search_Title();
		$cgst->content_id = $content_id;
		$cgst->title = $content_title;
		$cgst->save();
	}

	public function sources_pie_chart($id, $untracked)
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$view_context = $stats_hash->context();
		$stats_hash->action = Stats_URI_Builder::ACTION_VIEW_NETWORK;
		$network_context = $stats_hash->context();

		$date_range = Stats_UI_Base::calc_date_range($this);

		$stats_query = new Stats_Query();
		$hits_our_network = $stats_query->hits_summation($network_context);

		$stats_query = new Stats_Query();
		$hits_total = $stats_query->hits_summation($view_context);
		$hits_total += $untracked;

		if ($hits_total)
		{
			$network_fraction = $hits_our_network / $hits_total;
			$external_fraction = 1 - $network_fraction;
		}
		else
		{
			$network_fraction = 0;
			$external_fraction = 0;
		}

		$this->vd->network_fraction = $network_fraction;
		$this->vd->external_fraction = $external_fraction;
	}

	public function sources($id)
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$view_context = $stats_hash->context();
		$stats_hash->action = Stats_URI_Builder::ACTION_VIEW_NETWORK;
		$network_context = $stats_hash->context();

		$date_range = Stats_UI_Base::calc_date_range($this);

		$stats_query = new Stats_Query();
		$hits_our_network = $stats_query->hits_over_period_summation($network_context, 
			$date_range[0], $date_range[1]);

		$stats_query = new Stats_Query();
		$hits_total = $stats_query->hits_over_period_summation($view_context, 
			$date_range[0], $date_range[1]);

		$network_fraction = $hits_our_network / $hits_total;
		$external_fraction = 1 - $network_fraction;

		$chart = new Pie_Chart(array(
			array($network_fraction, array(19, 87, 168)),
			array($external_fraction, array(55, 55, 55)),
		), 100, 100);

		$chart->expires = 300;
		$chart->render();
	}
	
	public function geolocation($id) 
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->geolocation();
	}

	public function world_map($id, $width = '100%', $height = '300px')
	{
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->world_map(null, $width, $height);
	}

	public function us_states_map($id, $width = '100%', $height = '300px')
	{
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->us_states_map(null, $width, $height);
	}
	
	public function report($id, $is_printable = false)
	{
		$int_printable = value_if_test($is_printable, 1, 0);
		$generate_url = "manage/analyze/content/report_generate/{$id}/{$int_printable}";
		$generate_url = gstring($generate_url);
		$this->vd->generate_url = $generate_url;
		
		$return_url = "manage/analyze/content/view/{$id}";
		$return_url = gstring($return_url);
		$this->vd->return_url = $return_url;
		
		$this->load->view('manage/header');
		$this->load->view('manage/analyze/report-generate');
		$this->load->view('manage/footer');
	}
	
	public function report_generate($id, $is_printable = false, $is_branded = true)
	{
		// force printable mode for now
		// because presentation mode has 
		// an issue with wkhtmltopdf and 
		// really long pages
		$is_printable = true;

		$int_printable = value_if_test($is_printable, 1, 0);
		$int_branded = value_if_test($is_branded, 1, 0);
		$url = "manage/analyze/content/report_index/{$id}/{$int_printable}/{$int_branded}";
		$url = $this->newsroom->url($url);
		$url = gstring($url);

		if (!$is_printable)
		{
			$base_height_mil = 760;
			$services = $this->dist_load_services($id);
			$service_lines = ceil(count($services) / 2);
			$services_height = ceil(22 * $service_lines);

			$cbc_contacts = $this->bundled_campaign_contacts($id);
			$mo_lines = ceil(count($cbc_contacts) / 2);
			$mo_height = ceil(18.5 * $mo_lines);

			$presentation_height = $base_height_mil 
				+ $services_height + $mo_height;
			if ($presentation_height > 5000)
				$is_printable = true;
		}

		if ($is_printable)
		{
			$report = new PDF_Generator($url);
			$report->set_zoom_level(0.75);
			$report->generate();
		}
		else
		{
			$report = new PDF_Generator($url);
			$report->set_page_size(259, $presentation_height);
			$report->generate();
		}
		
		if ($this->input->post('indirect'))
			  $this->vd->download_url = $report->indirect();
		else $report->deliver();
		
		// indirect => load feedback (and download) message for the user
		$feedback_view = 'manage/partials/report-generated-feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
	}
	
	public function report_index($id, $is_printable = false, $is_branded = true)
	{		
		$this->vd->title = array();
		$company_id = (int) $this->newsroom->company_id;
		$m_content = Model_Content::find($id);
		$this->vd->m_content = $m_content;
		$this->vd->is_printable = (bool) $is_printable;
		$this->vd->is_branded = (bool) $is_branded;
		
		if (!$m_content) show_404();
		if ((int) $m_content->company_id !== $company_id)
			$this->denied();
		
		if ($m_content->is_premium && $m_content->is_published)
		{
			$this->vd->dist_count = Model_Distribution_Index::count(array(
				array('content_id', $m_content->id),
				array('date_discovered <= UTC_TIMESTAMP()')));
			$this->vd->google_results_count = Google_Search_Result_Count::
				count($m_content->title);
		}

		if (Auth::user()->is_reseller)
		{
			$m_reseller_details = Model_Reseller_Details::find(Auth::user()->id);
			$content_specific_logo = Model_Reseller_PR_Logo::find($m_content->id);
			if ($content_specific_logo) 
			     $logo_image_id = $content_specific_logo->image_id;
			else $logo_image_id = $m_reseller_details->logo_image_id;
			$this->vd->logo_image_id = $logo_image_id;
			
			$pre_header = 'reseller/report/pre_header';
			$pre_header = $this->load->view($pre_header, null, true);
			$this->vd->report_pre_header = $pre_header;
			$header_text = 'reseller/report/header_text';
			$header_text = $this->load->view($header_text, null, true);
			$this->vd->report_header_text = $header_text;
			$this->vd->report_skip_header = true;
			$this->vd->report_skip_footer = true;
		}

		if (!$is_branded)
		{
			$this->vd->report_pre_header = null;
			$this->vd->report_post_header = null;
			$this->vd->report_pre_footer = null;
			$this->vd->report_post_footer = null;
			$this->vd->report_header_text = null;
			$this->vd->report_skip_header = true;
			$this->vd->report_skip_footer = true;
		}

		foreach ($this->rdata as $k => $v)
			$this->vd->{$k} = $v;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->action = Stats_URI_Builder::ACTION_IMPRESSION;
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$date_range = Stats_UI_Base::calc_date_range($this);
		$stats_query = new Stats_Query();
		$impressions = $stats_query->hits_over_period_summation($context, 
			$date_range[0], $date_range[1]);
		$this->vd->impressions = $impressions;

		$this->dist_load_services($m_content->id);

		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);

		$cbc_contacts = $this->bundled_campaign_contacts($id);
		$this->vd->cbc_contacts = $cbc_contacts;

		$_base->index('manage/analyze/report/content');
	}
	
	protected function dist_load_services($content_id)
	{
		$sql = "SELECT ds.*, di.url as content_url 
			FROM nr_distribution_index di
			INNER JOIN nr_distribution_site ds ON di.content_id = ?
			AND di.date_discovered <= UTC_TIMESTAMP() AND
			di.distribution_site_id = ds.id
			ORDER BY ds.quality DESC, 
			ds.logo_image_id > 0 DESC";
			
		$db_result = $this->db->query($sql, array($content_id));
		$this->vd->services = Model_Distribution_Site::from_db_all($db_result);
		return $this->vd->services;
	}
	
	protected function dist_load_docsites($content_id)
	{
		$cd = Model_Content_DocSite::find($content_id);
		$this->vd->docs = $cd;
	}
	
	public function report_chart($id)
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->chart(940, 300);
	}

	public function report_hours($id)
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->hours(940, 180);
	}
	
	public function report_geolocation($id) 
	{		
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$view = 'manage/analyze/report/partials/geolocation';
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		return $_base->geolocation($view, 20, 100);
	}

	public function report_world_map($id)
	{
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$this->vd->disable_zoom = true;
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->world_map();
	}

	public function report_us_states_map($id)
	{
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;
		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;
		
		$this->vd->disable_zoom = true;
		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$_base = new Stats_UI_Base($this, $context);
		$_base->us_states_map();
	}
	
	public function search($status = null, $chunk = 1)
	{
		if ($status !== 'published') 
		{
			$url = 'manage/analyze/content/search/published';
			$this->redirect(gstring($url));
			return;
		}
		
		$type = static::TYPE_SEARCH;
		$this->vd->is_search = true;
		$terms = $this->input->get('terms');
		$terms_sql = sql_search_terms(array('c.title'), $terms);
		$filter = "{$terms_sql} AND c.is_published = 1";
		$this->listing($chunk, $status, $type, $filter);
	}
	
	public function views_feed_extended($id)
	{
		$m_content = Model_Content::find($id);
		$company_id = (int) $this->newsroom->company_id;

		if (!$m_content || (int) $m_content->company_id !== $company_id)
			return;

		$sources = array(
			(object) array('uri' => 'distribution/fin_content'),
			(object) array('uri' => 'distribution/world_now'),
			(object) array('uri' => 'distribution/digital_journal'),
			(object) array('uri' => 'distribution/digital_media_net'),
			(object) array('uri' => 'outreach/email'),
		);

		$stats_hash = new Stats_Hash();
		$stats_hash->content = $m_content->id;
		$context = $stats_hash->context();
		$stats_query = new Stats_Query();
		$total_views = $stats_query->hits_summation($context);

		$other = new stdClass();
		$other->uri = 'other/internal';
		$other->views = $total_views;

		foreach ($sources as $source)
		{
			$hash = new Stats_Hash();
			$hash->content = $m_content->id;
			$hash->source = $source->uri;
			$context = $hash->context();

			$stats_query = new Stats_Query();
			$views = $stats_query->hits_summation($context);
			$source->views = $views;
			$other->views -= $views;
		}
		
		$sources[] = $other;
		$this->vd->sources = $sources;
		$this->load->view('manage/analyze/partials/views-feed-extended');
	}

}

?>