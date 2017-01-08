<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Countries extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $country_id)
		{
			$country_id = (int) $country_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.country_id = {$country_id}";
		}
		
		return $this->or_builder;
	}
}

?>