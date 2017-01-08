<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class CB_Categories_Controller extends CLI_Base { // for crunchbase categories
	
	public function index()
	{		
		error_reporting(E_ALL);
		$url = "http://api.crunchbase.com/v/2/categories?user_key=bfc43d0c11fc0227d6f98c6103f42c15";
		$result = @file_get_contents($url);
		$result = json_decode($result);
		foreach ($result->data->items as $item)
		{
			$cat = new Model_CB_Category();
			$cat->name = $item->name;
			$cat->uuid = $item->uuid;
			$cat->path = $item->path;
			$cat->date_created = date("Y-m-d H:i:s", $item->created_at);
			$cat->date_updated = date("Y-m-d H:i:s", $item->updated_at);;
			$cat->num_organizations = $item->number_of_organizations;			
			$cat->save();
		}
	}
	
}

?>
