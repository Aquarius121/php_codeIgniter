<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('manage/base');
load_controller('shared/upgrade');
load_controller('shared/order');

class Upgrade_Controller extends Manage_Base {
	
	use Order_Trait;
	use Upgrade_Trait;

	protected $order_url_prefix = 'manage/order';
	
	public function __construct()
	{
		parent::__construct();
		$this->vd->order_url_prefix =
			$this->order_url_prefix;
	}

	public function index()
	{
		if (Auth::user()->has_platinum_access())
			$this->redirect('manage/upgrade/credits');
		$this->redirect('manage/upgrade/plans');
	}
	
	public function credits()
	{
		$this->vd->cart = Cart::instance();
		$items = $this->locate_credits_for_access_level();
		$this->vd->pr_credit = $items->pr_credit;
		$this->vd->email_credit = $items->email_credit;
		$this->vd->newsroom_credit = $items->newsroom_credit;
		$this->vd->writing_credit = $items->writing_credit;
		$this->vd->other_items = $items->other;
		
		$this->load->view('manage/header');
		$this->load->view('manage/upgrade/credits');
		$this->load->view('manage/footer');
	}
	
	protected function plans_render()
	{
		$this->vd->silver_plan = Model_Plan::from_item($this->vd->silver);
		$this->vd->gold_plan = Model_Plan::from_item($this->vd->gold);
		$this->vd->platinum_plan = Model_Plan::from_item($this->vd->platinum);
		$this->vd->cart = Cart::instance();		
		$this->load->view('manage/header');
		$this->load->view('manage/upgrade/plans');
		$this->load->view('manage/footer');
	}
	
	public function plans()
	{
		$this->vd->silver = Model_Item::find_slug('silver-plan');
		$this->vd->gold = Model_Item::find_slug('gold-plan');
		$this->vd->platinum = Model_Item::find_slug('platinum-plan');
		$this->vd->period = 1;
		$this->plans_render();
	}
	
	public function plans_annual()
	{
		$this->vd->silver = Model_Item::find_slug('silver-plan-12-months');
		$this->vd->gold = Model_Item::find_slug('gold-plan-12-months');
		$this->vd->platinum = Model_Item::find_slug('platinum-plan-12-months');
		$this->vd->period = 12;
		$this->plans_render();
	}

	// -------------------------------------
	// -------------------------------------

	public function premium()
	{
		// load feedback message for the user
		$feedback_view = 'manage/upgrade/partials/premium_feedback';
		$feedback = $this->load->view($feedback_view, null, true);
		$this->add_feedback($feedback);
		$this->redirect('manage/upgrade');
	}
	
	public function newsroom()
	{
		if (Auth::user()->newsroom_credits_available())
		{
			// load feedback message for the user
			$this->vd->upgrade_redirect = $this->session->get('upgrade-redirect');
			$this->session->delete('upgrade-redirect');
			$this->vd->upgrade_company = $this->session->get('upgrade-company');
			$this->session->delete('upgrade-company');
			$feedback_view = 'manage/upgrade/partials/newsroom_activation_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->redirect('manage/companies');
		}
		else
		{
			// load feedback message for the user
			$feedback_view = 'manage/upgrade/partials/newsroom_needed_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->add_feedback($feedback);
			$this->redirect('manage/upgrade');
		}
	}
	
}

?>