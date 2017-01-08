<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CSS_Inliner {

	protected $additional_css = null;
	protected $html = null;
	
	public function __construct($html = null, $css = null)
	{
		$this->set_html($html);
		$this->set_css($css);
	}
	
	public function set_css($css)
	{
		$this->additional_css = $css;
	}
	
	public function set_html($html)
	{
		$this->html = $html;
	}
	
	public function convert()
	{
		$emogrifier = new \Pelago\Emogrifier();
		$emogrifier->setHtml((string) $this->html);
		$emogrifier->setCss((string) $this->additional_css);
		$emogrifier->disableInvisibleNodeRemoval();
		return @$emogrifier->emogrify();
	}
	
}