<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Blocked extends Model {
	
	protected static $__table = 'nr_blocked';
	protected static $__primary = 'addr';
	
	public function save()
	{
		if ($this->__is_new())
		{
			if (($existing = Model_Blocked::find($this->addr)))
			{
				$existing->date_blocked = Date::$now;
				$existing->save();
				return;
			}

			$this->uuid = UUID::create();			
		}

		$this->date_blocked = Date::$now;
		parent::save();
	}
	
}