<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stored_Image extends Stored_File {
	
	const SAFE_EXTENSION = 'jpg';

	const EXT_GIF  = 'gif';
	const EXT_JPEG = 'jpeg';
	const EXT_JPG  = 'jpg';
	const EXT_PNG  = 'png';

	public $width;
	public $height;
	
	protected $image;
	
	protected static $__supported_exts = array(

		self::EXT_GIF, 
		self::EXT_JPEG,
		self::EXT_JPG, 
		self::EXT_PNG, 
		
	);

	public static function parse_extension($filename, $default = null)
	{
		$extension = parent::parse_extension($filename, $default);
		return $extension;
	}

	public function image()
	{
		if ($this->image) return $this->image;
		return Image::from_file($this_file);
	}
		
	public function is_valid_image()
	{
		if ($this->__moved) 
			return Image::is_valid_file($this->destination);
		return Image::is_valid_file($this->source);
	}
	
	public function from_this_resized($width = null, $height = null, $cropped = false, $format = Image::FORMAT_JPEG)
	{
		if (is_object($width))
		{
			$v_size = $width;
			$width = null;
			
			if (isset($v_size->height))
				$height = $v_size->height;
			if (isset($v_size->width))
				$width = $v_size->width;
			if (isset($v_size->format))
				$format = $v_size->format;
		}
		
		$this_file = $this->__moved ? 
			$this->destination : 
			$this->source;
			
		$image = Image::from_file($this_file);
		$image->cropped($cropped);
		$image->height($height);
		$image->width($width);
		$image->format($format);
		
		if (isset($v_size))
			$image->set($v_size);
		
		$buffer_file = File_Util::buffer_file();
		$image->save($buffer_file);

		$stored_image = new static();
		$stored_image->source = $buffer_file;
		$stored_image->extension = $format;
		$stored_image->image = $image;
		$stored_image->generate_filename();
		$stored_image->move();
		
		return $stored_image;
	}
	
	public function save_to_db()
	{
		if (!$this->__moved)
			$this->move();
			
		$ci =& get_instance();
		$im = Image::from_file($this->destination);				
		$ci->db->query("INSERT IGNORE INTO nr_stored_image 
			(filename, width, height) VALUES (?, ?, ?)",
			array($this->filename, $im->width(), $im->height()));
		
		if (!($id = $ci->db->insert_id()))
		{
			$query = $ci->db->query("SELECT id FROM nr_stored_image 
				WHERE filename = ?", array($this->filename));
			$result = $query->row();
			$this->id = $id = $result->id;
		}
		
		return $id;
	}
	
	public static function load_data_from_db($id)
	{
		$ci =& get_instance();
		$data = array('id' => $id);
		$result = $ci->db->get_where('nr_stored_image', $data);
		return $result->row();
	}
	
}