<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Virtuals_Callback_Controller extends Iella_Base {
	
	public function index()
	{
		$content = Model_Content::from_object($this->iella_in->content);
		$cvs = Model_Content_Virtual_Source::find($content->id);
		if (!$cvs) return;

		$vs = Model_Virtual_Source::find($cvs->virtual_source_id);
		if (!$vs->callback) return;

		$iella = Virtuals_Callback_Iella_Request::create($vs);
		$iella->data->content = $content;
		$iella->data->uuid = $cvs->remote_uuid;
		$iella->send('content_event/published');
	}
	
}