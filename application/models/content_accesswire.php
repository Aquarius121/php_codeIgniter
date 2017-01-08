<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Accesswire extends Model {
	
	protected static $__table = 'nr_content_accesswire';
	protected static $__primary = 'content_id';

	public function save()
	{
		$this->date_updated = Date::utc();
		parent::save();
	}

}
