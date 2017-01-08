<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Iella_Request {
	
	protected $secret;
	
	public $base;
	public $data;
	public $response;
	public $raw_response;
	public $enable_debug = true;

	public $files = array();
	
	public function __construct()
	{
		$ci =& get_instance();
		$this->data = new stdClass();
		$this->base = $ci->conf('iella_base_url');

		if (!$this->secret)
		{
			$secret_file = $ci->conf('iella_secret_file');
			$this->secret = file_get_contents($secret_file);
		}
	}

	public function add_file($file, $name = null)
	{
		if (!$name)
		{
			$hash = substr(md5(microtime(true)), 0, 16);
			$name = sprintf('iella_file_%s', $hash);
		}
		
		$_file = new stdClass();
		$_file->name = $name;
		$_file->path = $file;
		$this->files[] = $_file;
		return $name;
	}
	
	public function send($method, $data = null)
	{
		if ($data === null) 
			$data = $this->data;
		
		$ci =& get_instance();
		$http_request = new HTTP_Request();
		$http_request->url = "{$this->base}{$method}";		
		$http_request->data = array();
		$http_request->data['iella-secret'] = $this->secret;
		$http_request->data['iella-in'] = json_encode($data);
		$http_request->data['iella-url'] = $http_request->url;
		$http_request->data['iella-files'] = json_encode($this->files);
		
		foreach ($this->files as $_file)
			$http_request->add_file($_file->name, $_file->path);
		$http_request->encode_as_form_data();

		$request_version = $ci->conf('request_version');
		$http_request->set_header('cookie', 
			sprintf('use_v%d=1', $request_version)); 

		$this->raw_response = $http_request->post();

		// request failed completely
		if ($this->raw_response === false ||
			// request succeeded but there is an issue within iella
			(($this->response = json_decode($this->raw_response->data)) === null 
				&& json_last_error() !== JSON_ERROR_NONE))
		{
			if ($this->enable_debug)
			{
				$debug = new stdClass();
				$debug->data = $data;
				$debug->request = $http_request;
				$debug->response = $this->raw_response;
				$debug->json_error = json_last_error_msg();
				$alert = new Critical_Alert($debug);
				$alert->send();
			}
			
			return;
		}

		return $this->response;
	}
	
}

