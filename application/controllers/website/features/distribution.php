<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');
load_shared_fnc('website/distribution_trait');

class Distribution_Controller extends Website_Base {

	use Distribution_Trait;

	public function index()
	{
		$this->load_items();
		$this->render_website('website/pages/features_distribution');
	}

	public function prnewswire()
	{
		$url = sprintf('%sother/prnewswire_distribution.pdf', 
			$this->vd->assets_base);
		$this->redirect($url, false);
	}
	
}