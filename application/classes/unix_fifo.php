<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Unix_FIFO {

	protected $file;
	
	public function __construct($file)
	{
		if (!is_file($file))
			posix_mkfifo($file, 0666);
		$this->file = $file;
	}

	public function write($data)
	{
		$handle = fopen($this->file, 'w');
		fwrite($handle, $data); 
		fclose($handle);
	}

	public function read()
	{
		file_get_contents($this->file);
	}
	
}

?>