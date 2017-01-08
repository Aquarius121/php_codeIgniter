<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/base');

class Pricing_Controller extends Website_Base {

	public $title = 'Pricing';

	public function index()
	{
		$this->vd->silver = Model_Item::find_slug('silver-plan');
		$this->vd->gold = Model_Item::find_slug('gold-plan');
		$this->vd->platinum = Model_Item::find_slug('platinum-plan');
		$this->vd->period = 1;
		$this->render();
	}
	
	public function m12()
	{
		$this->vd->silver = Model_Item::find_slug('silver-plan-12-months');
		$this->vd->gold = Model_Item::find_slug('gold-plan-12-months');
		$this->vd->platinum = Model_Item::find_slug('platinum-plan-12-months');
		$this->vd->period = 12;
		$this->render();
	}
	
	public function m24()
	{
		$this->vd->silver = Model_Item::find_slug('silver-plan-24-months');
		$this->vd->gold = Model_Item::find_slug('gold-plan-24-months');
		$this->vd->platinum = Model_Item::find_slug('platinum-plan-24-months');
		$this->vd->period = 24;
		$this->render();
	}
	
	protected function render()
	{
		$this->vd->free_column_enabled = false;
		$this->render_website('website/pages/pricing');
	}
	
}

?>