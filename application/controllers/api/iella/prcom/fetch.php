<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Fetch_Controller extends PRCom_API_Base {
	
	public function countries()
	{
		$order = array('name', 'asc');
		$criteria = array('is_common', 1);
		$this->iella_out->common_countries = Model_Country::find_all($criteria, $order);
		$this->iella_out->countries = Model_Country::find_all(null, $order);
		$this->iella_out->country_ID_UNITED_STATES = Model_Country::ID_UNITED_STATES;
		$this->iella_out->country_ID_UNITED_KINGDOM = Model_Country::ID_UNITED_KINGDOM;
		$this->iella_out->country_ID_CANADA = Model_Country::ID_CANADA;
	}

	public function states()
	{
		$order = array('name', 'asc');
		$criteria = array('country_id', Model_Country::ID_UNITED_STATES);
		$this->iella_out->states = Model_Region::find_all($criteria, $order);
	}

	public function regions()
	{
		$this->iella_out->regions = Model_Region::find_all();
	}

	public function categories()
	{
		$this->iella_out->categories = Model_Cat::list_all_cats_by_group();
	}

	public function beats()
	{
		$this->iella_out->beats = Model_Beat::list_all_beats_by_group();
	}
	
}

?>