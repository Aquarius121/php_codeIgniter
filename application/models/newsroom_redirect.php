<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Newsroom_Redirect extends Model {
	
	protected static $__table = 'nr_newsroom_redirect';
	protected static $__primary = 'old_slug';

	public function mock_newsroom()
	{
		$newsroom = new Model_Newsroom();
		$newsroom->name = $this->new_slug;
		return $newsroom;
	}

}