<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');

class Beats_Controller extends API_Base {
	
	public function index()
	{
		$sql = "SELECT b.id AS beat_id, b.name AS beat_name, 
			bg.name AS beat_group_name, bg.id AS beat_group_id
			FROM nr_beat b INNER JOIN nr_beat bg 
			ON b.beat_group_id = bg.id 
			AND b.is_listed = 1
			AND bg.is_listed = 1
			ORDER BY bg.name ASC, b.name ASC";
				
		$query = $this->db->query($sql);
		$beats = array();
		
		foreach ($query->result() as $result)
		{
			$beat = array();
			$beat['beat_id'] = $result->beat_id;
			$beat['beat_name'] = $result->beat_name;
			$beat['beat_group_name'] = $result->beat_group_name;
			$beats[] = $beat;
		}
		
		$this->iella_out->beats = $beats;
	}
	
}

?>
