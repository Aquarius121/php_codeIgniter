<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Fetch_Tweets_Trait {

	public function fetch_tweets()
	{
		$username = $this->input->get('username');
		$count = $this->input->get('count');
		$feed = new Social_Twitter_Feed($username);
		$_tweets = array();
		$tweets = $feed->get(array(
			'count' => $count ? $count : 10,
			'user_trim' => 1,
		));

		if (!$tweets || !is_array($tweets)) 
			return $this->json(null);

		foreach ($tweets as $tweet)
		{
			$_tweet = new stdClass();
			$_tweet->id = $tweet->id;
			$_tweet->text = $tweet->text;
			$_tweet->html = Social_Twitter_Post::parse($tweet->text);
			$_tweets[] = $_tweet;
		}

		$this->json($_tweets);
	}

}

?>