<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');

class Info_Controller extends Iella_Base {
	
	public function index()
	{
		$this->iella_out->env = new stdClass();
		$this->iella_out->env->environment = $this->env['environment'];
		$this->iella_out->env->ssl_enabled = $this->env['ssl_enabled'];
		$this->iella_out->env->host = $this->env['host'];
		$this->iella_out->env->cwd = $this->env['cwd'];
		$this->iella_out->env->rewrites = $this->env['rewrites'];
		$this->iella_out->env->requested_uri = $this->env['requested_uri'];
		$this->iella_out->env->user_agent = $this->env['user_agent'];
		$this->iella_out->env->remote_addr = $this->env['remote_addr'];
		$this->iella_out->input = $this->iella_in;
		$this->iella_out->files = $this->iella_files;
	}
	
}

?>