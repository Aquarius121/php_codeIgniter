<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_ZIPs extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $zip)
		{
			$zip = $this->db->escape($zip);
			$this->or_builder = "{$this->or_builder} 
				OR c.zip like {$zip}";
		}
		
		return $this->or_builder;
	}
}

?>