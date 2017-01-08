<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feedback_View {

	protected $view;
	
	public function __construct($view)
	{
		$this->view = $view;
	}
	
	public function render()
	{
		$ci =& get_instance();
		return $ci->load->view($this->view, null, true);
	}
	
	public function __toString()
	{
		return $this->render();
	}
	
}
