<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Files_Controller extends CIL_Controller {

	// must be hardcoded! cannot use config
	const REAL_WEBSITE = 'http://www.newswire.com/';

	// this allows viewing assets 
	// on dev environment
	public function index()
	{
		if ($this->env['environment'] !== 'development') return;
		$request = new HTTP_Request(static::REAL_WEBSITE . $this->uri->uri_string);
		$response = $request->get();
		if (!$response) show_404();
		foreach ($response->headers as $header)
			if (!preg_match('#^accept#i', $header))
				header($header);
		echo $response->data;
		exit();
	}

}
