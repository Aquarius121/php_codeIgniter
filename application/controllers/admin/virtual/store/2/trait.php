<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Virtual_Store_Trait {

	protected $connection_tested = false;

	public function __on_execution_start()
	{
		$this->vd->store_has_renewals = false;
		$this->vd->store_has_items = true;
		$this->vd->store_has_plans = false;
		$this->vd->store_has_coupons = true;
		$this->vd->store_has_orders = true;
		$this->vd->store_has_renewals = false;

		$this->m_virtual_source = Model_Virtual_Source::find(Model_Virtual_Source::ID_PRESSRELEASECOM);
		$this->vd->m_virtual_source = $this->m_virtual_source;
	}

	protected function create_request()
	{
		if (!$this->connection_tested)
		{
			$this->connection_tested = true;
			$test = Virtuals_Callback_Iella_Request::create($this->m_virtual_source);
			$test->data->hello = 1;
			$response = $test->send('echo');

			if (empty($response->hello))
			{
				$feedback = new Feedback('error');
				$feedback->set_title('Error!');
				$feedback->set_text('Could not establish connection to Iella.');
				$this->use_feedback($feedback);
			}
		}

		return Virtuals_Callback_Iella_Request::create($this->m_virtual_source);
	}

}