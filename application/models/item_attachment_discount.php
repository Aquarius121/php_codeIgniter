<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Item_Attachment_Discount extends Model {
	
	protected static $__table = 'co_item_attachment_discount';

	public static function find_attached($item, $attached)
	{
		if (!count($attached))
			return null;

		if ($item instanceof Cart_Item)
			$item = $item->item_id;
		if ($item instanceof Model_Item)
			$item = $item->id;
		$item = (int) $item;

		foreach ($attached as $k => $atd)
		{
			if ($atd instanceof Cart_Item)
				$attached[$k] = $atd->item_id;
			if ($atd instanceof Model_Item)
				$attached[$k] = $atd->id;
			$attached[$k] = (int) 
				$attached[$k];
		}

		$table = static::$__table;
		$attached_in_list = sql_in_list($attached);

		$sql = "SELECT * FROM {$table} WHERE 
			item_id = {$item} AND 
			(attached_1_id IS NULL OR attached_1_id IN ($attached_in_list)) AND
			(attached_2_id IS NULL OR attached_2_id IN ($attached_in_list)) AND
			(attached_3_id IS NULL OR attached_3_id IN ($attached_in_list))
			ORDER BY discount DESC LIMIT 1";

		$dbr = static::__db()->query($sql);
		$instance = static::from_db($dbr);
		return $instance;
	}
	
}

?>