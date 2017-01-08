<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class KissMetrics_Event_Library extends KissMetrics_Process {
	
	// whenever a user registers
	public function event_signed_up()
	{
		$this->event('Signed Up', array(
			'Plan Name' => $this->user->package_name(),
		));
	}
	
	// whenever a user signs into site
	public function event_signed_in()
	{
		$this->event('Signed In', array(
			'Plan Name' => $this->user->package_name(),
		));
	}
	
	// whenever a user buys a plan 
	public function event_purchased_plan($cart_item)
	{
		$plan = Model_Plan::from_item($cart_item->item());
		if (!$plan) return;
		
		$this->event('Subscribed', array(
			'Purchase Amount' => $cart_item->price,
			'Plan Name' => $plan->name,
		));
	}
	
	// whenever a user buys a non-plan
	public function event_purchased_item($cart_item)
	{		
		$this->event('Purchased Item', array(
			'Purchase Amount' => $cart_item->price,
			'Item Name' => $cart_item->item()->name,
		));
	}
	
	// whenever a user is billed
	public function event_billed($transaction)
	{
		$item_names = array();
		$virtual_cart = Virtual_Cart::instance();
		$virtual_cart->unserialize($transaction->virtual_cart);
		foreach ($virtual_cart->items() as $c_item)
			$item_names[] = $c_item->item()->name;
		
		$this->event('Billed', array(
			'Billing Description' => comma_separate($item_names, true),
			'Billing Transaction' => $transaction->id,
			'Billing Amount' => $transaction->price,
		));
	}
	
	// when a user cancels plan
	public function event_cancelled()
	{
		// * ignore the spelling error
		$this->event('Canceled', array());
	}
	
	// whenever a user submits content
	public function event_submitted($content)
	{
		if ($content->type == Model_Content::TYPE_PR)
		     $name = 'Submitted PR';
		else if ($content->type == Model_Content::TYPE_NEWS)
			  $name = 'Submitted News';
		else if ($content->type == Model_Content::TYPE_VIDEO)
			  $name = 'Submitted Video';
		else if ($content->type == Model_Content::TYPE_IMAGE)
			  $name = 'Submitted Image';
		else if ($content->type == Model_Content::TYPE_EVENT)
			  $name = 'Submitted Event';
		else if ($content->type == Model_Content::TYPE_AUDIO)
			  $name = 'Submitted Audio';
		else return;
		
		$data = array();
		if ($content->type == Model_Content::TYPE_PR)
			$data = array('Submitted PR Type' =>
				$content->is_premium ? 'Premium' : 'Basic');
		
		$this->event($name, $data);
	}
	
	// whenever a user publishes content
	public function event_published($content)
	{
		if ($content->type == Model_Content::TYPE_PR)
		     $name = 'Published PR';
		else if ($content->type == Model_Content::TYPE_NEWS)
			  $name = 'Published News';
		else if ($content->type == Model_Content::TYPE_VIDEO)
			  $name = 'Published Video';
		else if ($content->type == Model_Content::TYPE_IMAGE)
			  $name = 'Published Image';
		else if ($content->type == Model_Content::TYPE_EVENT)
			  $name = 'Published Event';
		else if ($content->type == Model_Content::TYPE_AUDIO)
			  $name = 'Published Audio';
		else return;
		
		$data = array();
		if ($content->type == Model_Content::TYPE_PR)
			$data = array('Published PR Type' =>
				$content->is_premium ? 'Premium' : 'Basic');
		
		$this->event($name, $data);
	}
	
}
