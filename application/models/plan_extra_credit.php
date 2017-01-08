<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Plan_Extra_Credit extends Model {
	
	protected static $__table = 'co_plan_extra_credit';
	protected static $__primary = array('plan_id', 'type');
	
	protected $item;

	public function item()
	{
		if ($this->item) return $this->item;
		return $this->item = Model_Item::find($this->item_id);
	}
	
}

?>