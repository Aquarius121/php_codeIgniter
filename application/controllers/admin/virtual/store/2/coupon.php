<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/coupon');
load_controller('admin/virtual/store/2/trait');

class Coupon_Controller extends VS_Coupon_Base {

	use Virtual_Store_Trait;

}
