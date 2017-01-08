<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Billing extends Model {

	use Raw_Data_Trait {
		Raw_Data_Trait::raw_data as __raw_data;
	}
	
	const VIRTUAL_CARD_PAYPAL = 'PAYPAL';
	
	protected static $__table = 'co_billing';
	protected static $__primary = 'user_id';
	
	// * added default is_virtual_card
	// * customer_id => remote_customer_id
	public function raw_data($data = NR_DEFAULT)
	{
		$data = $this->__raw_data($data);
		if ($data && !isset($data->is_virtual_card))
			$data->is_virtual_card = false;
		if ($data && !isset($data->remote_customer_id)
		          &&  isset($data->customer_id))
			$data->remote_customer_id = $data->customer_id;
		return $data;
	}
	
}