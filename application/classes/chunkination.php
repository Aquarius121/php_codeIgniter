<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Chunkination {
	
	private $chunk_size = 10;
	private $chunk;
	private $total;
	private $url_format;
	
	public function __construct($chunk)
	{
		$this->set_chunk($chunk);
		$this->total = 0;
	}

	public function set_chunk($chunk)
	{
		if (!$chunk) $chunk = 1;
		$this->chunk = $chunk - 1;
	}
	
	public function set_chunk_size($value)
	{
		$this->chunk_size = $value;
	}
	
	public function set_url_format($value)
	{
		$this->url_format = $value;
	}
	
	public function set_total($value)
	{
		$this->total = $value;
	}
	
	public function offset()
	{
		return $this->chunk * $this->chunk_size;
	}
	
	public function chunk_size()
	{
		return $this->chunk_size;
	}
	
	public function total()
	{
		return $this->total;
	}
	
	public function is_out_of_bounds()
	{
		if ($this->chunk === 0) return false;
		if ($this->chunk < 0) return true;
		if ($this->offset() >= $this->total) return true;
		return false;
	}
	
	public function limit_str()
	{
		$offset = $this->offset();
		if ($offset < 0) return "LIMIT 0";
		return "LIMIT {$offset}, {$this->chunk_size}";
	}

	public function url($chunk = null)
	{
		if ($chunk === null) $chunk = $this->chunk + 1;
		return str_replace('-chunk-', $chunk, 
			$this->url_format);
	}
	
	public function render($template = null)
	{
		if ($template === null)
			$template = 'partials/chunkination';
		
		$current = $this->chunk;
		
		$prev = $this->chunk > 0 ? ($this->chunk - 1) : null;
		$next = ((($this->chunk + 1) * $this->chunk_size) 
			< $this->total	? ($this->chunk + 1) : null);
		
		$prev_2 = $this->chunk > 1 ? ($this->chunk - 2) : null;
		$next_2 = ((($this->chunk + 2) * $this->chunk_size) 
			< $this->total	? ($this->chunk + 2) : null);
					
		$first = $this->chunk > 0 ? 0 : null;
		$last = ((($this->chunk + 1) * $this->chunk_size) 
			< $this->total ? (ceil($this->total / $this->chunk_size) - 1) : null);
		
		$view_data = array();
		$view_data['current'] = $current;
		$view_data['prev_2'] = $prev_2;
		$view_data['next_2'] = $next_2;
		$view_data['first'] = $first;
		$view_data['prev'] = $prev;
		$view_data['next'] = $next;
		$view_data['last'] = $last;
		
		foreach ($view_data as $k => $chunk)
		{
			if ($chunk === null) continue;
			$view_data[$k] = new stdClass();
			$view_data[$k]->url = str_replace('-chunk-', 
				($chunk + 1), $this->url_format);
			$view_data[$k]->chunk = $chunk + 1;
		}
		
		$ci =& get_instance();
		return $ci->load->view($template, $view_data, true);
	}

	public function render_bigger($template = null, $page_to_show = 3)
	{
		if ($template === null)
			$template = 'partials/chunkination';
		
		$current = $this->chunk;
		
		$prev = $this->chunk > 0 ? ($this->chunk - 1) : null;
		$next = ((($this->chunk + 1) * $this->chunk_size) 
			< $this->total	? ($this->chunk + 1) : null);
		
		$prev_2 = $this->chunk > 1 ? ($this->chunk - 2) : null;
		$next_2 = ((($this->chunk + 2) * $this->chunk_size) 
			< $this->total	? ($this->chunk + 2) : null);

		$next_p = array();

		for ($c = 3; $c < $page_to_show; $c++)
			$next_p[] = ((($this->chunk + $c) * $this->chunk_size) 
				< $this->total	? ($this->chunk + $c) : null);

		$next_p = array_filter($next_p);
			
		$first = $this->chunk > 0 ? 0 : null;
		$last = ((($this->chunk + 1) * $this->chunk_size) 
			< $this->total ? (ceil($this->total / $this->chunk_size) - 1) : null);
		
		$view_data = array();
		$view_data['current'] = $current;
		$view_data['prev_2'] = $prev_2;
		$view_data['next_2'] = $next_2;
		$view_data['first'] = $first;
		$view_data['prev'] = $prev;
		$view_data['next'] = $next;
		$view_data['last'] = $last;
		
		foreach ($view_data as $k => $chunk)
		{
			if ($chunk === null) continue;
			$view_data[$k] = new stdClass();
			$view_data[$k]->url = str_replace('-chunk-', 
				($chunk + 1), $this->url_format);
			$view_data[$k]->chunk = $chunk + 1;
		}

		foreach ($next_p as $k => $chunk)
		{
			if ($chunk === null) continue;
			$next_p[$k] = new stdClass();
			$next_p[$k]->url = str_replace('-chunk-', 
				($chunk + 1), $this->url_format);
			$next_p[$k]->chunk = $chunk + 1;
		}
		
		$view_data['next_p'] = $next_p;
		
		$ci =& get_instance();
		return $ci->load->view($template, $view_data, true);
	}
	
}

?>