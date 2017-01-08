<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Process_Results_Image_Variant_Trait {

	protected function process_results_image_variant($results, $id_property, $target_property, $variant)
	{
		$id_list = array();
		$indexed_results = array();
		foreach ($results as $result)
			$id_list[] = $result->{$id_property};
		if (!count($id_list)) return $results;
		$id_list_str = sql_in_list($id_list);
		
		$variant = escape_and_quote($variant);
		$sql = "SELECT i.*, si.filename, si.width, si.height FROM nr_image i
			INNER JOIN nr_image_variant iv ON iv.image_id = i.id
			INNER JOIN nr_stored_image si ON si.id = iv.stored_image_id
			WHERE i.id IN ({$id_list_str}) AND iv.name = {$variant}";
			
		$db_result = $this->db->query($sql);
		$image_set = Model_Image::from_db_all($db_result);
		$indexed_results = array();

		foreach ($results as $result)
		{
			if (isset($indexed_results[$result->{$id_property}]))
			     $indexed_results[$result->{$id_property}][] = $result;
			else $indexed_results[$result->{$id_property}] = array($result);
		}

		foreach ($image_set as $image)
			foreach ($indexed_results[$image->id] as $result)
				$result->{$target_property} = $image;
		
		return $results;
	}

}