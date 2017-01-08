<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media_Database_Filter_Beats extends Media_Database_Filter
{
	public function build($values)
	{
		if (!$values) return 1;
		
		foreach ($values as $beat_id)
		{
			$beat_id = (int) $beat_id;
			$this->or_builder = "{$this->or_builder} 
				OR c.beat_1_id = {$beat_id}";
			$this->or_builder = "{$this->or_builder}
				OR c.beat_2_id = {$beat_id}";
			$this->or_builder = "{$this->or_builder}
				OR c.beat_3_id = {$beat_id}";
		}
		
		return $this->or_builder;
	}
}

?>