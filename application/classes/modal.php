<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Modal {
	
	public $auto_show = false;
	public $content;
	public $content_view;
	public $footer;
	public $footer_view;
	public $header;
	public $header_view;
	public $id;
	public $title;
	
	public function __construct()
	{
		$this->id = substr(md5(microtime()), 0, 8);
		$this->id = "m{$this->id}";
	}
	
	public function set_content($content)
	{
		$this->content = $content;
	}

	public function set_content_view($view)
	{
		$this->content_view = $view;
	}
	
	public function set_footer($footer)
	{
		$this->footer = $footer;
	}

	public function set_footer_view($view)
	{
		$this->footer_view = $view;
	}

	public function set_header($header)
	{
		$this->header = $header;
	}

	public function set_header_view($view)
	{
		$this->header_view = $view;
	}
	
	public function set_title($title)
	{
		$this->title = $title;
	}
	
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	public function auto_show($auto_show)
	{
		$this->auto_show = $auto_show;
	}
	
	public function render($width, $height)
	{
		$ci =& get_instance();

		if ($this->header_view && !$this->header)
			$this->header = $ci->load->view_return($this->header_view);
		if ($this->content_view && !$this->content)
			$this->content = $ci->load->view_return($this->content_view);
		if ($this->footer_view && !$this->footer)
			$this->footer = $ci->load->view_return($this->footer_view);

		$view_data = array();
		$view_data['width'] = (int) $width;
		$view_data['height'] = (int) $height;
		$view_data['content'] = $this->content;
		$view_data['footer'] = $this->footer;
		$view_data['header'] = $this->header;
		$view_data['title'] = $this->title;
		$view_data['as'] = $this->auto_show;
		$view_data['id'] = $this->id;		
		return $ci->load->view('shared/partials/modal.php',
			$view_data, true);
	}
	
}
