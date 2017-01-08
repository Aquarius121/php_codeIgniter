<?php

abstract class Distribution_Bundle_Base {

	protected $providers = array();
	protected $cdb;

	public function __construct(Model_Content_Distribution_Bundle $cdb)
	{
		$this->cdb = $cdb;
	}

	public function disable()
	{
		$rd = $this->cdb->raw_data_object();
		$crps = Raw_Data::from($rd->crp);
		
		foreach ($crps as $crpid)
		{
			$crp = Model_Content_Release_Plus::find($crpid);
			if (!$crp) continue;
			$crp->delete();
		}
	}

	public function enable()
	{
		$raw = $this->cdb->raw_data_object();
		$raw->crp = array();

		foreach ($this->providers as $provider)
		{
			// we check if it already exists and bail out if it does
			$crp = Model_Content_Release_Plus::find_content_with_provider(
				$this->cdb->content_id, $provider);
			if ($crp && $crp->is_confirmed) continue;
			if ($crp) $crp->delete();
			
			$crp = new Model_Content_Release_Plus();
			$crp->content_id = $this->cdb->content_id;
			$crp->provider = $provider;
			$crp->is_confirmed = 0;
			$crp->is_bundled = 1;
			$crp->save();

			$raw->crp[$provider] = $crp->id;
		}
		
		$this->cdb->raw_data($raw);
		$this->cdb->save();
	}

	public function confirm()
	{
		$this->cdb->is_confirmed = 1;
		$this->cdb->save();

		foreach ($this->cdb->raw_data_object()->crp as $crpid)
		{
			$crp = Model_Content_Release_Plus::find($crpid);
			if (!$crp) continue;
			$crp->is_confirmed = 1;
			$crp->save();
		}
	}

	public function customize(Raw_Data $raw)
	{
		// ------------------
	}

	public function providers()
	{
		return $this->providers;
	}

	public function has_provider($provider)
	{
		return in_array($provider, $this->providers);
	}

}

