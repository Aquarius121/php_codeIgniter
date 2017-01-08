<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('website/order/offer_writing');
load_controller('website/order/capture_lead_trait');

class Google_PRNewswire_Controller extends Offer_Writing_Controller {

	protected $force_website_checkout = true;

	use Capture_Lead_Trait;

	public function index()
	{
		$this->v1();
	}

	public function v1()
	{
		$this->vd->inject_after_rule[] = 'website/order/google_prnewswire_v1';
		$this->capture_lead();
		parent::index();
	}

	public function v2()
	{
		$this->vd->inject_before_rule[] = 'website/order/google_prnewswire_v2';
		$this->capture_lead();
		parent::index();
	}
	
}