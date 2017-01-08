<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Regions extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $region_id)
		{
			$region_id = (int) $region_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.region_id = {$region_id}";
		}
		
		return $this->or_builder;
	}
}

?>