<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request_Data {

	protected $__data;
	protected $__key;

	public function __set($name, $value)
	{
		$this->__data->{$name} = $value;
	}

	public function __get($name)
	{
		if (isset($this->__data->{$name}))
			return $this->__data->{$name};
		return null;
	}
	
	public static function load(CI_Input $input)
	{
		$key = $input->get('request_data_key');
		if (!$key) return new Raw_Data();
		$rdata = Data_Cache_ST::read_object($key);
		if (!$rdata) return new Raw_Data();
		return Raw_Data::from_object($rdata);
	}

	public function __construct()
	{
		$this->__data = new stdClass();
		$this->__key = md5(UUID::create());
		$this->__data->__key = $this->__key;
	}

	public function save()
	{
		Data_Cache_ST::write_object($this->__key, $this->__data);
	}

	public function key()
	{
		return $this->__key;
	}

	public function insert_into_query_string($url)
	{
		return insert_into_query_string($url, array(
			'request_data_key' => $this->__key,
		));
	}
	
}