<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');

class Country_Controller extends API_Base {
	
	public function index()
	{
		$m_countries = Model_Country::find_all(null, array('name', 'asc'));		
		$countries = array();
		
		foreach ($m_countries as $m_country)
		{
			$country = array();
			$country['country_id'] = $m_country->id;
			$country['country_name'] = $m_country->name;
			$countries[] = $country;
		}
					
		$this->iella_out->countries = $countries;
	}
	
}

?>
