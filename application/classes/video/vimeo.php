<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Video_Vimeo extends Video {
	
	// vimeo oembed documentation at 
	// https://developer.vimeo.com/apis/oembed

	public static $provider_name = 'Vimeo';
	
	public function __construct($video_id = null)
	{
		$this->video_id = $video_id;
	}
	
	public function parse_video_id($str)
	{
		$str = trim($str);

		// the entire string is the video id
		if (preg_match('#^[0-9]{6,20}$#i', $str))
			return $this->video_id = $str;

		// the string contains a video id and access token
		if (preg_match('#^(https?://(www\.)?vimeo\.com/)?([0-9]{6,20}/[a-z0-9]{6,20})$#i', $str, $m))
		{
			$this->video_id = $m[3];
			return $this->video_id;
		}

		$url = static::oembed_url($str);
		$http = new HTTP_Request($url);
		$response = $http->get();

		if (!$response || !$response->data)
			return $this->video_id = null;

		$response_data = json_decode($response->data);
		if (!$response_data->video_id)
			return $this->video_id = null;
		return $this->video_id = $response_data->video_id;
	}
	
	public function save_image()
	{
		if (!$this->video_id)
			return null;
		
		$video_id = $this->video_id;

		$json_url = "https://vimeo.com/api/v2/video/{$video_id}.json";
		$http = new HTTP_Request($json_url);
		$response = $http->get();

		if (!$response || !$response->data)
			return null;

		$data = json_decode($response->data);
		if (is_array($data)) $data = $data[0];
		$data = Raw_Data::from_object($data);

		$variants = array();
		$variants[] = $data->thumbnail_large;
		$variants[] = $data->thumbnail_medium;
		$variants[] = $data->thumbnail_small;
		
		$b_file = File_Util::buffer_file();
		$result = false;
		
		foreach ($variants as $variant)
		{
			$result = @copy($variant, $b_file);
			if ($result) break;
		}
		
		if (!$result) 
			return null;
		
		if (Image::is_valid_file($b_file))
			return $b_file;
		
		return null;
	}
	
	public function data()
	{
		if (!$this->video_id)
			return null;
		
		$url = static::oembed_url($this->url());
		$http = new HTTP_Request($url);
		$response = $http->get();

		if (!$response || !$response->data)
			return null;

		$response_data = Raw_Data::from_object(json_decode($response->data));
		if (!isset($response_data->video_id))
			return null;

		$data = new stdClass();
		$data->title = $response_data->title;
		$data->author = $response_data->author_name;
		$data->published = new DateTime($response_data->upload_date);
		$data->published->setTimezone(Date::$utc);
		$data->description = $response_data->description;
		
		return $data;
	}
	
	public function render($width = 640, $height = 360, $options = array())
	{
		if (!$this->video_id)
			return null;
		
		$ci =& get_instance();

		// Can use width and height both as parameters
		// better to just use width, as it will auto 
		// calculate height based on the width and
		// will prevent the video from being cluttered

		$json_url = static::oembed_url($this->url());
		$json_url = "{$json_url}&width={$width}";
		$http = new HTTP_Request($json_url);
		$response = $http->get();

		if (!$response || !$response->data)
			return null;

		$response_data = json_decode($response->data);

		$view_data = array();
		$view_data['html_render_video'] = $response_data->html;
		return $ci->load->view('partials/video/vimeo', 
			$view_data, true);
	}
	
	public function url()
	{
		if (!$this->video_id)
			return null;

		$url = "https://vimeo.com/{$this->video_id}";
		return $url;
	}

	protected static function oembed_url($url)
	{
		$url = rawurlencode($url);
		$oembed_endpoint = "https://vimeo.com/api/oembed.json";
		return "{$oembed_endpoint}?url={$url}";
	}
	
}

?>