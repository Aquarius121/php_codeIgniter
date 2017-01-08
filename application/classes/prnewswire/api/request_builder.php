<?php

class PRNewswire_API_Request_Builder {

	// NOTE: THIS IS NOT COMPLIANT WITH RFC2387
	// DESPITE PRODUCING SIMILAR RESULTS.
	// THE PRN API DOESN'T WORK WITH RFC2387.

	protected $boundary = null;
	protected $buffer = array();

	// only works well with 
	// Windows new lines
	const PRN_EOL = "\r\n";

	public function __construct()
	{
		// the header is actually
		// part of the message for PRN
		// instead of request headers
		$this->boundary = md5(microtime());
		$this->buffer[] = sprintf(
			'Content-Type: multipart/related; boundary=--%s;',
			$this->boundary);

		$this->buffer[] = static::PRN_EOL;
	}

	protected function _add_boundary()
	{
		$this->buffer[] = sprintf('--%s', $this->boundary);
		$this->buffer[] = static::PRN_EOL;
	}

	protected function _add_header($name, $value)
	{
		$this->buffer[] = sprintf('%s: %s', $name, $value);
		$this->buffer[] = static::PRN_EOL;
	}

	public function add_string($string, $id, $mime)
	{
		$this->_add_boundary();
		$this->_add_header('Content-Type', $mime);
		$this->_add_header('Content-ID', $id);

		$this->buffer[] = $string;
		$this->buffer[] = static::PRN_EOL;
	}

	public function add_file($file, $id, $mime = null)
	{
		if ($mime === null)
			$mime = File_Util::detect_mime($file);

		$binary = file_get_contents($file);
		$base64 = base64_encode($binary);

		$this->_add_boundary();
		$this->_add_header('Content-Type', 'application/jpeg');
		$this->_add_header('Content-Transfer-Encoding', 'Base64');
		$this->_add_header('Content-ID', $id);
		$this->_add_header('Content-Length', strlen($base64));

		$this->buffer[] = $base64;
		$this->buffer[] = static::PRN_EOL;
	}

	public function body()
	{
		$buffer = $this->buffer;
		return implode($buffer);
	}
	
}