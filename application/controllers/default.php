<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Default_Controller extends CIL_Controller {

	public function index()
	{
		if (!Auth::is_user_online()) 
			$this->redirect(null);
		
		$user = Auth::user();
		if ($user->is_admin) $this->redirect($this->website_url('admin'), false);
		if ($user->is_reseller) $this->redirect($this->website_url('reseller'), false);
		$this->redirect($this->website_url('manage'), false);
	}
	
	public function website_url($relative_url = null, $use_ssl = NR_DEFAULT)
	{
		if (Auth::is_admin_mode())
		     return Admo::url($relative_url);
		else return parent::website_url($relative_url, $use_ssl);
	}

}