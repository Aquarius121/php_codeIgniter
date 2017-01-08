<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SEM_Rush_API {
	
	protected $key = '84358e6ac03d38cdd36a196289144446';
	protected $url = 'http://api.semrush.com/';
	
	public function query($params)
	{
		if (!isset($params->key))
			$params->key = $this->key;
		$request = new HTTP_Request($this->url);
		$request->data = (array) $params;
		$response = $request->get();
		return $response;
	}

}

?>