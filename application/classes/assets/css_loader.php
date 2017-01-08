<?php

namespace Assets;

class CSS_Loader extends Loader {

	protected $hex = null;

	public function render($basic = false, $charset = 'UTF-8')
	{
		if ($basic)
		{
			$dom = new \DOMDocument();

			foreach ($this->assets as $src)
			{
				$url = build_url($this->base_url, $src);
				$element = $dom->createElement('link');
				$element->setAttribute('rel', 'stylesheet');
				$element->setAttribute('href', $url);
				$dom->appendChild($element);
			}

			return $dom->saveHTML();
		}
		else
		{
			$hex = $this->hex = $this->hash();
			$file = $this->out_filename($hex);
			$url = $this->out_url($hex);

			if (!is_file($file))
				$this->build_css($file, $charset);

			$dom = new \DOMDocument();
			$element = $dom->createElement('link');
			$element->setAttribute('rel', 'stylesheet');
			$element->setAttribute('href', $url);
			$dom->appendChild($element);
			return $dom->saveHTML();
		}
	}

	protected function build_css($outfile, $charset)
	{
		// the charset is always included 
		// at the very top of the document
		$buffer = \File_Util::buffer_file();
		$bufferstr = sprintf('@charset "%s";', $charset);
		file_put_contents($buffer, $bufferstr);
		$minifier = new \MatthiasMullie\Minify\CSS();
		$minifier->add($buffer);
		unlink($buffer);

		foreach ($this->assets as $asset)
		{
			$file = build_path($this->base_dir, $asset);	
			$minifier->add($file);
		}

		$output = $minifier->minify($outfile);
		$this->build_compressed($output, $outfile);
	}

	protected function out_filename($hex)
	{
		return build_path($this->base_dir, 
			static::LOADER_PATH,
			sprintf('%s.css', $hex));
	}

	protected function out_url($hex)
	{
		return build_url($this->base_url, 
			static::LOADER_PATH,
			sprintf('%s.css', $hex));
	}
	
}