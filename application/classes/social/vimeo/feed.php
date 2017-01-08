<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Social_Vimeo_Feed {

	public static function get($vimeo_id, $count = 20)
	{

		$ci =& get_instance();

		if (preg_match('/^channels\//', $vimeo_id, $matches))
			$url_segment = null;
		else
			$url_segment = "users/";

		$access_token = $ci->conf('vimeo_access_token');

		$url = "https://api.vimeo.com/{$url_segment}{$vimeo_id}/videos";
		$url = "{$url}?access_token={$access_token}&page=1&per_page={$count}";
		$url = "{$url}&sort=date&direction=desc";

		$data = @file_get_contents($url);
		$data = json_decode($data, false);

		$items = $data->data;

		return $items;
	}

	public static function is_valid($id)
	{
		$ci =& get_instance();

		if (preg_match('/^channels\//', $id, $matches))
			$url_segment = null;
		else
			$url_segment = "users/";

		$access_token = $ci->conf('vimeo_access_token');

		$url = "https://api.vimeo.com/{$url_segment}{$id}/videos";
		$url = "{$url}?access_token={$access_token}&page=1&per_page=2";
		$url = "{$url}&sort=date&direction=desc";

		$data = @file_get_contents($url);
		$data = json_decode($data, false);

		if (!isset($data) || isset($data->error))
			return false;

		return true;
	}
	
}

?>