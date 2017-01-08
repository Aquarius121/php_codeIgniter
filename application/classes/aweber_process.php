<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class AWeber_Process {
	
	public $client;
	public $config;
	
	const STATUS_SUBSCRIBED = 'subscribed';
	const BASE_URL = 'https://api.aweber.com/1.0';
	
	public function __construct()
	{
		$this->client = AWeber_Client_Factory::create_and_authenticate();
		$this->config = AWeber_Client_Factory::load_config();
	}
	
	public function add_to_list($user, $list_id = false)
	{
		if (!$list_id) $list_id = $this->determine_list_id($user);
		if (!$list_id) return;
		
		$base = static::BASE_URL;
		$url = "{$base}/accounts/{$this->client->id}/lists/{$list_id}/subscribers"; 
		$params = array(
			'ws.op' => 'create',
			'email' => $user->email,
			'ip_address' => $user->remote_addr,
			'name' => $user->name(),
		);
		
		$response = $this->client->adapter->request('POST', $url,
			$params, array('return' => 'headers'));
		if (is_array($response) && $response['Status-Code'] == 201)
			return true;
		
		$ex = new Exception('AWeber: unable to add subscriber');
		$ex->response = $response;
		throw $ex;
	}
	
	public function move_to_list($user, $list_id = false)
	{
		if (!($subscriber = $this->find_subscriber($user->email)))
			return $this->add_to_list($user, $list_id);
		if ($subscriber->data['status'] !== static::STATUS_SUBSCRIBED)
			return false;
		
		if (!$list_id) $list_id = $this->determine_list_id($user);
		if (!$list_id) return;
		
		$base = static::BASE_URL;
		$url = "{$base}/accounts/{$this->client->id}/lists/{$list_id}";
		$params = array('ws.op' => 'move', 'list_link' => $url);
		$response = $this->client->adapter->request('POST', $subscriber->url,
			$params, array('return' => 'headers'));
		if (is_array($response) && $response['Status-Code'] == 201)
			return true;

		$ex = new Exception('AWeber: unable to move subscriber');
		$ex->response = $response;
		throw $ex;
	}
	
	public function find_subscriber($email)
	{
		$params = array('email' => $email);
		$response = $this->client->findSubscribers($params);
		if (!isset($response->data['entries'][0])) return false;
		
		$subscriber_url = $response->data['entries'][0]['self_link'];
		$subscriber = $this->client->loadFromUrl($subscriber_url);
		if (!$subscriber) return false;
		return $subscriber;
	}
	
	public function determine_list_id($user)
	{
		$plan = $user->m_plan();
		if (!$plan) return false;
		if (isset($this->config['lists'][(int) $plan->id]))
			return $this->config['lists'][(int) $plan->id];
		return false;
	}

}

?>