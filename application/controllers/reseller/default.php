<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/base');

class Default_Controller extends Reseller_Base {

	public function index()
	{
		$m_reseller_details = Model_Reseller_Details::find(array('user_id', Auth::user()->id));
		if($m_reseller_details->editing_privilege == 'reseller_editor')
			$this->redirect('reseller');
		else
			$this->redirect('manage');
	}

}

?>