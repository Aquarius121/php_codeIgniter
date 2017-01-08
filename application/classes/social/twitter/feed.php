<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_Feed extends Social_Twitter_API {
	
	const RATE_LIMIT_EXCEEDED_ERROR_CODE = 88;

	protected $screen_name;

	public function __construct($screen_name = null)
	{
		parent::__construct();
		$this->screen_name = $screen_name;
	}
	
	public function set_screen_name($screen_name)
	{
		$this->screen_name = $screen_name;
	}
	
	public function get($_params = array())
	{
		$tweets = null;
		$params = array(
			'screen_name' => $this->screen_name,
			'count' => 5,
			'include_rts' => 1,
			'exclude_replies' => 1,
		);

		foreach ($_params as $k => $v)
			$params[$k] = $v;

		try { $tweets = $this->twitter->get('statuses/user_timeline', $params); }
		catch (Exception $e) {}
		return $tweets;
	}

	public static function is_valid($screen_name)
	{
		$instance = new static($screen_name);
		$tweets = $instance->get(array('count' => 5));
		return is_array($tweets) && count($tweets);
	}

	public function get_for_social_wire($count = 5)
	{
		$params = array(
			'count' => $count,
			'include_rts' => 0,
			'exclude_replies' => 1,
		);

		$tweets = $this->get($params);

		if (!empty($tweets->errors) && count($tweets->errors))
			if ($tweets->errors[0]->code == static::RATE_LIMIT_EXCEEDED_ERROR_CODE)
				return array();
		
		return $tweets;
	}
	
}

?>