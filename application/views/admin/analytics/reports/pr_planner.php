<style>

div.report-container table {
	border-collapse: collapse;
	border: none;
	margin-bottom: 20px;
	width: auto;
}

div.report-container table th,
div.report-container table td {
	border: 1px solid #ccc;
	padding: 2px 6px;	
	vertical-align: top;
	max-width: 350px;
}

div.report-container {
	font-family: sans-serif;
	padding: 10px 2px;	
}

div.report-container table th:first-child,
div.report-container table td:first-child {
	padding-right: 12px;
}

div.report-container div.muted {
	color: #999;
}

div.report-container h2 {
	color: #666;
	font-size: 18px;
	padding: 0;
	margin: 0 0 10px 0;
}

</style>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>PR Planner</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="analytics-report-content">
<div class="report-container">

	<div class="pull-left marR-20">
		<div class="marB-5"><strong class="status-info">Direct</strong></div>
		<table cellpadding="2" border="1">
			<thead>
				<tr>			
					<th align="center">Planner Progress</th>
					<th align="center">Count</th>		
					<th align="center">Percent</th>		
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="left">Reached P0</td>
					<td align="right"><?= $vd->counts->direct->zero ?></td>
					<td align="right">100.0</td>
				</tr>
				<tr>
					<td align="left">Reached P1</td>
					<td align="right"><?= $vd->counts->direct->one ?></td>
					<td align="right">100.0</td>
				</tr>
				<?php if ($vd->counts->direct->zero): ?>
				<tr>
					<td align="left">Reached P2</td>
					<td align="right"><?= $vd->counts->direct->two ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->two / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P3</td>
					<td align="right"><?= $vd->counts->direct->three ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->three / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P4</td>
					<td align="right"><?= $vd->counts->direct->four ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->four / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P5</td>
					<td align="right"><?= $vd->counts->direct->five ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->five / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P6</td>
					<td align="right"><?= $vd->counts->direct->six ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->six / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P7</td>
					<td align="right"><?= $vd->counts->direct->seven ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->seven / $vd->counts->direct->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Finished</td>
					<td align="right"><?= $vd->counts->direct->finished ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->direct->finished / $vd->counts->direct->zero)) ?></td>
				</tr>
				<?php endif ?>
			</tbody>
		</table>
	</div>

	<div class="pull-left marR-20">
		<div class="marB-5"><strong class="status-info">With Intro</strong></div>
		<table cellpadding="2" border="1">
			<thead>
				<tr>			
					<th align="center">Planner Progress</th>
					<th align="center">Count</th>		
					<th align="center">Percent</th>		
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="left">Reached P0</td>
					<td align="right"><?= $vd->counts->intro->zero ?></td>
					<td align="right">100.0</td>
				</tr>
				<tr>
					<td align="left">Reached P1</td>
					<td align="right"><?= $vd->counts->intro->one ?></td>
					<td align="right">100.0</td>
				</tr>
				<?php if ($vd->counts->intro->zero): ?>
				<tr>
					<td align="left">Reached P2</td>
					<td align="right"><?= $vd->counts->intro->two ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->two / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P3</td>
					<td align="right"><?= $vd->counts->intro->three ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->three / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P4</td>
					<td align="right"><?= $vd->counts->intro->four ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->four / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P5</td>
					<td align="right"><?= $vd->counts->intro->five ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->five / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P6</td>
					<td align="right"><?= $vd->counts->intro->six ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->six / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Reached P7</td>
					<td align="right"><?= $vd->counts->intro->seven ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->seven / $vd->counts->intro->zero)) ?></td>
				</tr>
				<tr>
					<td align="left">Finished</td>
					<td align="right"><?= $vd->counts->intro->finished ?></td>
					<td align="right"><?= sprintf('%0.1f', (100 * $vd->counts->intro->finished / $vd->counts->intro->zero)) ?></td>
				</tr>
				<?php endif ?>
			</tbody>
		</table>
	</div>

</div>
</div>