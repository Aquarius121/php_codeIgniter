<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Coverages extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $contact_coverage_id)
		{
			$contact_coverage_id = (int) $contact_coverage_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.contact_coverage_id = {$contact_coverage_id}";
		}
		
		return $this->or_builder;
	}
}

?>