<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/contact/pitch_wizard_order/main');

class Upload_List_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();		
		$this->vd->title[] = 'Upload Lists';
	}

	public function index($chunk = 1, $filter = 1)
	{
		$redirect_url = 'admin/contact/pitch_wizard_order/upload_list';				
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring('admin/contact/pitch_wizard_order/upload_list/-chunk-');
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_pending_list($chunkination);
		
		if ($chunkination->is_out_of_bounds())
		{
			// out of bounds so redirect to first
			$url = 'admin/contact/pitch_wizard_order/upload_list';
			$this->redirect(gstring($url));
		}
		
		$this->add_upload_modal();		
		$this->add_order_detail_modal();
		
		$view_name = "admin/contact/pitch_wizard_order/upload_list";
		$this->render($chunkination, $results, $view_name);
	}

}

?>