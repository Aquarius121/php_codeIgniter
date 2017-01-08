<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Media_Types extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $contact_media_type_id)
		{
			$contact_media_type_id = (int) $contact_media_type_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.contact_media_type_id = {$contact_media_type_id}";
		}
		
		return $this->or_builder;
	}
}

?>