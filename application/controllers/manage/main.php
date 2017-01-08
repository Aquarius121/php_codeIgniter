<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');

class Main_Controller extends Manage_Base {
	
	public function index()
	{	
		$user = Auth::user();
		
		if (count($this->vd->user_newsrooms) <= 1)
		{
			$url = $user->default_newsroom()->url('manage/dashboard');
			$this->redirect($url, false);
		}

		if (!$this->is_common_host)
		     $this->redirect('manage/dashboard');
		else $this->redirect('manage/overview/dashboard');
	}

}