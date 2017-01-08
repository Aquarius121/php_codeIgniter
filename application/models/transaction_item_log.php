<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Transaction_Item_Log extends Model {
	
	protected static $__table = 'co_transaction_item_log';
	
	public static function create(Model_Transaction $transaction, Cart $vc)
	{
		foreach ($vc->items() as $citem)
		{
			$instance = new static();
			$instance->transaction_id = $transaction->id;
			$instance->item_id = $citem->item_id;
			$instance->quantity = $citem->quantity;
			$instance->price = $citem->price;
			$instance->save();
		}
	}
	
}
