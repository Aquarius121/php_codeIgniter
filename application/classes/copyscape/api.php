<?php

class Copyscape_API {

	const API_URL = 'http://www.copyscape.com/api/';

	protected $username;
	protected $key;

	public function __construct($config)
	{
		$this->username = $config['username'];
		$this->key = $config['key'];
	}

	public function count($text, $encoding = 'UTF-8')
	{
		$req = new HTTP_Request(static::API_URL);
		$req->data->u = $this->username;
		$req->data->k = $this->key;
		$req->data->o = 'csearch';
		$req->data->f = 'xml';
		$req->data->e = $encoding;
		$req->data->t = $text;

		$res = $req->post();

		if (!$res || !$res->data)
			throw new Exception($req->error);

		$lxuie = libxml_use_internal_errors(true);
		$xml = simplexml_load_string($res->data);
		libxml_use_internal_errors($lxuie);

		if (!isset($xml->count))
			throw new Exception($res->data);

		return (int) $xml->count;
	}

}
