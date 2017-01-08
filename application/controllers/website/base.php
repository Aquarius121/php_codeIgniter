<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Website_Base extends CIL_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->is_website = true;

		// use a separate function so that
		// it can be overridden while still
		// permitting calling of the constructor
		$this->__construct_website_base();
	}

	protected function __construct_website_base()
	{
		if ($this->is_common_host && !$this->is_website_host)
		{
			$url = gstring($this->uri->uri_string);
			$url = $this->website_url($url);
			$this->redirect($url, false);
		}
		
		if (!$this->is_website_host)
		{
			$url = $this->website_url($this->uri->uri_string);
			$this->redirect_301($url, false);
			return;
		}
		
		$this->vd->meta_content = 
			Model_Meta_Content::find_current();
	}
	
	protected function render_website($view)
	{
		$content = $this->load->view($view, null, true);
		$this->load->view('website/header');
		$this->output->append_output($content);
		$this->load->view('website/footer');
	}

}