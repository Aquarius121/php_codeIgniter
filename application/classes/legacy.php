<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class LEGACY {

	public static function database($class = null)
	{
		if ($class === null) $class = 'legacy';
		return get_instance()->load_db($class);
	}
	
	public static function content_url($content_id)
	{
		$ldb = static::database();
		$result = $ldb->select('id, url_title')->from('prs')
			->where('migrated_content_id', $content_id)->get();
		if (!($pr = $result->row())) return false;
		return "{$pr->url_title}/{$pr->id}";
	}
	
}