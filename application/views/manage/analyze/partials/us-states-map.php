<div id="us-states-canvas" style="width: <?= $vd->width ?>;
	height: <?= $vd->height ?>;"></div>
<script>

$(function() {

	var canvas = $('#us-states-canvas');
	var chart_data = <?= json_encode($vd->chart_data) ?>;

	canvas.vectorMap({
		map: 'usa_en',
		backgroundColor: null,
		color: '#F2F8F9',
		hoverOpacity: 0.7,
		selectedColor: '#666666',
		enableZoom: <?= json_encode(!isset($vd->disable_zoom)) ?>,
		showTooltip: true,
		values: chart_data,
		scaleColors: ['#D9EAF2', '#006491'],
		normalizeFunction: 'polynomial',
	});

});

</script>