<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SendGrid_API {

	const BASE_URL = 'https://api.sendgrid.com/v3';
	const BOUNCES_LIMIT = 500;

	protected $key;

	public function __construct($key)
	{
		$this->key = $key;
	}

	public function bounces_list(DateTime $start = null, DateTime $end = null, $offset = 0)
	{
		if ($start)
		     $start_ts = $start->getTimestamp();
		else $start_ts = Date::days(-90)->getTimestamp();

		if ($end)
		     $end_ts = $end->getTimestamp();
		else $end_ts = Date::$now->getTimestamp();

		$request = $this->__create_request('suppression/bounces');
		$request->data['start_time'] = $start_ts;
		$request->data['end_time'] = $end_ts;
		$request->data['offset'] = $offset;
		$request->data['limit'] = static::BOUNCES_LIMIT;
		$response = $request->get();

		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			if (count($return->data) == static::BOUNCES_LIMIT)
			     $return->next = $offset + static::BOUNCES_LIMIT;
			else $return->next = false;
			return $return;
		}

		throw new Exception();
	}

	public function bounces_delete_all()
	{
		$request = $this->__create_request('suppression/bounces');
		$request->data = json_encode(array('delete_all' => true));
		$response = $request->exec('DELETE');
	}

	public function create_ip_pool($name)
	{
		$request = $this->__create_request('ips/pools');
		$request->data = json_encode(array('name' => $name));
		$response = $request->post();

		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	public function list_ip_pool()
	{
		$request = $this->__create_request('ips/pools');
		$response = $request->get();

		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	public function add_ip_pool_address($name, $ip)
	{
		$uri = sprintf('ips/pools/%s/ips', $name);
		$request = $this->__create_request($uri);
		$request->data = json_encode(array('ip' => $ip));
		$response = $request->post();
		
		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	public function delete_ip_pool_address($name, $ip)
	{
		$uri = sprintf('ips/pools/%s/ips/%s', $name, $ip);
		$request = $this->__create_request($uri);
		$response = $request->exec(HTTP_Request::METHOD_DELETE);
		
		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	public function list_ip_pool_addresses($name)
	{
		$uri = sprintf('ips/pools/%s', $name);
		$request = $this->__create_request($uri);
		$response = $request->get();

		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	public function delete_ip_pool($name)
	{
		$uri = sprintf('ips/pools/%s', $name);
		$request = $this->__create_request($uri);
		$response = $request->exec(HTTP_Request::METHOD_DELETE);

		if ($response) 
		{
			$return = new stdClass();
			$return->data = json_decode($response->data);
			return $return;
		}

		throw new Exception();
	}

	protected function __create_request($relative)
	{
		$request_url = sprintf('%s/%s', 
			static::BASE_URL, $relative);
		$request = new HTTP_Request($request_url);
		$auth = sprintf('Bearer %s', $this->key);
		$request->set_http_version(1.1);
		$request->set_header('Authorization', $auth);
		$request->set_header('Content-Type', 'application/json');
		$request->set_header('Accept', 'application/json');
		return $request;
	}
	
}