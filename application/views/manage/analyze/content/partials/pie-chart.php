<div class="stats-loader ta-center">	
	<canvas id="pie-chart"></canvas>
</div>

<div id="pie-chart-legend" class="chart-legend pie-chart-legend"></div>

<script>

	$(function() {

		var canvas = document.getElementById('pie-chart');
		var context = canvas.getContext("2d");
		
		<?php if (!$vd->network_fraction && !$vd->external_fraction): ?>

			var chart_data = [
				{
					value: 1,
					color:"#666666",
					highlight: "#666666",
					label: "No Data",
				},
			]

		<?php else: ?>

			var chart_data = [
				{
					value: <?= $vd->network_fraction ?>,
					color:"#1F5FAD",
					highlight: "#1F5FAD",
					label: "Newswire Network",
				},
				{
					value: <?= $vd->external_fraction ?>,
					color: "#FFC870",
					highlight: "#FFC870",
					label: "External Sources"
				},
			]
			
		<?php endif ?>

		var myPieChart = new Chart(context).Pie(chart_data, {
			responsive: true,
			animateRotate: true,
			animateScale: false,
			showTooltips: false
		});

		var legend = document.getElementById('pie-chart-legend');
		legend.innerHTML = myPieChart.generateLegend();		

	});
	
	
</script>
