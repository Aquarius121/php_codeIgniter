<?php

use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class Logo_Color_Extractor {
	
	// value between 0 and 255
	const MAX_INTENSITY = 175;

	protected $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function extract()
	{
		$max_intensity = static::MAX_INTENSITY * 3;
		$palette = Palette::fromFilename($this->file);
		$extractor = new ColorExtractor($palette);
		$colors = $extractor->extract(5);

		foreach ($colors as $color)
		{
			$r = ($color >> 16) & 0xFF; 
			$g = ($color >> 8) & 0xFF;
			$b = ($color) & 0xFF;

			if ($r + $g + $b < $max_intensity)
				return Color::fromIntToHex($color);
		}

		return false;
	}

}