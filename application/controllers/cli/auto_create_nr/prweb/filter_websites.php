<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');
// this cron filters website names from long website urls
class Filter_Websites_Controller extends CLI_Base { 

	public function index()
	{
		error_reporting(E_ALL);
		
		$ci =& get_instance();
		$cnt = 1;

		$sql = "SELECT * FROM ac_nr_prweb_company_data 
				WHERE is_website_read = 0
				AND website IS NOT NULL
				LIMIT 1";


		while ($cnt <= 5000)
		{
			$cnt++;
			
			$result = $this->db->query($sql);
			if (!$result->num_rows()) break;
			
			$c_data = Model_PRWeb_Company_Data::from_db($result);
			if (!$c_data) break;

			$info = parse_url($c_data->website);
			$host = $info['host'];
			$new_web = $info['scheme']."://".$info['host'];
			
			$c_data->website = $new_web;
			$c_data->is_website_read = 1;
			$c_data->save();
		}

		
	}
}

?>
