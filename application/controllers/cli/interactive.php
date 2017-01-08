<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Interactive_Controller extends CLI_Base {
	
	public function index()
	{
		error_reporting(E_ALL);

		while (true)
		{
			$__readline = readline('> ');
			readline_add_history($__readline);
			try { eval($__readline); }	catch (Exception $e)
			{ echo $e->getTraceAsString(); }
			
			$content = ob_get_contents();
			ob_clean();
			
			// have we output something but forgotten new line?
			if ($content && !preg_match('#[\r\n]$#s', $content))
			     echo $content, PHP_EOL;
			else echo $content;
			
			$this->__flush();
		}
	}
	
}

?>
