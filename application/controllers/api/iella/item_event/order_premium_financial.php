<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/item_event/order_distribution_bundle');

class Order_Premium_Financial_Controller extends Order_Distribution_Bundle_Controller {
	
	protected $credit_class = Credit::TYPE_PREMIUM_FINANCIAL;
	
}