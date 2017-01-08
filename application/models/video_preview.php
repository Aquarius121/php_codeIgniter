<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Video_Preview extends Model {
	
	protected static $__table = 'nr_video_preview';
	protected static $__primary = array(
		'web_video_provider', 
		'web_video_id'
	);

	public static function find_or_create($web_video_provider, $web_video_id)
	{
		$instance = static::find_id(array($web_video_provider, $web_video_id));
		if ($instance) return $instance;

		$instance = new static();
		$instance->web_video_provider = $web_video_provider;
		$instance->web_video_id = $web_video_id;
		if (!$instance->video()) return;
		$instance->generate();
		
		return $instance;
	}
	
	public function video()
	{
		return Video::get_instance($this->web_video_provider, 
			$this->web_video_id);
	}

	public function image()
	{
		return Model_Image::find($this->image_id);
	}

	public function generate()
	{
		if (!($video = $this->video())) return;
		if (($buffer_file = $video->save_image()) &&
			 Image::is_valid_file($buffer_file))
		{
			$variant = 'web-video-preview';
			$v_size = get_instance()->conf('v_sizes')[$variant];
			$controls_file = sprintf('assets/other/%s.png',
				$this->web_video_provider);

			$si_original = Stored_Image::from_file($buffer_file);
			$si_thumbnail = $si_original->from_this_resized($v_size);
			$im_thumbnail = $si_thumbnail->image();
			$si_thumbnail->delete();
			$si_original->delete();

			$im_controls = Image::from_file($controls_file);
			$im_thumbnail->execute_copy($im_controls);
			$im_thumbnail->format(Image::FORMAT_PNG);
			$im_thumbnail->save($buffer_file);
			unset($im_thumbnail);
			unset($im_controls);

			$si_preview = Stored_Image::from_file($buffer_file, Stored_Image::EXT_PNG);
			$si_id = $si_preview->save_to_db();
			
			$m_image = Model_Image::__new_save();
			$m_image->add_variant($si_id, $variant);

			$this->image_id = $m_image->id;
			$this->save();
		}
	}
	
}

?>