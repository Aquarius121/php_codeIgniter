<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Youtube_Feed {
	
	public static function get($id)
	{
		$ci =& get_instance();

		$key = $ci->conf('youtube_api_key');

		if (preg_match('/^channel\//', $id, $matches))
		{
			$id = str_replace('channel/', '', $id);
			$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$id}";
			$url = "{$url}&order=date&key={$key}&maxResults=20";

			$data = @file_get_contents($url);
			$data = json_decode($data, true);

			$items = $data['items'];
		
			if (count($items))
				return $items;
		}

		$id = str_replace('user/', '', $id);
		$url = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails";
		$url = "{$url}&forUsername={$id}&key={$key}";
		
		$data = @file_get_contents($url);
		$data = json_decode($data, true);

		$channel_id = @$data['items'][0]['id'];

		$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&";

		if (!empty($channel_id))
			$url = "{$url}channelId={$channel_id}";
		else // possible the id passed as param may be the channel id
			$url = "{$url}channelId={$id}";

		$url = "{$url}&order=date&key={$key}&maxResults=20";

		$data = @file_get_contents($url);
		$data = json_decode($data, true);

		$items = $data['items'];

		return $items;
	}

	public static function is_valid($id)
	{
		$ci =& get_instance();
		$key = $ci->conf('youtube_api_key');

		if (preg_match('/^channel\//', $id, $matches))
		{
			$id = str_replace('channel/', '', $id);
			$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channel_id}";
			$url = "{$url}&order=date&key={$key}&maxResults=10";
 
			$data = @file_get_contents($url);
			$data = json_decode($data, true);

			$items = $data['items'];
		
			if (count($items))
				return true;
		}

		$id = str_replace('user/', '', $id);

		$url = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails";
		$url = "{$url}&forUsername={$id}&key={$key}";
		
		$data = @file_get_contents($url);
		$data = json_decode($data, true);

		$channel_id = @$data['items'][0]['id'];

		$url = "https://www.googleapis.com/youtube/v3/search?part=snippet";

		if (!empty($channel_id))
			$url = "{$url}&channelId={$channel_id}";
		else // the id passed as param may be the channel id
			$url = "{$url}&channelId={$id}";

		$url = "{$url}&order=date&key={$key}&maxResults=10";

		$data = @file_get_contents($url);
		$data = json_decode($data, true);

		$items = $data['items'];
		
		if ( ! count($items))
			return false;		
		else
			return true;
	}
	
}

?>