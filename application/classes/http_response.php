<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HTTP_Response {

	public $headers;
	public $data;

	public function header($name)
	{
		$name = preg_quote($name);
		foreach ($this->headers as $header)
			if (preg_match("#^{$name}:((.|\s)*)\$#is", $header, $match))
				return trim($match[1]);
	}

}

?>