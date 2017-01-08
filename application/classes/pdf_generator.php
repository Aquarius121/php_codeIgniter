<?php

class PDF_Generator {

	protected $url = null;
	protected $cover = null;
	protected $out = null;
	protected $page_width = 210;
	protected $page_height = 297;
	protected $zoom = 1;
	
	public static function from_file($file)
	{
		$instance = new static(null);
		$instance->out = $file;
		return $instance;
	}
	
	public function __construct($url)
	{
		$this->url = $url;
	}
	
	public function set_cover($url)
	{
		$this->cover = $url;
	}

	public function set_page_size($width, $height)
	{
		$this->page_width = (int) $width;
		$this->page_height = (int) $height;
	}

	public function set_zoom_level($zoom)
	{
		$this->zoom = (float) $zoom;
	}
	
	public function generate()
	{
		$ci =& get_instance();
			
		$this->out = File_Util::buffer_file();
		$content_url = escapeshellarg($this->url);
		$out_file = escapeshellarg($this->out);
		$executable = 'application/binaries/wkhtmltopdf/convert';
		$secret_file = escapeshellarg($ci->conf('auth_secret_file'));
		$request_version = $ci->conf('request_version');
		$cover_arg = null;
		
		if ($this->cover)
		{
			$cover_url = escapeshellarg($this->cover);
			$cover_arg = sprintf('cover %s', $cover_url);
		}
		
		$command = '%s %s --zoom %0.3f --quiet \
			--dpi 96 --disable-smart-shrinking \
			--page-width %d --page-height %d \
			--margin-bottom 5 --margin-top 5 \
			--margin-left 5 --margin-right 5 \
			--cookie use_v%d 1 \
			--post-file auth-secret %s \
			%s %s 2>/dev/null';

		$command = sprintf($command, $executable, 
			$cover_arg, $this->zoom, 
			$this->page_width, $this->page_height, 
			$request_version, $secret_file, 
			$content_url, $out_file);
		
		if (isset($ci->session))
			$ci->session->close();
		shell_exec($command);
		return $this->out;
	}
	
	public function deliver($name = null)
	{
		ob_clean();
		if ($name === null)
			$name = 'report.pdf';
		$type = 'application/pdf';
		$size = filesize($this->out);
		$ci =& get_instance();
		$ci->force_download($name, $type, $size);
		readfile($this->out);
		unlink($this->out);
		exit();
	}
	
	public function indirect()
	{
		$token = md5(microtime(true));
		$session_name = "download_token_{$token}";
		$download_url = "shared/download/pdf/{$token}";
		Data_Cache_ST::write($session_name, $this->out);
		return $download_url;
	}

}

?>