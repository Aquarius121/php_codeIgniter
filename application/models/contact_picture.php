<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Contact_Picture extends Model {
	
	protected static $__table = 'nr_contact_picture';
	protected static $__primary = 'contact_id';
	
	public static function create($file)
	{
		if (!Image::is_valid_file($file))
			return false;
		
		$instance = new static();		
		$original = Stored_Image::from_file($file);
		$original->move();
		$finger = $original->from_this_resized(33, 33, true, Image::FORMAT_GIF);
		$thumb = $original->from_this_resized(66, 66, true, Image::FORMAT_GIF);
		$instance->original = $original->filename;
		$instance->finger = $finger->filename;
		$instance->thumb = $thumb->filename;
		return $instance;
	}
	
}

?>