<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Main_Controller extends Admin_Base {

	public function index()
	{
		$this->load->view('admin/header');
		$this->load->view('admin/analytics/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/analytics/reports/index');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

}