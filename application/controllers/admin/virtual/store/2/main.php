<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/virtual/store/base/main');
load_controller('admin/virtual/store/2/trait');

class Main_Controller extends VS_Main_Base {

	use Virtual_Store_Trait;
	
}