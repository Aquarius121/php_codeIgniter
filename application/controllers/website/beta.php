<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Beta_Controller extends Website_Base {

	public $title = 'Newswire Beta';

	public function index()
	{
		$this->redirect(null);
		
		// $versions = array('use_v20', 'use_v25');

		// if ($this->input->get('switch'))
		// {
		// 	foreach ($versions as $version)
		// 	{
		// 		$cookie = new Cookie($version);
		// 		$cookie->delete();
		// 	}

		// 	$cookie = new Cookie($this->input->get('switch'));
		// 	$cookie->set(1);

		// 	if ($hash = $this->input->get('intent'))
		// 	{
		// 		$intent = Data_Cache_LT::read($hash);
		// 		Data_Cache_LT::delete($hash);
		// 		$this->redirect($intent, false);
		// 	}

		// 	$this->redirect('default');
		// }

		// $this->vd->versions = $versions;
		// $this->render_website('website/pages/beta');
	}

}
