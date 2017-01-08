<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Canvas_Bar_Chart extends Canvas_Line_Chart {
	
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
		$bars = array();

		foreach ($this->data as $data_bar)
		{
			$bar = new stdClass();
			$bar->labels = array();
			$bar->points = array();

			// separating out the labels and data points
			// * maybe need to check if they are combined
			//   before we decide to separate them
			foreach ($data_bar->points as $item)
			{
				$bar->labels[] = $item->label;
				$bar->points[] = $item->value;
			}

			$bar->color = $data_bar->color;
			if (isset($data_bar->label))
			     $bar->label = $data_bar->label;
			else $bar->label = null;
			$bars[] = $bar;
		}

		$ci =& get_instance();
		$view_data = array(
			'is_hide_legend' => $this->is_hide_legend,
			'bars' => $bars,
			'options' => $this,
		);

		$view = 'manage/partials/canvas-bar-chart';
		return $ci->load->view_return($view, $view_data);
	}	
}

?>