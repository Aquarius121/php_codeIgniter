<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Distribution_Index extends Model {
	
	protected static $__table = 'nr_distribution_index';
	protected static $__primary = 'id';

	public static function find_index_from_source(Model_Distribution_Site $d_site, Model_Content $m_content)
	{
		return static::find(array(
			array('distribution_site_id', $d_site->id),
			array('content_id', $m_content->id),
		));
	}

	public static function create_index_from_source(Model_Distribution_Site $d_site, Model_Content $m_content)
	{
		$instance = new static();
		$instance->distribution_site_id = $d_site->id;
		$instance->content_id = $m_content->id;
		$instance->date_discovered = Date::$now;
		$instance->url = null;
		$instance->save();

		return $instance;
	}
	
}

?>
