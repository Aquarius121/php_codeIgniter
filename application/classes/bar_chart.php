<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// require_once 'application/helpers/image_alpha_line.php';
require_once 'application/helpers/image_bold_line.php';

class Bar_Chart {
	
	public $colors;
	public $expires;
	public $font;
	public $font_size;
	public $grid_size;
	public $height;
	public $width;
	
	public function __construct($data, $width = 500, $height = 200)
	{
		$this->colors         = new stdClass();
		$this->colors->grid   = array(245, 245, 255, 0);
		$this->colors->step   = array(215, 215, 215, 0);
		$this->colors->font   = array(110, 110, 110, 0);
		$this->colors->line   = array(228, 93, 43, 0);
		$this->colors->fill   = array(251, 154, 6, 90);
		$this->font           = 'assets/other/roboto-bold.ttf';
		$this->width          = $width;
		$this->height         = $height;
		$this->data           = $data;
		$this->expires        = 7200;
		$this->font_size      = 8;
		$this->grid_size      = 5;
	}
	
	public function render()
	{
		$width = $this->width;
		$height = $this->height;
		$grid_size = $this->grid_size;
		$font_size = $this->font_size;
		$data_points = count($this->data);
		$chart_data = $this->data;
		$font_file = $this->font;

		$label_spacing = 4;
		$bounds = imagettfbbox($font_size, 0, $font_file, 'Y');
		$data_area_height = $height - abs($bounds[7] - $bounds[1]) - (2 * $label_spacing) - 1;
		
		$im = imagecreatetruecolor($width, $height);
		$back_color = imagecolorallocate($im, 255, 255, 255);
		$font_back_color = imagecolorallocatealpha($im, 255, 255, 255, 63);	
		imagefill($im, 0, 0, $back_color);
		imagealphablending($im, true);
		imageantialias($im, true);
		
		$grid_color = imagecolorallocatealpha($im, 
			$this->colors->grid[0], $this->colors->grid[1], 
			$this->colors->grid[2], $this->colors->grid[3]);	
				
		$step_color = imagecolorallocatealpha($im, 
			$this->colors->step[0], $this->colors->step[1], 
			$this->colors->step[2], $this->colors->step[3]);
		
		$font_color = imagecolorallocatealpha($im, 
			$this->colors->font[0], $this->colors->font[1], 
			$this->colors->font[2], $this->colors->font[3]);
		
		$line_color = imagecolorallocatealpha($im, 
			$this->colors->line[0], $this->colors->line[1], 
			$this->colors->line[2], $this->colors->line[3]);
		
		$fill_color = imagecolorallocatealpha($im, 
			$this->colors->fill[0], $this->colors->fill[1], 
			$this->colors->fill[2], $this->colors->fill[3]);
		
		if ($grid_size > 0)
		{
			for ($x = $grid_size; $x < $width; $x += $grid_size)
				imageline($im, $x, 0, $x, $data_area_height, $grid_color);
			for ($y = $grid_size; $y < $data_area_height; $y += $grid_size)
				imageline($im, 0, $y, $width, $y, $grid_color);
		}
		
		$max_value = 0;
		$min_value = PHP_INT_MAX;
		
		foreach ($chart_data as $item)
		{
			$max_value = max($max_value, $item->value);
			$min_value = min($min_value, $item->value);
		}
		
		for ($step_i = 1; $step_i <= 3; $step_i++)
		{
			$step_percent = ($step_i / 4);
			$y = $data_area_height - ceil($data_area_height * $step_percent);
			imageline($im, 0, $y, $width, $y, $step_color);
		}

		// line below data area 
		imageline($im, 0, $data_area_height, $width, 
			$data_area_height, $step_color);

		$spacing = 3;
		$side_spacing = 2 * $spacing;
		$between_spacing = ($data_points - 1) * $spacing;
		$total_bar_width = $width - $side_spacing - $between_spacing;
		$bar_width = floor($total_bar_width / $data_points);
		$total_used_width = ($bar_width * $data_points) + 
			$side_spacing + $between_spacing;
		$offset_left = floor(($width - $total_used_width) / 2);
		$x_left = -$bar_width;

		for ($i = 0; $i < $data_points; $i++)
		{
			$item = $chart_data[$i];
			$bar_height = floor(($data_area_height - 1) * ($item->value / $max_value));
			$x_left = $x_left + $bar_width + $spacing;
			if ($bar_height <= 0) continue;
			
			$x_right = $x_left + $bar_width - 1;
			$y_top = $data_area_height - $bar_height - 1;
			$y_bottom = $data_area_height - 2;

			$item_fill_color = $fill_color;
			$item_line_color = $line_color;

			if (isset($item->colors))
			{
				if (isset($item->colors->fill))
				{
					$item_fill_color = imagecolorallocatealpha($im, 
						$item->colors->fill[0], $item->colors->fill[1], 
						$item->colors->fill[2], $item->colors->fill[3]);
				}

				if (isset($item->colors->line))
				{
					$item_line_color = imagecolorallocatealpha($im, 
						$item->colors->line[0], $item->colors->line[1], 
						$item->colors->line[2], $item->colors->line[3]);
				}
			}
			
			imagefilledrectangle($im, $offset_left + $x_left, 
				$y_top, $offset_left + $x_right, 
				$y_bottom, $item_fill_color);

			imagerectangle($im, $offset_left + $x_left, 
				$y_top, $offset_left + $x_right, 
				$y_bottom, $item_line_color);
		}
	
		for ($step_i = 1; $step_i <= 3; $step_i++)
		{
			// $step_percent = ($step_i / 4);
			// $y = $height - round($height * $step_percent);
			// $label = $min_step + (int) ($step_percent * ($max_step - $min_step));
			// $bounds = imagettfbbox($font_size, 0, $font_file, $label);
			// imagettftext($im, $font_size, 0, 5, (5 + $y + (-$bounds[5])), 
			// 	$font_color, $font_file, $label);
		}
				
		// $bounds = imagettfbbox($font_size, 0, $font_file, $max_step);
		// imagettftext($im, $font_size, 0, 5, (5 + (-$bounds[5])), 
		// 	$font_color, $font_file, $max_step);
		
		for ($i = 0; $i < $data_points; $i++)
		{
			$item = $chart_data[$i];
			$label = $item->label;
			$bounds = imagettfbbox($font_size, 0, $font_file, $label);
			$b_height = ($bounds[7] - $bounds[1]);
			$b_width = ($bounds[2] - $bounds[0]);
			$x_left = $spacing + (($bar_width + $spacing) * $i);
			$x_left += floor(($bar_width - $b_width) / 2);
			$x_left += $offset_left;
			imagettftext($im, $font_size, 0, $x_left, ($height - 4), 
				$font_color, $font_file, $label);
		}
		
		ob_clean();
		$ci =& get_instance();
		$ci->expires($this->expires);
		header("Content-Type: image/png");
		imagepng($im);
		exit;
	}
	
}

?>