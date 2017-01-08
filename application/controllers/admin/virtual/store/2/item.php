<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/item');
load_controller('admin/virtual/store/2/trait');

class Item_Controller extends VS_Item_Base {

	use Virtual_Store_Trait;

}