<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

trait Upgrade_Trait {

	protected $__locate_credits_for_access_level_cached;
	
	protected function locate_credits_for_access_level()
	{
		if ($this->__locate_credits_for_access_level_cached)
			return $this->__locate_credits_for_access_level_cached;
		$items = Store::locate_store_items_for_user(Auth::user());
		$this->__locate_credits_for_access_level_cached = $items;
		return $items;
	}
	
}