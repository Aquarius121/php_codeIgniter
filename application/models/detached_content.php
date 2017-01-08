<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Detached_Content extends Model_Content {
	
	public $__detached_beats = array();
	public $__detached_images = array();
	public $__detached_tags = array();
	
	public function save() {}
	public function reload() {}
	public function values($values = null) {}
	public function delete() {}
	public function load_content_data() {}
	public function load_local_data() {}
	public function load_data() {}
	
	public function set_tags($values)
	{
		foreach ($values as $k => &$value)
			if (!($value = trim($value))) unset($values[$k]);
		$this->__detached_tags = $values;
	}
	
	public function set_images($values)
	{
		foreach ($values as &$value)
			if ($value instanceof Model_Image) 
				$value = $value->id;
		$this->__detached_images = $values;
	}

	public function set_beats($values)
	{
		foreach ($values as &$value)
			if ($value instanceof Model_Beat) 
				$value = $value->id;
		$this->__detached_beats = $values;
	}
	
	public function get_tags()
	{
		return $this->__detached_tags;
	}
	
	public function get_images()
	{
		$image_ids = $this->__detached_images;
		if (!count($image_ids)) return array();
		
		$image_ids_str = sql_in_list($image_ids);
		$query = $this->db->query("SELECT i.* FROM nr_image i 
			WHERE i.id IN ({$image_ids_str})");
		
		$images = Model_Image::from_db_all($query);
		return $images;
	}

	public function get_beats()
	{
		$beat_ids = $this->__detached_beats;
		if (!count($beat_ids)) return array();
		
		$beat_ids_str = sql_in_list($beat_ids);
		$query = $this->db->query("SELECT b.* FROM nr_beat b 
			WHERE b.id IN ({$beat_ids_str})");
		
		$beats = Model_Beat::from_db_all($query);
		return $beats;
	}

	public static function from_model_content($m_content)
	{
		$instance = new static();
		foreach ($m_content as $k => $value)
			$instance->$k = $value;
		$instance->set_beats($m_content->get_beats());
		$instance->set_images($m_content->get_images());
		$instance->set_tags($m_content->get_tags());
		return $instance;
	}
	
}