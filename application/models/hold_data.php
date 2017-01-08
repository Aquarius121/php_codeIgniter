<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Hold_Data extends Model {
	
	use Raw_Data_Trait;
	
	protected static $__table = 'nr_hold_data';
	protected static $__primary = 'content_id';
	
	public static function find_or_create($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;
		
		if (!($hold = static::find($content)))
			$hold = new static();
		$hold->content_id = $content;
		$hold->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		$hold->save();
		
		return $hold;
	}
	
	public static function __delete($content)
	{
		if ($content instanceof Model_Content)
			$content = $content->id;
		if ($hold = static::find($content))
			$hold->delete();
	}

	public function add_comment($comment)
	{
		$hrd = $this->raw_data();
		if (!$hrd) $hrd = new Raw_Data();

		if ($hrd->comments)
		{
			$hrd->comments = concat(
				$hrd->comments, PHP_EOL, 
				str_repeat('-', 40), PHP_EOL, 
				$comment);
		}
		else
		{
			$hrd->comments = $comment;
		}

		$this->raw_data($hrd);
	}
	
}

?>