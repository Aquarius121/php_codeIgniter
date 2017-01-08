<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Facebook_Shares {

	public static function get(Model_Content $m_content, Model_Newsroom $m_newsroom)
	{
		$shares = 0;
		$ci =& get_instance();		
		$share_url = $ci->website_url($m_content->url());
		$shares += static::__fetch($share_url);

		if ($m_newsroom->is_active)
		{
			$share_url = $m_newsroom->url($m_content->url(), true);
			$shares += static::__fetch($share_url);
		}

		$chain = Model_Content_Slug_Redirect::find_all_within_chain($m_content->slug, 2);
		
		foreach ($chain as $csr) 
		{
			$share_url = $ci->website_url($m_content->url(true, $csr->new_slug));
			$shares += static::__fetch($share_url);

			if ($m_newsroom->is_active)
			{
				$share_url = $m_newsroom->url($m_content->url(true, $csr->new_slug), true);
				$shares += static::__fetch($share_url);
			}
		}

		return $shares;
	}

	protected static function __fetch($url)
	{
		$cache_name = sprintf('facebook_shares_%s', $url);
		$cache_name = md5($cache_name);
		$shares = Data_Cache_LT::read($cache_name);
		if ($shares !== false)
			return (int) $shares;
		
		$data_url = urlencode($url);
		$data_url = "https://graph.facebook.com/?ids={$data_url}";
		$source = @file_get_contents($data_url);
		$data = @json_decode($source);
		
		if (!empty($data->{$url}->shares))
			$shares = $data->{$url}->shares;
		$shares = (int) $shares;
		
		Data_Cache_LT::write($cache_name, $shares, 900);
		return $shares;
	}
	
}

?>