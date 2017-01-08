<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class VS_Base extends Admin_Base {

	public $store_base = null;
	public $m_virtual_source = null;

	public function __construct()
	{
		parent::__construct();

		if (preg_match('#^(admin/virtual/store/\d+)(/|$)#i', 
			$this->uri->uri_string, $m))
		{
			$this->vd->store_base = $m[1];
			$this->store_base = $m[1];
		}

		$this->vd->store_has_renewals = false;
		$this->vd->store_has_items = false;
		$this->vd->store_has_plans = false;
		$this->vd->store_has_coupons = false;
		$this->vd->store_has_orders = false;
		$this->vd->store_has_renewals = false;
	}

	protected function process_results(&$results)
	{
		$users_ids = array();

		foreach ($results as $k => $result)
		{
			// remove any remote user data
			$result->o_user_email = null;
			$result->o_user_id = null;

			if ($result->user && $result->user->raw_data)
			{
				$user = Model_With_Raw_Data::from_object($result->user);
				$urd = $user->raw_data();

				// TODO: batch load the user data
				if ($urd->newswire_user && $urd->newswire_user->id)
					$result->user = Model_User::find($urd->newswire_user->id);
			}
		}
	}
	
}

?>