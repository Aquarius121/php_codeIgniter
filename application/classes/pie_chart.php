<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pie_Chart {
	
	public $data;
	public $expires;
	public $gap;
	public $height;
	public $width;
	
	public function __construct($data, $width = 200, $height = 200)
	{
		$this->width          = $width;
		$this->height         = $height;
		$this->data           = $data;
	}
	
	public function render()
	{
		$width = $this->width * 4;
		$height = $this->height * 4;
		$data = $this->data;
		
		$im = imagecreatetruecolor($width, $height);
		$back_color = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $back_color);
		imagealphablending($im, true);
		imageantialias($im, true);

		$cx = $width / 2;
		$cy = $height / 2;
		$offset_degree = 0;

		foreach ($data as $idx => $data_point)
		{
			$col = $data_point[1];
			if (count($data_point[1]) == 3)
			     $arc_color = imagecolorallocate($im, $col[0], $col[1], $col[2]);
			else $arc_color = imagecolorallocatealpha($im, $col[0], $col[1], $col[2], $col[3]);
			$data_point_angle = round($data_point[0] * 360);
			// ensure we complete full circle when rounded values are less than 360
			if ($idx == count($data) - 1 && $offset_degree + $data_point_angle < 360)
				$data_point_angle = 360 - $offset_degree;
			imagefilledarc($im, $cx, $cy, $width, $height, $offset_degree, 
				$offset_degree + $data_point_angle, $arc_color, IMG_ARC_PIE);
			$offset_degree += $data_point_angle;
		}

		$offset_degree = 0;
		$cx = $this->width / 2;
		$cy = $this->height / 2;
		$im2 = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($im2, $im, 0, 0, 0, 0, 
			$this->width, $this->height, $width, $height);
		unset($im);
		
		ob_clean();
		$ci =& get_instance();
		$ci->expires($this->expires);
		header("Content-Type: image/png");
		imagepng($im2);
		exit;
	}
	
}

?>