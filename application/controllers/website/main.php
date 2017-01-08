<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

//	* this controller can be used on any host 
// despite the Website_Base parent class

// * the controller will decide whether
// to invoke the host-check logic depending
// on the url pattern that it matches

class Main_Controller extends Website_Base {
	
	const PAGES_BASE_DIR = 'website/pages';

	// initially we don't do anything because 
	// we need to check for regex url matches
	protected function __construct_website_base()
	{
		return;
	}
	
	public function index()
	{
		if (!count($this->params))
			return $this->home();

		// attempt to find a matching page
		if ($this->find_page($this->params))
			return;

		// looks like a possible content slug
		// but missing some parts such as the
		// content type (v2) or PR id (v1)
		$slug = $this->uri->uri_string;
		$pattern = '#^[a-z0-9\-]+$#i';
		if (preg_match($pattern, $slug))
		{
			// find version 2 content (exact slug match)
			if ($content = Model_Content::find_slug($slug))
			{
				$url = $this->website_url($content->url());
				$this->redirect_301($url, false);
				return;
			}

			$like_criteria = array();
			$like_criteria[] = array('is_legacy', 1);
			$like_criteria[] = array('slug', 'like', sprintf('%s/%%', $slug));

			// find version 1 content with any PR id
			if ($content = Model_Content::find($like_criteria))
			{
				$url = $this->website_url($content->url());
				$this->redirect_301($url, false);
				return;
			}
		}
		
		// looks like legacy content
		// with the slug and PR id
		$slug = $this->uri->uri_string;
		$pattern = '#^[a-z0-9\-]+/[a\-]?[0-9]+$#i';
		if (preg_match($pattern, $slug))
		{
			if ($content = Model_Content::find_slug($slug))
			{
				$internal_url = $content->internal_url();
				$this->_make_internal_redirect($internal_url);
				return;
			}

			if ($deleted = Model_Content_Deleted_301::find($slug))
			{
				$this->redirect_301(null);
				return;
			}
		}

		// fallback to v1
		show_404();
	}
	
	protected function find_page($params)
	{
		// load a view from PAGES_BASE_DIR
		// that matches the requested url - the filename
		// must have underscore in place of every 
		// non-alphanumeric character
		
		// normalize request into filename
		$name = implode('_', $params);
		$name = preg_replace('#[^a-z0-9_]#is', '_', $name);
		
		// construct the path
		$application_dir = APPPATH;
		$pages_dir = static::PAGES_BASE_DIR;
		$page_file = "{$pages_dir}/{$name}.php";
		
		// found a page that matches the requested url
		if (is_file("{$application_dir}/views/{$page_file}"))
		{
			parent::__construct_website_base();
			$canonical_name = preg_replace('#[^a-z0-9]#is', '/', $name);
			$this->vd->canonical_url = $canonical_name;
			$this->cache_duration = 0; // fix for mike
			$this->render_website($page_file);
			return true;
		}
	}
	
	protected function home()
	{
		parent::__construct_website_base();
		$this->cache_duration = 300;
		$this->load->view('website/header');
		$this->load->view('website/index');
		$this->load->view('website/footer');
	}

}

?>
