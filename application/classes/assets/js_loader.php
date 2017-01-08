<?php

namespace Assets;

class JS_Loader extends Loader {

	public function render($basic = false)
	{
		if ($basic)
		{
			$dom = new \DOMDocument();

			foreach ($this->assets as $src)
			{
				$url = build_url($this->base_url, $src);
				$element = $dom->createElement('script');
				$element->setAttribute('src', $url);
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
				$this->build($file);

			$dom = new \DOMDocument();
			$element = $dom->createElement('script');
			$element->setAttribute('src', $url);
			$dom->appendChild($element);
			return $dom->saveHTML();
		}
	}

	public function build($file)
	{
		// correctly join scripts with 
		// semicolon and some new lines
		// for presentation
		$this->glue = ";\r\n\r\n";
		return parent::build($file);
	}

	protected function process_file($file)
	{
		// already minified? skip this step
		// * this allows to use a different 
		// minification process if required
		if (str_ends_with($file, '.min.js'))
			return file_get_contents($file);

		$minifier = new \MatthiasMullie\Minify\JS();
		$minifier->add($file);
		$output = $minifier->minify();
		return $output;
	}

	protected function out_filename($hex)
	{
		return build_path($this->base_dir, 
			static::LOADER_PATH,
			sprintf('%s.js', $hex));
	}

	protected function out_url($hex)
	{
		return build_url($this->base_url, 
			static::LOADER_PATH,
			sprintf('%s.js', $hex));
	}
	
}