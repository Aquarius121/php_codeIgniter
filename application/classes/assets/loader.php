<?php 

namespace Assets;

abstract class Loader {
	
	protected $hash;
	protected $base_dir;
	protected $base_url;
	protected $assets = array();
	protected $glue = PHP_EOL;

	const LOADER_PATH = 'loader';

	public function __construct($base_url, $base_dir)
	{
		$this->base_url = $base_url;
		$this->base_dir = $base_dir;
	}

	public abstract function render($basic = false);

	public function add($asset)
	{
		$file = build_path($this->base_dir, $asset);
		if (!is_file($file)) throw new \Exception('Asset not found');
		$this->assets[] = $asset;
	}

	public function clear()
	{
		$this->assets = array();
	}

	protected function build($outfile)
	{
		$output = array();
		
		foreach ($this->assets as $asset)
		{
			$file = build_path($this->base_dir, $asset);	
			$output[] = $this->process_file($file);
		}

		$output = implode($this->glue, $output);
		$output = $this->process_output($output, $outfile);
		file_put_contents($outfile, $output);
		$this->build_compressed($output, $outfile);
	}

	protected function build_compressed($output, $outfile)
	{
		$output = \GZIP::encode($output);
		$gzfile = sprintf('%s.gz', $outfile);
		file_put_contents($gzfile, $output);
	}

	protected function hash()
	{
		foreach ($this->assets as $asset)
		{
			$file = build_path($this->base_dir, $asset);
			$hash_files[] = $file;
			$hash_times[] = filemtime($file);
		}

		$this->hash = new \Data_Hash();
		$this->hash->files = $hash_files;
		$this->hash->times = $hash_times;
		return $this->hash->hash_hex();
	}

	protected function process_file($file)
	{
		return file_get_contents($file);
	}

	protected function process_output($output, $file)
	{
		return $output;
	}
	
	protected function out_filename($hex)
	{
		return build_path($this->base_dir, 
			static::LOADER_PATH,
			sprintf('%s.dat', $hex));
	}

	protected function out_url($hex)
	{
		return build_url($this->base_url, 
			static::LOADER_PATH,
			sprintf('%s.dat', $hex));
	}

}