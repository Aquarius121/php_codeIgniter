<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Canvas_Line_Chart extends Line_Chart {
	
	public $curve;
	public $font_size;
	public $height;
	public $width;
	public $is_hide_legend;
	
	public function __construct($data, $width = 500, $height = 200)
	{
		parent::__construct($data, $width, $height);
		$this->font_size = 10;
		$this->curve = true;
	}
	
	public function get_css_color($color)
	{
		return sprintf('rgba(%d, %d, %d, %.2f)',
			$color[0], $color[1], $color[2],
			(1 - ($color[3] / 128)));
	}

	public function render()
	{
		$lines = array();

		foreach ($this->data as $data_line)
		{
			$line = new stdClass();
			$line->labels = array();
			$line->points = array();

			// separating out the labels and data points
			// * maybe need to check if they are combined
			//   before we decide to separate them
			foreach ($data_line->points as $item)
			{
				$line->labels[] = $item->label;
				$line->points[] = $item->value;
			}

			$line->color = $data_line->color;
			if (isset($data_line->label))
			     $line->label = $data_line->label;
			else $line->label = null;
			$lines[] = $line;
		}

		$ci =& get_instance();
		$view_data = array(
			'is_hide_legend' => $this->is_hide_legend,
			'lines' => $lines,
			'options' => $this,
		);

		$view = 'manage/partials/canvas-line-chart';
		return $ci->load->view_return($view, $view_data);
	}	
}

?>