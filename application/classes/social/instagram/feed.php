<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Instagram_Feed {
	
	public static function get($id, $num_feed = 5)
	{
		$access_token = "2926220194.ab103e5.5fed9d41d65e4fe7bdff29f08734d5dc";

		if (!$id)
			return;

		// Retrieving numeric instagram id of the user			
		$link = "https://api.instagram.com/v1/users/search?q={$id}&access_token={$access_token}";

		if(get_http_response_code($link) != "200")
			return false;
		
		$result = file_get_contents($link);
		$result = json_decode($result);

		if ($result->meta->code !== 200)
			return array();

		$numeric_id = $result->data[0]->id;

		if (!$numeric_id)
			return array();

		$link = "https://api.instagram.com/v1/users/{$numeric_id}/media/recent/?";
		$link .= "access_token={$access_token}&count={$num_feed}";

		$json = file_get_contents($link);
		$results = json_decode($json);

		if (empty($results->data))
			return array();

		return $results->data;
	}

	public static function is_valid($id)
	{		
		$feed = static::get($id);
		if (!count($feed))
			return false;
		else
			return true;
	}
	
}

?>