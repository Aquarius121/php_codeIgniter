<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Localities extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $locality_id)
		{
			$locality_id = (int) $locality_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.locality_id = {$locality_id}";
		}
		
		return $this->or_builder;
	}
}

?>