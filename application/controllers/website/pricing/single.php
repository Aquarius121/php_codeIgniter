<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');
load_shared_fnc('website/distribution_trait');

class Single_Controller extends Website_Base {

	use Distribution_Trait;

	public $title = 'Pricing';
	
	public function index()
	{
		$this->load_items();
		$this->render_website('website/pages/pricing_single');
	}
	
	protected function render()
	{
		$this->vd->free_column_enabled = false;
		$this->render_website('website/pages/pricing');
	}
	
}

?>