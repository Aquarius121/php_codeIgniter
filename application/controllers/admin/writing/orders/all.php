<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/writing/orders/main');

class All_Controller extends Main_Controller {
	
	protected $tab = 'all';
	protected $filter = 1;
		
	public function index($visible_bits = null, $chunk = 1)
	{
		$this->vd->tab_name = 'All Orders';
		$this->visible_bits = $visible_bits;
		parent::index($chunk);
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;
		
		$this->load->view('admin/header');
		$this->load->view('admin/writing/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/writing/orders/list-all');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
}

?>