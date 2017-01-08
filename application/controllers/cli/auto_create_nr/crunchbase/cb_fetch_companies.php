<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
load_controller('shared/cb_api'); //crunchbase API

class CB_Fetch_Companies_Controller extends CLI_Base { // for crunchbase companies
	
	use CB_API_Trait;
	
	public function index()
	{
		error_reporting(E_ALL);
		set_memory_limit('2048M');
		set_time_limit(300);
		
		$user_key = $this->select_api_key();
		if (empty($user_key))
		{
			echo "keys used";
			return;
		}

		//$user_key = "bfc43d0c11fc0227d6f98c6103f42c15";

		$cnt = 1;

		while ($cnt <= 1)
		{
			$cnt++;

			$sql = "SELECT * FROM ac_nr_cb_category
					WHERE pages_scanned < ceil(num_organizations/1000)
					ORDER BY num_organizations 
					LIMIT 1";

			if ( ! $dbr = $this->db->query($sql))
				break;

			$cat = Model_CB_Category::from_db($dbr);
			$cat_uuid = $cat->uuid;
			$t_pages = ceil($cat->num_organizations / 1000); // every call returns a max of 1000 results

			if ($cat->pages_scanned >= $t_pages)
				continue;

			$page = (int) $cat->pages_scanned;
			$page++;
			$url = "http://api.crunchbase.com/v/2/";
			$url = "{$url}organizations?category_uuids={$cat_uuid}&user_key={$user_key}&page={$page}";
			$result = @file_get_contents($url);

			$this->log_key_usage($user_key);
			
			$result = json_decode($result);		
			
			foreach ($result->data->items as $item)
			{
				if ($comp = Model_CB_Company::find('name', $item->name))
					continue;
				$comp = new Model_CB_Company();
				$comp->name = $item->name;
				$comp->path = $item->path;
				$comp->date_created = date("Y-m-d H:i:s", $item->created_at);
				$comp->date_updated = date("Y-m-d H:i:s", $item->updated_at);;
				$comp->date_fetched = Date::$now->format(DATE::FORMAT_MYSQL);
				$comp->cb_category_id = $cat->id;
				$comp->save();
			}

			$cat->pages_scanned = $page;
			$cat->save();
			sleep(10);
		}
		
	}
	
}

?>
