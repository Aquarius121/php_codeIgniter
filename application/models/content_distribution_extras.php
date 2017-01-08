<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Content_Distribution_Extras extends Model {

	use Raw_Data_Trait { Raw_Data_Trait::raw_data_object as raw_data_object_t; }

	const TYPE_MICROLIST  = 'MICROLIST';
	const TYPE_OTHER      = 'OTHER';
	const TYPE_PRN_IMAGES = 'PRN_IMAGES';	
	const TYPE_PRN_VIDEO  = 'PRN_VIDEO';	

	protected static $__table = 'nr_content_distribution_extras';
	protected static $__primary = 'content_id';

	public function raw_data_object()
	{
		$rdo = $this->raw_data_object_t();
		if (is_object($rdo->extras) || is_array($rdo->extras))
		     $rdo->extras = (array) $rdo->extras;
		else $rdo->extras = array();
		return $rdo;
	}

	public function add($data, $type)
	{
		$rdo = $this->raw_data_object();
		$uuid = UUID::create();
		$rdo->extras[$uuid] = new Raw_Data();
		$rdo->extras[$uuid]->uuid = $uuid;
		$rdo->extras[$uuid]->type = $type;
		$rdo->extras[$uuid]->data = $data;
		$this->raw_data($rdo);
		return $rdo->extras[$uuid];
	}

	public function get($uuid)
	{
		$rdo = $this->raw_data_object();
		if (!isset($rdo->extras[$uuid]))
			return new Raw_Data();
		return $rdo->extras[$uuid];
	}

	public function set($uuid, $extra)
	{
		$rdo = $this->raw_data_object();
		$rdo->extras[$uuid] = $extra;
		$this->raw_data($rdo);
	}

	public function remove($uuid)
	{
		$rdo = $this->raw_data_object();
		if (!isset($rdo->extras[$uuid]))
			return false;
		unset($rdo->extras[$uuid]);
		$this->raw_data($rdo);
		return true;
	}

	public function filter($type)
	{
		$matched = array();
		$rdo = $this->raw_data_object();
		foreach ($rdo->extras as $uuid => $extra)
			if ($extra->type === $type)
				$matched[$uuid] = $extra;
		return $matched;
	}
	
}