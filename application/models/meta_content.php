<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Meta_Content extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_meta_content';
	protected static $__primary = 'url';
	
	public static function find_current()
	{
		$ci =& get_instance();
		$url = sprintf('/%s', $ci->uri->uri_string);
		return static::find($url);
	}
	
}

?>