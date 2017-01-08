<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Virtual_Source extends Model {

	protected static $__table = 'nr_virtual_source';
	protected static $__cached_enabled = true;

	// the name of the internal source
	const INTERNAL = 'Newswire.com';

	const ID_GENERIC = 1;
	const ID_PRESSRELEASECOM = 2;

	public static function find_id($id)
	{
		if ($id === -1)
		{
			$internal = new static();
			$internal->id = -1;
			$internal->name = static::INTERNAL;
			$internal->callback = null;
			$internal->is_common = 1;
			return $internal;
		}

		return parent::find_id($id);
	}

}

?>