<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/order');
load_controller('admin/virtual/store/2/trait');

class Order_Controller extends VS_Order_Base {

	use Virtual_Store_Trait;	
	
}