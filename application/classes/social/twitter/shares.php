<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Twitter_Shares extends Social_Twitter_API {

	protected static function __list(Social_Twitter_API $twitter, $search)
	{
		$params = array(
			'q' => $search,
			'count' => 100,
			'result_type' => 'recent',
			'include_entities' => false,
		);
		
		try { $response = $twitter->get('search/tweets', $params); }
		catch (Exception $e) { return array(); }
		
		if (!empty($response->statuses))
		{
			return array_map(function($tweet) {
				return $tweet->id;
			}, $response->statuses);
		}

		return array();
	}

	public static function __list_all(Model_Content $m_content)
	{
		$shares = array();
		$twitter = new Social_Twitter_API();
		$twitter->set_default_access_token();

		$shares += static::__list($twitter, $m_content->title);
		$shares += static::__list($twitter, $m_content->slug);
		$chain = Model_Content_Slug_Redirect::find_all_within_chain($m_content->slug, 2);
		foreach ($chain as $csr) $shares += static::__list($twitter, $csr->new_slug);
		return array_unique($shares);
	}

	public static function __count(Model_Content $m_content)
	{
		return count(static::__list_all($m_content));
	}

	public static function get(Model_Content $m_content)
	{
		$class = get_class();
		$cache_name = sprintf('twitter_shares_%d', $m_content->id);
		$cache_name = md5($cache_name);
		$shares = Data_Cache_LT::read($cache_name);
		if ($shares !== false)
			return (int) $shares;

		$shares = static::__count($m_content);
		Data_Cache_LT::write($cache_name, $shares, 900);
		return $shares;
	}
	
}

?>