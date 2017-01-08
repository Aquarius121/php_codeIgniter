<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_Credit_Consumer extends Credit_Consumer {
	
	public function consume($count)
	{
		foreach ($this->sorted() as $reserve)
		{
			$count -= $reserve->consume($count);
			if ($count <= 0) break;
		}
	}
	
}

?>