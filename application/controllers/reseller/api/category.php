<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('reseller/api/base');

class Category_Controller extends API_Base {
	
	public function index()
	{
		$sql = "SELECT c.id AS category_id, c.name AS category_name, 
			cg.name AS category_group_name, cg.id AS category_group_id
			FROM nr_cat c INNER JOIN nr_cat cg 
			ON c.cat_group_id = cg.id 
			AND c.is_listed = 1
			AND cg.is_listed = 1
			ORDER BY cg.name ASC, c.name ASC";
				
		$query = $this->db->query($sql);
		$categories = array();
		
		foreach ($query->result() as $result)
		{
			$cat = array();
			$cat['category_id'] = $result->category_id;
			$cat['category_name'] = $result->category_name;
			$cat['category_group_name'] = $result->category_group_name;
			$categories[] = $cat;
		}
		
		$this->iella_out->categories = $categories;
	}
	
}

?>
