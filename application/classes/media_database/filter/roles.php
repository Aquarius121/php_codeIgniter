<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Roles extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $contact_role_id)
		{
			$contact_role_id = (int) $contact_role_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.contact_role_id = {$contact_role_id}";
		}
		
		return $this->or_builder;
	}
}

?>