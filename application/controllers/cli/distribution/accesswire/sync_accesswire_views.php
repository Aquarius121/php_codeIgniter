<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Sync_Accesswire_Views_Controller extends CLI_Base {

	public function index()
	{
		$accesswire = Accesswire_Scraper_Factory::create();
		$acw = $this->get_accesswire_content();

		foreach ($acw as $acw_model) 
		{
			$views = $accesswire->get_views(intval($acw_model->accesswire_id));
			if (!$views || $views == $acw_model->views) continue;
			$acw_model->views = $views;
			$acw_model->save();

			sleep(5);
		}
	}

	protected function get_accesswire_content()
	{
		$criteria = array();
		$criteria[] = array('date_created', '>', Date::days(-7));
		$criteria[] = array('accesswire_id', '>', 0);
		return Model_Content_Accesswire::find_all($criteria);
	}

}
