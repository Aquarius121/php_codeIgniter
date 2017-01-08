<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/news-center/base');

class Main_Controller extends News_Center_Base {

	protected $basic_types = array(Model_Content::TYPE_PR);
	protected $query_cache_ttl = 900;
	protected $limit = 24;

	public function __construct()
	{
		parent::__construct();
		$this->vd->title[] = 'Newsroom';
	}
	
	public function _remap($method, $params = array())
	{
		$chunk = 1;
		$url = $this->uri->uri_string;
		$this->vd->news_center_params = $params;

		if (count($params) > 1)
		{
			$last_b1 = $params[count($params) - 2];
			$last_b0 = $params[count($params) - 1];
			
			if ($last_b1 === 'page' && is_numeric($last_b0))
			{
				$url_params = array_slice($this->uri->segments, 0, -2);
				$this->vd->news_center_params = $params = array_slice($url_params, 1);
				$url = implode('/', $url_params);
				if ($last_b0 == 1) $this->redirect(gstring($url));
				$chunk = $last_b0;
			}
		}

		# viewed externally as newsroom instead of news-center
		$url = preg_replace('#^news-center#', 'newsroom', $url);

		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size($this->limit);
		$url_format = gstring("{$url}/page/-chunk-");
		$chunkination->set_url_format($url_format);
		$this->chunkination = $chunkination;
		$this->vd->chunkination = $chunkination;
		$this->offset = $chunkination->offset();

		// cache pages (more recent => expires sooner)
		$this->cache_duration = $chunk * 300;
		
		if ($method === null)
			$method = array_shift($params);
		return parent::_remap($method, $params);
	}
	
	public function cats()
	{
		$this->redirect_301('newsroom/beats');
	}

	public function beats()
	{
		$this->vd->title[] = 'Categories';
		$groups = Model_Beat::list_all_beats_by_group();
		$this->vd->beat_groups = $groups;
		$lines_count = 0;

		foreach ($groups as $group)
			if ($group->is_listed) 
				foreach ($group->beats as $beat)
					if ($beat->is_listed) $lines_count++;
			
		$this->vd->lines_count = $lines_count;
		$this->load->view('website/header');
		$this->load->view('website/news-center/beats');
		$this->load->view('website/footer');
	}
	
	public function all()
	{
		$this->basic();
	}
	
	public function rss()
	{
		$this->limit = $this->rss_limit;
		$this->rss_enabled = true;
		$this->basic();
	}
	
	public function custom($name)
	{
		if ($name === 'all-press-releases')
			return $this->basic(array(Model_Content::TYPE_PR));
		if ($name === 'premium-press-releases')
			return $this->basic(array(Model_Content::TYPE_PR),
				'c.is_premium = 1');
		show_404();
	}
	
	public function front()
	{
		$this->limit = 20;
		$this->basic(array(Model_Content::TYPE_PR),
			'c.is_premium = 1');
	}
	
	public function front_cached()
	{
		$cached = Data_Cache_ST::read('newsroom/front');
		$res_data = json_decode($cached);

		if (empty($res_data->data))
		{
			$request = new HTTP_Request();
			$request->url = $this->website_url('newsroom/front');
			if ($this->is_development())
				$request->disable_ssl_verification();
			$request->data['partial'] = 1;
			$response = $request->get();
			$res_data = json_decode($response->data);
			Data_Cache_ST::write('newsroom/front', 
				$response->data, 900);
		}
		
		$this->json($res_data);
	}
		
	public function index($filter = null)
	{
		// filter can be null, type or category
		if ($filter === null) return $this->basic();
		if (Model_Content::is_allowed_type($filter))
			return $this->basic(array($filter));
		$this->beat($filter);
	}
	
	protected function render_list_view($results)
	{
		$this->vd->results = $results;

		if ($this->chunkination->is_out_of_bounds()) 
		{
			// redirect to the first chunk as out of bounds
			$params = array_slice($this->uri->segments, 0, -2);
			$url = gstring(implode('/', $params));
			$this->redirect($url);
		}

		if ($this->rss_enabled)
		{
			$this->output->set_content_type('application/rss+xml');
			return $this->load->view('browse/rss');
		}

		$impressions_uri = new Stats_URI_Builder();
		foreach ($results as $result)
			$impressions_uri->add_content_impression($result);
		$impressions_uri_str = $impressions_uri->build();
		$this->vd->impressions_uri = $impressions_uri_str;
		
		if ($this->input->get('partial')) 
		{
			if (!count($results)) return $this->json(false);
			$content = $this->load->view('website/news-center/partial-listing', null, true);			
			return $this->json(array('data' => $content, 'pixel' => $impressions_uri_str));
		}
		
		$this->load->view('website/header');
		$this->load->view('website/news-center/listing');
		$this->load->view('website/footer');
	}
	
}
