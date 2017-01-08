<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Download_Controller extends CIL_Controller {

	protected $ssl_optional = true;
	protected $ssl_required = false;
	protected $ssl_required_post = false;

	protected function file($token)
	{
		$session_name = "download_token_{$token}";
		$file = Data_Cache_ST::read($session_name);
		Data_Cache_ST::delete($session_name);
		return $file;
	}
	
	public function pdf($token)
	{
		if (!($file = $this->file($token))) return;
		$pdf = PDF_Generator::from_file($file);
		$pdf->deliver();
	}

}

?>