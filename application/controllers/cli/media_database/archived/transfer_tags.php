<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Transfer_Tags_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = true;
		
	public function index()
	{
		$sql = "SELECT * FROM nr_contact_keyword_builder
			WHERE is_tags_transferred = 0 AND is_api_queried = 1
			AND tags IS NOT NULL
			LIMIT 1";
			
		while (true)
		{
			set_time_limit(300);
			$dbr = $this->db->query($sql);
			$model = Model_Contact_Keyword_Builder::from_db($dbr);
			if (!$model) break;
			
			// mock contact to add tags 
			$contact = new Model_Contact();
			$contact->id = $model->contact_id;
			$tags = json_decode($model->tags);
			$contact->add_tags($tags);
			
			$model->is_tags_transferred = 1;
			$model->save();
			
			$this->trace($model->contact_id, 
				count($tags));
		}
	}

}

?>
