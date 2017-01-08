<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Unsubscribe_Controller extends CIL_Controller {

	public function index($id, $secret)
	{
		if (!$id) return;
		if (!$secret) return;

		$alert = Model_Insights_Alert::find($id);
		if ($alert->secret != $secret) return;
		$alert->is_enabled = 0;
		$alert->save();

		$feedback = new Feedback('success');
		$feedback->set_title('Success!');
		$feedback->set_text('Email alert has been disabled.');
		$this->add_feedback($feedback);

		$url = $this->website_url('manage');
		$this->redirect($url, false);
	}

}