<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Beat_Group extends Model {
	
	protected static $__table = 'nr_beat_group';
	
	public function beat()
	{
		return Model_Beat::find($this->id);
	}
	
}

?>