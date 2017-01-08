<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('browse/base');

class Contact_Controller extends Browse_Base {

	public function index($id)
	{
		$company_id = (int) $this->newsroom->company_id;
		$m_contact = Model_Company_Contact::find($id);
		if (!$m_contact) show_404();
		if ((int) $m_contact->company_id !== $company_id)
			show_404();
		
		$this->redirect($m_contact->url());
	}
	
	public function id($id)
	{
		$this->index();
	}
	
	public function slug($slug = null)
	{
		$company_id = (int) $this->newsroom->company_id;
		
		$criteria = array();
		$criteria[] = array('slug', $slug);
		$criteria[] = array('company_id', $company_id);
		$m_contact = Model_Company_Contact::find($criteria);
		
		if ($this->is_detached_host && 
		    $d_contact = Detached_Session::read('m_company_contact'))
			$m_contact = $d_contact;
		
		if (!$m_contact) show_404();
		if ((int) $m_contact->company_id !== $company_id)
			show_404();
		
		// url rewrite has not happened
		// => direct access to view/contact/*
		if (!has_url_rewrite('view_contact')
			&& !$this->is_detached_host)
			$this->redirect($m_contact->url());
		
		$this->vd->m_contact = $m_contact;
		$this->title = $m_contact->name;
		
		$this->load->view('browse/header');
		$this->load->view('browse/view-contact');
		$this->load->view('browse/footer');
	}

}

?>