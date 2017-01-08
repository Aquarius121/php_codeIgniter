<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Cat_To_Beat extends Model {
	
	protected static $__table = 'nr_cat_to_beat';
	protected static $__primary = array('cat_id', 'beat_id');
	
	public static function find_pair($cat, $beat)
	{
		if ($cat instanceof Model_Cat) $cat = $cat->id;
		if ($beat instanceof Model_Beat) $beat = $beat->id;
		return static::find(array(
			array('cat_id', $cat),
			array('beat_id', $beat),
		));
	}

	public static function beats($cat)
	{
		if ($cat instanceof Model_Cat)
			$cat = $cat->id;
		$cat = (int) $cat;
		$table = static::$__table;
		$sql = "SELECT b.* FROM nr_beat b INNER JOIN 
			{$table} ctb ON ctb.beat_id = b.id
			WHERE ctb.cat_id = {$cat}";
		return Model_Beat::from_sql_all($sql);
	}
	
}

?>