<?php

class Distribution_Bundle_Premium_Plus_State extends Distribution_Bundle_Base {

	protected $providers = array(
		Model_Content_Release_Plus::PROVIDER_DIGITAL_JOURNAL,
		Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE,
		Model_Content_Release_Plus::PROVIDER_WORLDNOW,
	);

	public function customize(Raw_Data $raw)
	{
		if ($raw->state)
		{
			foreach ($this->cdb->raw_data_object()->crp as $provider => $crpid)
			{
				if ($provider == Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE)
				{
					$crp = Model_Content_Release_Plus::find($crpid);
					if (!$crp) continue;

					$crp_raw = $crp->raw_data_object();
					$crp_raw->state = $raw->state;
					$crp->raw_data($crp_raw);
					$crp->save();
					break;
				}
			}
		}

		$cdb_raw = $this->cdb->raw_data_object();
		$cdb_raw->customization = $raw;
		$this->cdb->raw_data($cdb_raw);
		$this->cdb->save();
	}

}

