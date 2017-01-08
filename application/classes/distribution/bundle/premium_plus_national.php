<?php

class Distribution_Bundle_Premium_Plus_National extends Distribution_Bundle_Base {

	protected $providers = array(
		Model_Content_Release_Plus::PROVIDER_DIGITAL_JOURNAL,
		Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE,
		Model_Content_Release_Plus::PROVIDER_WORLDNOW,
	);

	public function enable()
	{
		parent::enable();

		$crps = Raw_Data::from($this->cdb->raw_data_object()->crp);

		if ($crps[Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE])
		{
			$crpid = $crps[Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE];
			$crp = Model_Content_Release_Plus::find($crpid);
			if (!$crp) continue;

			$crp_raw = $crp->raw_data_object();
			$crp_raw->national = true;
			$crp->raw_data($crp_raw);
			$crp->save();
		}
	}

}