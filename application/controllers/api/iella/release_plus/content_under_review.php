<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Content_Under_Review_Controller extends Iella_Base {
	
	public function index()
	{
		$m_content = Model_Content::from_object($this->iella_in->content);
		$releases = Model_Content_Release_Plus::find_all_content($m_content->id);
		$mailer = new Release_Plus_Mailer();
		
		foreach ($releases as $release)
			$mailer->send_under_review($m_content, $release);
	}
	
}

?>