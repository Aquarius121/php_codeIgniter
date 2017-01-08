<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Rejection_Data extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_rejection_data';
	protected static $__primary = 'content_id';
	
	public static function find_or_create($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;
		
		if (!($rejection = static::find($content)))
			$rejection = new static();
		$rejection->content_id = $content;
		$rejection->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		$rejection->save();
		
		// delete the most recent approval data
		if ($approval = Model_Approval_Data::find($content))
			$approval->delete();

		// delete the most recent hold data
		if ($hold = Model_Hold_Data::find($content))
			$hold->delete();
		
		return $rejection;
	}
	
	public static function __delete($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;		
		if ($rejection = static::find($content))
			$rejection->delete();
	}
	
}

?>