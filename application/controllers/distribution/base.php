<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Distribution_Base extends CIL_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		if (!$this->is_website_host)
		{
			$url = gstring($this->uri->uri_string);
			$url = $this->website_url($url);
			$this->redirect($url, false);
		}
	}

}

?>