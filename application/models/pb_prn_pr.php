<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_PB_PRN_PR extends Model {
	
	protected static $__table = 'nr_pb_prn_pr';
	protected static $__primary = 'content_id';

	use Raw_Data_Trait;
	
	public function map_categories()
	{
		$raw_data = $this->raw_data();
		if (empty($raw_data))
			return true;

		$categories = $raw_data->categories;

		if (empty($categories))
			return true;

		$filters = array();
		foreach ($categories as $category)
			$filters[] = "(cg.slug = '{$category->group_slug}' AND c.name = \"{$category->name}\")";

		if (!count($filters))
			return false;

		$filter = implode(" OR ", $filters);

		$sql = "SELECT c.*
				FROM ac_nr_prn_category c
				INNER JOIN ac_nr_prn_category_group cg
				ON c.prn_category_group_id	= cg.id
				WHERE {$filter}";

		$results = static::from_sql_all($sql);
		
		$beats = array();
		foreach ($results as $result)
		{
			if ($result->beat_1_id)
				$beats[] = $result->beat_1_id;

			if ($result->beat_2_id)
				$beats[] = $result->beat_2_id;

			if ($result->beat_3_id)
				$beats[] = $result->beat_3_id;

			if ($result->beat_4_id)
				$beats[] = $result->beat_4_id;

			if ($result->beat_5_id)
				$beats[] = $result->beat_5_id;			
		}

		if (!count($beats))
			return false;

		$beats = array_unique($beats);

		$m_content = Model_Content::find($this->content_id);
		$m_content->set_beats($beats);
	}
}