<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_PB_PR extends Model {
	
	protected static $__table = 'nr_pb_pr';
	protected static $__primary = 'content_id';
	
	public function clean_files()
	{
		if ($this->stored_file_name_1)
		{
			$this->stored_file_name_1 = str_replace('\\', '/', $this->stored_file_name_1);
			$this->stored_file_name_1 = basename($this->stored_file_name_1);
		}
		
		if ($this->stored_file_name_2)
		{
			$this->stored_file_name_2 = str_replace('\\', '/', $this->stored_file_name_2);
			$this->stored_file_name_2 = basename($this->stored_file_name_2);
		}
	}
	
	public function clean_video()
	{
		$is_valid_video = false;
		
		if ($this->web_video_provider && $this->web_video_id)
		{
			if (($provider = Video::get_instance($this->web_video_provider)) !== null) 
			{
				if (($video_id = $provider->parse_video_id($this->web_video_id)) !== null) 
				{
					$this->web_video_id = $video_id;
					$is_valid_video = true;
				}
			}
		}
		
		if (!$is_valid_video)
		{
			$this->web_video_provider = null;
			$this->web_video_id = null;
		}
	}
	
}

?>