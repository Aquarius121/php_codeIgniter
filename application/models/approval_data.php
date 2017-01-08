<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Approval_Data extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_approval_data';
	protected static $__primary = 'content_id';
	
	public static function find_or_create($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;
		
		if (!($approval = static::find($content)))
			$approval = new static();
		$approval->content_id = $content;
		$approval->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		$approval->save();
		
		// delete the most recent rejection data
		if ($rejection = Model_Rejection_Data::find($content))
			$rejection->delete();

		// delete the most recent hold data
		if ($hold = Model_Hold_Data::find($content))
			$hold->delete();
		
		return $approval;
	}
	
	public static function __delete($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;		
		if ($approval = static::find($content))
			$approval->delete();
	}
	
}

?>