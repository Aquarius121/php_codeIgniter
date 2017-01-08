<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reseller_Base extends CIL_Controller {

	protected $m_reseller_details;

	public function __construct()
	{
		parent::__construct();
		
		if (!$this->is_website_host)
		{
			if (Auth::is_admin_mode())
			{
				$url = $this->uri->uri_string;
				$url = Admo::url($url);
				$url = gstring($url);
				$this->redirect($url, false);
			}
			else
			{
				$url = $this->uri->uri_string;
				$url = $this->website_url($url);
				$url = gstring($url);
				$this->redirect($url, false);
			}
		}
		
		if (!Auth::is_user_online() ||
		    !Auth::user()->is_reseller)
			$this->denied();
	}
	
	public function m_reseller_details()
	{
		if (!$this->m_reseller_details)
			$this->m_reseller_details = Model_Reseller_Details::find(Auth::user()->id);
		return $this->m_reseller_details;
	}
	
	protected function auth_reseller_editor()
	{
		if ($this->m_reseller_details()->editing_privilege != 'reseller_editor')
			$this->denied();
	}
	
	protected function auth_non_reseller_editor()
	{
		if ($this->m_reseller_details()->editing_privilege == 'reseller_editor')
			$this->denied();
	}
	
	public function is_reseller_editor()
	{
		return $this->m_reseller_details()->editing_privilege == 'reseller_editor';
	}

}

?>
