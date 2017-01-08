<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SiteMAP_Controller extends CIL_Controller {

	const BLOCK_SIZE = 10000;

	public function __on_execution_start()
	{
		$this->output->set_content_type('application/xml');
		$this->cache_duration = 7200;
	}

	public function index()
	{
		$sql = "SELECT count(*) as count 
			from nr_newsroom n inner join nr_content c 
			on n.company_id = c.company_id
			where n.is_active = 1
			and c.is_published = 1
			and c.slug is not null
			group by n.company_id";
		$this->vd->newsroom_count = $this->db
			->query($sql)->row()->count;

		$sql = "SELECT count(*) as count 
			from nr_content where is_published = 1
			and slug is not null";
		$this->vd->content_count = $this->db
			->query($sql)->row()->count;

		$this->vd->block_size = static::BLOCK_SIZE;
		$this->load->view('sitemap/index');
	}

	public function pages()
	{
		$urls = array(
			array($this->website_url('pricing'), 'monthly'),
			array($this->website_url('pricing/m12'), 'monthly'),
			array($this->website_url('pricing/m24'), 'monthly'),
			array($this->website_url('membership-plan'), 'monthly'),
			array($this->website_url('membership-plan/12'), 'monthly'),
			array($this->website_url('membership-plan/24'), 'monthly'),
			array($this->website_url('pricing-page'), 'monthly'),
			array($this->website_url('single-prn'), 'monthly'),
			array($this->website_url('single-pr'), 'monthly'),
			array($this->website_url('login'), 'monthly'),
			array($this->website_url('register'), 'monthly'),
			array($this->website_url('features/distribution'), 'monthly'),
			array($this->website_url('features/writing'), 'monthly'),
			array($this->website_url('features/pitching'), 'monthly'),
			array($this->website_url('features/newsrooms'), 'monthly'),
			array($this->website_url('features/social'), 'monthly'),
			array($this->website_url('features/analytics'), 'monthly'),
			array($this->website_url('how-it-works'), 'monthly'),
			array($this->website_url('helpdesk'), 'monthly'),
			array($this->website_url('newsroom'), 'hourly'),
			array($this->website_url('about'), 'monthly'),
			array($this->website_url('why-us'), 'monthly'),
			array($this->website_url('affiliate-program'), 'monthly'),
			array($this->website_url('reseller-program'), 'monthly'),
			array($this->website_url('feeds'), 'monthly'),
			array($this->website_url('terms-of-service'), 'monthly'),
			array($this->website_url('privacy-policy'), 'monthly'),
			array($this->website_url('content-guidelines'), 'monthly'),
			array($this->website_url('editorial-process'), 'monthly'),
		);

		$this->render_basic($urls);
	}

	public function news_center()
	{
		$beats = Model_Beat::find_all();
		$urls = array();

		foreach ($beats as $beat)
		{
			$loc = sprintf('newsroom/%s', $beat->slug);
			$loc = $this->website_url($loc);
			$urls[] = array($loc, 'hourly');
		}

		$this->render_basic($urls);
	}

	public function content_block($chunk)
	{
		$chunk = (int) $chunk;
		$chi = new Chunkination($chunk);
		$chi->set_chunk_size(static::BLOCK_SIZE);
		$limit_str = $chi->limit_str();
		$urls = array();

		$sql = "SELECT c.slug, c.type, c.is_legacy, 
			n.name as newsroom__name, 
			n.is_active as newsroom__is_active,
			greatest(c.date_publish, c.date_updated) as date_modified
			from nr_content c inner join nr_newsroom n
			on c.company_id = n.company_id 
			where c.is_published = 1
			and c.slug is not null
			{$limit_str}";

		$dbr = $this->db->query($sql);
		$content_arr = Model_Content::from_db_all($dbr, array(
			'newsroom' => 'Model_Newsroom'
		));

		$max_last_modified = Date::days(-3);

		foreach ($content_arr as $content)
		{
			if ($content->newsroom->is_active)
			     $loc = $content->newsroom->url($content->url());
			else $loc = $this->website_url($content->url());
			$lastmod = Date::utc($content->date_modified);
			if ($lastmod >= $max_last_modified)
			     $changefreq = 'daily';
			else $changefreq = 'yearly';
			$urls[] = array($loc, $changefreq, $lastmod);
		}
		
		$this->render_basic($urls);
	}

	public function newsroom_block($chunk)
	{
		$chunk = (int) $chunk;
		$chi = new Chunkination($chunk);
		$chi->set_chunk_size(static::BLOCK_SIZE);
		$limit_str = $chi->limit_str();
		$urls = array();

		$sql = "SELECT n.*, max(c.date_publish) as date_modified
			from nr_newsroom n inner join nr_content c
			on c.company_id = n.company_id 
			where n.is_active = 1
			and c.is_published = 1
			group by n.company_id
			{$limit_str}";

		$dbr = $this->db->query($sql);
		$newsroom_arr = Model_Newsroom::from_db_all($dbr);
		$max_last_modified = Date::days(-30);

		foreach ($newsroom_arr as $newsroom)
		{
			$loc = $newsroom->url();
			$lastmod = Date::utc($newsroom->date_modified);
			if ($lastmod >= $max_last_modified)
			     $changefreq = 'daily';
			else $changefreq = 'monthly';
			$urls[] = array($loc, $changefreq, $lastmod);
		}
		
		$this->render_basic($urls);
	}

	protected function render_basic($urls)
	{
		$this->vd->urls = $urls;
		$this->load->view('sitemap/basic');
	}
	
}

?>