<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_MOT_Writer extends Model_Base {
	
	protected static $writers;
			
	public static function find_all()
	{
		$request = new MOT_Iella_Request();
		$result = $request->send('mot_writers/get_all_writers');
		if (!@$request->response->success) return array();
		$writers = $request->response->writers;
		foreach ($writers as $k => $writer)
			$writers[$k] = static::from_object($writer);
		static::$writers = $writers;
		return $writers;
	}
	
	public static function find($id)
	{
		if (!$id) return null;
		if (!static::$writers)
			static::find_all();
		$writers = static::$writers;
		foreach ($writers as $writer)
			if ($writer->id == $id)
				return $writer;
		return null;
	}
	
	public function name()
	{
		return implode(' ', array($this->first_name, $this->last_name));
	}
	
	public function reset_password()
	{
		$request = new MOT_Iella_Request();
		$request->data->id = $this->id;
		$request->send('mot_writers/reset_password');
		if (!@$request->response->success) return false;
		$password = $request->response->password;
		return $password;
	}
	
	public function save()
	{
		$request = new MOT_Iella_Request();
		$request->data->id = $this->id;
		$request->data->email = $this->email;
		$request->data->notes = $this->notes;
		$request->data->first_name = $this->first_name;
		$request->data->last_name = $this->last_name;
		$request->data->is_enabled = $this->is_enabled;
		$response = $request->send('mot_writers/save_writer');
		if ($response->success && $response->writer_id)
			$this->id = $response->writer_id;
		return $response;
	}
	
	// TODO: move this to somewhere more suitable
	public static function fetch($chunkination, $filter)
	{
		$request = new MOT_Iella_Request();
		$request->data->chunkination = $chunkination;
		$request->data->filter = $filter;
		$request->data->limit_str = $chunkination->limit_str();
		$r = $request->send('mot_writers/get_writers_for_admin_area');		
		return $request->response;
	}
	
}

?>