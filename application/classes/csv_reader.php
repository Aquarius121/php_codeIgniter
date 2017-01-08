<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CSV_Reader {
	
	protected $filename;
	protected $handle;
	
	public function __construct($filename)
	{
		$this->filename = $filename;

		// some files coming from mac v9 use just \r
		// instead of \r\n or \n and were not working 
		// correctly. solution: auto-detect the line endings
		ini_set('auto_detect_line_endings', true);
		$this->handle = fopen($filename, 'r');
		ini_set('auto_detect_line_endings', false);
	}
	
	public function handle()
	{
		return $this->handle;
	}
	
	public function read()
	{
		if (feof($this->handle)) 
		{
			fclose($this->handle);
			return;
		}
		
		$line = fgetcsv($this->handle);
		return $line;
	}
	
	public function close()
	{
		if (is_resource($this->handle))
			fclose($this->handle);
	}
	
}

?>