<?php

Trait Distribution_Trait {

	protected function load_items()
	{
		return array(
			$this->vd->single_pr_premium = Model_Item::find_slug('premium-pr-credit'),
			$this->vd->single_pr_writing = Model_Item::find_slug('writing-credit'),
			$this->vd->single_pr_premium_plus = Model_Item::find_slug('premium-plus-credit'),
			$this->vd->single_pr_premium_plus_state = Model_Item::find_slug('premium-plus-state-credit'),
			$this->vd->single_pr_premium_plus_national = Model_Item::find_slug('premium-plus-national-credit'),
			$this->vd->single_pr_premium_financial = Model_Item::find_slug('premium-financial-credit'),
			$this->vd->single_pps_extra_100_words = Model_Item::find_slug('pps-extra-100-words'),
			$this->vd->single_ppn_extra_100_words = Model_Item::find_slug('ppn-extra-100-words'),
		);
	}

	public function order()
	{
		$items = $this->load_items();
		$item_id = $this->input->get_post('item_id');
		$add_writing = $this->input->get_post('add_writing');

		foreach ($items as $item)
		{
			if ($item->id == $item_id)
			{
				if ($add_writing) 
				     $callback = 'manage/writing/process';
				else if ($item->id == $this->vd->single_pr_premium->id)
				     $callback = 'manage/publish/pr/edit?distribution=PREMIUM';
				else if ($item->id == $this->vd->single_pr_premium_plus->id)
				     $callback = 'manage/publish/pr/edit?distribution=PREMIUM-PLUS';
				else if ($item->id == $this->vd->single_pr_premium_plus_state)
				     $callback = 'manage/publish/pr/edit?distribution=PREMIUM-PLUS-STATE';
				else if ($item->id == $this->vd->single_pr_premium_financial)
				     $callback = 'manage/publish/pr/edit?distribution=PREMIUM-PLUS-STATE';
				else $callback = 'manage/publish/pr/edit';

				$cart = Cart::instance();
				$cart_item = Cart_Item::create($item);
				$cart_item->callback = $callback;
				if ($add_writing)
					$cart_item->attach($this->vd->single_pr_writing);
				$cart->add_cart_item($cart_item);
				$cart->save();

				$this->redirect('order');
				return;
			}
		}

		$this->redirect('features/distribution');
	}

}