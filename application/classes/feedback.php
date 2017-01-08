<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feedback
{
	public $html;
	public $status;
	public $title;

	public $enable_inline = true;
	public $enable_alert = false;
	
	// success, error, warning, info
	public function __construct($status, $title = '', $text = '')
	{
		$status_maps = array(
			'warn' => 'warning',
			'error' => 'danger',
			'fatal' => 'danger',
		);

		if (isset($status_maps[$status]))
			$status = $status_maps[$status];
		$this->status = $status;

		if ($title)
		{
			$this->title = $title;
		}
		
		if ($text)
		{
			$text = (new View_Data)->esc($text);
			$text = preg_replace('#[\r\n\t]+#', ' ', $text);
			$this->html = $text;
		}
	}
	
	public function set_title($title)
	{
		$this->title = $title;
	}
	
	public function set_text($text)
	{
		$this->html = null;
		$this->add_text($text);
	}
	
	public function add_text($text, $new_line = false)
	{
		// separate with a new line or just a space?
		$join = $new_line ? '<br>' : ' ';
		$escaped = (new View_Data)->esc($text);
		if (strlen($this->html))
		     $html = concat($this->html, $join, $escaped);
		else $html = $escaped;
		$html = preg_replace('#[\r\n\t]+#', ' ', $html);
		$this->html = $html;
	}

	public function set_html($html)
	{
		// separate with a new line or just a space?
		$this->html = $html;
	}

	public function enable_alert()
	{
		$this->enable_alert = true;
	}

	public function disable_alert()
	{
		$this->enable_alert = false;
	}

	public function enable_inline()
	{
		$this->enable_inline = true;
	}

	public function disable_inline()
	{
		$this->enable_inline = false;
	}
	
	public function render()
	{
		$ci =& get_instance();
		return $ci->load->view('partials/feedback', 
			array('feedback' => $this), true);
	}

	public function alert_object()
	{
		$ci =& get_instance();
		$view = 'partials/feedback-alert-object';
		$message = $ci->load->view($view, 
			array('feedback' => $this), true);
		$ob = new stdClass;
		$ob->className = sprintf('bootbox-%s',
			$this->status);
		$ob->message = $message;
		return $ob;
	}
	
	public function __toString()
	{
		return $this->render();
	}

}