<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CSV_Writer {
	
	protected $filename;
	protected $handle;

	// outputs a 2D array in CSV format
	public static function dump($arr, $file = null)
	{
		// default to STDOUT
		if ($file === null) 
			$file = 'php://stdout';

		// array of lines, each line must be an array
		if (!is_array($arr) || (!is_array($arr[0]) && !is_object($arr[0])))
			throw new InvalidArgumentException();
		
		$writer = new static();
		foreach ($arr as $line)
			$writer->write($v);
		$writer->close();
	}
	
	public function __construct($filename = null)
	{
		// default to STDOUT
		if ($filename === null) 
			$filename = 'php://stdout';

		$this->filename = $filename;
		$this->handle = fopen($filename, 'w');
	}
	
	public function handle()
	{
		return $this->handle;
	}
	
	public function write($data)
	{
		if (feof($this->handle)) 
		{
			fclose($this->handle);
			return;
		}
		
		$res = fputcsv($this->handle, $data);
		return $res !== false;
	}
	
	public function close()
	{
		fclose($this->handle);
	}
	
}