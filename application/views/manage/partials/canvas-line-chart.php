<?php $__canvas_id = sprintf('chart_canvas_%s', substr(md5(microtime(true)), 0, 8)); ?>
<?php $__legend_id = sprintf('chart_legend_%s', substr(md5(microtime(true)), 0, 8)); ?>

<!--[if lt IE 9]>
<script src="<?= $vd->assets_base ?>lib/excanvas.min.js"></script>
<![endif]-->

<canvas height="<?= $options->height ?>"
	id="<?= $__canvas_id ?>"></canvas>

<div id="<?= $__legend_id ?>" class="chart-legend <?= 
	value_if_test($is_hide_legend, 'dnone') ?>"></div>

<!-- TODO: avoid loading this twice for multiple charts -->
<script src="<?= $vd->assets_base ?>lib/chartjs/Chart.min.js"></script>	

<script>

	$(function() {
	
		var canvas = document.getElementById(<?= json_encode($__canvas_id) ?>);
		var context = canvas.getContext("2d");
		
		var chartData = {
			labels: <?= json_encode($lines[0]->labels) ?>,
			datasets: [
				<?php foreach ($lines as $line): ?>
				{
					label: <?= json_encode($line->label) ?>,
					fillColor: <?= json_encode($options->get_css_color($line->color->fill)) ?>,
					strokeColor: <?= json_encode($options->get_css_color($line->color->line)) ?>,
					pointColor: <?= json_encode($options->get_css_color($line->color->point)) ?>,
					pointStrokeColor: "#fff",
					pointHighlightFill: <?= json_encode($options->get_css_color($line->color->highlight)) ?>,
					pointHighlightStroke: "#fff",
					data: <?= json_encode($line->points) ?>
				}, 
				<?php endforeach ?>
			]
		};
	
		var myChart = new Chart(context).Line(chartData, {
			responsive: true,
			animateRotate: true,
			animateScale: false,
			bezierCurve: false
		});

		var legend = document.getElementById(<?= json_encode($__legend_id) ?>);
		legend.innerHTML = myChart.generateLegend();

	});
	
</script>
