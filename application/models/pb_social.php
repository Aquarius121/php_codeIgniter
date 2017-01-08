<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_PB_Social extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_pb_social';
	protected static $__primary = 'content_id';
	protected static $__compressed = array('raw_data');
	
	const TYPE_FACEBOOK  = 'facebook';
	const TYPE_TWITTER   = 'twitter';
	const TYPE_GPLUS     = 'gplus';
	const TYPE_YOUTUBE   = 'youtube';
	const TYPE_PINTEREST = 'pinterest';
	const TYPE_VIMEO	 = 'vimeo';
	const TYPE_INSTAGRAM = 'instagram';
	const TYPE_LINKEDIN = 'linkedin';

	public function url()
	{
		$raw_data = $this->raw_data();

		if ($this->media_type === static::TYPE_FACEBOOK)
		{
			if (isset($raw_data->story->link))
				return $raw_data->story->link;
			return Social_Facebook_Post::url($raw_data->id);
		}

		if ($this->media_type === static::TYPE_PINTEREST)
			return $raw_data->link;
		if ($this->media_type === static::TYPE_TWITTER)
			return Social_Twitter_Profile::url($raw_data->user->screen_name);
		if ($this->media_type === static::TYPE_GPLUS)
			return $raw_data->url;
		if ($this->media_type === static::TYPE_YOUTUBE)
			return $raw_data->link;
	}

	public static function social_media()
	{
		$s_media = array(
			static::TYPE_FACEBOOK,
			static::TYPE_TWITTER,
			static::TYPE_GPLUS,
			static::TYPE_YOUTUBE, 
			static::TYPE_PINTEREST,
			static::TYPE_VIMEO,
			static::TYPE_INSTAGRAM,
			static::TYPE_LINKEDIN
		);
		
		return $s_media;
	}

}

?>