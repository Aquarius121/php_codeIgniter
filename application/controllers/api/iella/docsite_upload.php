<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class DocSite_Upload_Controller extends Iella_Base {
	
	public function issuu()
	{
		$file = $this->iella_in->file;
		$name = $this->iella_in->name;
		$title = $this->iella_in->title;
		$issuu = new DocSite_Issuu();
		$url = $issuu->upload($file, $name, $title);
		$this->iella_out->url = $url;
	}
	
	public function scribd()
	{
		$file = $this->iella_in->file;
		$name = $this->iella_in->name;
		$title = $this->iella_in->title;
		$scribd = new DocSite_Scribd();
		$url = $scribd->upload($file, $name, $title);
		$this->iella_out->url = $url;
	}
	
}

?>