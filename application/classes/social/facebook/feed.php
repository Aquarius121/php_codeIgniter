<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Facebook_Feed extends Social_Facebook_API {

	public function get($id)
	{
		if ($id === null) return 0;
		
		$feeds = null;
		
		try
		{ $feeds = $this->facebook->api("/{$id}/posts"); }
		catch (Exception $e) {}
		
		return $feeds;
	}

	public static function is_valid($id)
	{
		$instance = new static(); 
		$data = $instance->get($id);
		if (!isset($data['data'])) return false;
		if (!count($data['data'])) return false;
		return true;
	}
	
}