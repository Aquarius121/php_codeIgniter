<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Video_Youtube extends Video {
	
	public static $provider_name = 'YouTube';
	
	public function __construct($video_id = null)
	{
		$this->video_id = $video_id;
	}
	
	public function parse_video_id($str)
	{
		$str = trim($str);

		// the entire string is the video id
		if (preg_match('#^[a-z0-9_-]{10,20}$#i', $str))
			return $this->video_id = $str;

		$url_pattern = 
			# http://stackoverflow.com/questions/3392993
			# http://rubular.com/r/M9PJYcQxRW
			'#(?<=[vi]=)[a-z0-9\-_]{10,20}(?=(?:[\?\/&\#"\']|$))
			 |(?<=[vi]\/)[a-z0-9\-_]{10,20}(?=(?:[\?\/&\#"\']|$))
			 |(?<=embed\/)[a-z0-9\-_]{10,20}(?=(?:[\?\/&\#"\']|$))
			 |(?<=youtu\.be\/)[a-z0-9\-_]{10,20}(?=(?:[\?\/&\#"\']|$))
			 #ix';
			
		// extract the video id from a video link
		if (preg_match($url_pattern, $str, $match))
			return $this->video_id = $match[0];
		
		return $this->video_id = null;
	}
	
	public function save_image()
	{
		if (!$this->video_id)
			return null;
		
		$id = $this->video_id;
		$versions = array();
		$versions[] = 'maxresdefault';
		$versions[] = 'sddefault';
		$versions[] = 'hqdefault';
		$versions[] = 'default';
		
		$b_file = File_Util::buffer_file();
		$result = false;
		
		foreach ($versions as $version)
		{
			$url = "http://img.youtube.com/vi/{$id}/{$version}.jpg";
			$result = @copy($url, $b_file);
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
		
		$data = new Raw_Data();
		
		// API creds under google wire.me account as "Newswire Youtube"
		$url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=";
		$url = "{$url}{$this->video_id}&key=AIzaSyC8jVn33UCUcmi7x1QY1BSeCWr53u9pDg8";
		$raw = @file_get_contents($url);
		$source = @json_decode($raw);
		
		if (!isset($source->items[0]->snippet))
			return null;
		
		$source = $source->items[0]->snippet;
		$data->title = @$source->title;
		$data->author = @$source->channelTitle;
		$data->published = new DateTime(@$source->publishedAt);
		$data->published->setTimezone(Date::$utc);
		$data->description = @$source->description;
		
		return $data;
	}
	
	public function render($width = 640, $height = 360, $options = array())
	{
		if (!$this->video_id)
			return null;
		
		$ci =& get_instance();
		$view_data = array();
		$view_data['width'] = $width;
		$view_data['height'] = $height;
		$view_data['id'] = $this->video_id;
		$view_data['options'] = $options;
		
		return $ci->load->view('partials/video/youtube', 
			$view_data, true);
	}
	
	public function url()
	{
		if (!$this->video_id)
			return null;
		
		$url = "http://www.youtube.com/watch?v={$this->video_id}";
		return $url;
	}
	
}