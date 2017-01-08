<?php

class PRNewswire_API_Connection {

	public $_request;
	public $_response;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function request($uri, $bdata = null)
	{
		$full_uri = $this->__full_uri($uri);
		$this->_request = new HTTP_Request($full_uri);
		$this->_request->set_timeout(300);
		$this->__add_authentication($this->_request);
		$method = HTTP_Request::METHOD_GET;

		if ($bdata !== null)
		{
			$method = HTTP_Request::METHOD_POST;
			$this->_request->data = $bdata;
		}

		$this->_response = $this->_request->exec($method);
		if (!$this->_response) return false;
		$this->_response->body = $this->_response->data;
		$this->_response->data = $this->__parse_xml($this->_response->body);
		return $this->_response;
	}

	protected function __parse_xml($res)
	{
		$use_errors = libxml_use_internal_errors(true);
		$sxml = simplexml_load_string($res);
		libxml_clear_errors();
		libxml_use_internal_errors($use_errors);
		if (!$sxml) return false;
		return sxml_to_raw_data($sxml);
	}

	protected function __full_uri($uri)
	{
		return concat($this->config['api_base'], $uri);
	}

	protected function __add_authentication(HTTP_Request $request)
	{
		$request->set_header('CAPIKEY', $this->config['api_key']);
	}
	
}