<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/base');

class Report_Controller extends Reseller_Base {
	
	public function index($id)
	{
		$m_content = Model_Content::find($id);
		$m_newsroom = Model_Newsroom::find($m_content->company_id);
	}
	
}

?>