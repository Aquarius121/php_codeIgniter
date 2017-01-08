<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Component_Set extends Model {
	
	protected static $__table = 'co_component_set';
	
	const UPDATE_CANCEL        = 'CANCEL';         // cancel of an item
	const UPDATE_RENEW_ADVANCE = 'RENEW_ADVANCE';  // increase date expires towards termination
	const UPDATE_RENEW_EXTEND  = 'RENEW_EXTEND';   // increate date of termination
	
	public function items()
	{
		$criteria = array('component_set_id', $this->id);
		$items = Model_Component_Item::find_all($criteria);
		return $items;
	}

}

?>