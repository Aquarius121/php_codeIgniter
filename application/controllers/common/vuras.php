<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class VURAS_Controller extends CIL_Controller {

	public function index($uid)
	{
		$user = Model_User::find($uid);
		if (!$user || !$user->is_virtual())
			throw new Exception();

		$vu = $user->virtual_user();
		$admin = Auth::user();
		$relative = build_url(array_slice(func_get_args(), 1));
		$relative = build_url('admo', $relative);
		$relative = gstring($relative);

		$ras = Virtual_User_Remote_Admo_Session::create($vu, $admin);
		$this->redirect($ras->url($relative), false);
	}
	
}

?>