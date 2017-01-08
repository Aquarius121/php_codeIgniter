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

<div class="report-container">

	<?php if ($vd->order_stats->items): ?>	
	<h2>Orders</h2>	
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<th></th>
				<th colspan="2" align="right"><?= $vd->day ?></th>
				<th colspan="2" align="right">Last 30 Days</th>
				<th colspan="2" align="right"><?= $vd->month ?></th>
			</tr>
			<tr>
				<th align="left">Store Item</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->order_stats->items as $item_id => $stat): ?>
			<tr>
				<td align="left">
					<?= $stat->name ?>
					<?php if ($stat->comment && $stat->comment != $stat->name &&
							$stat->comment != 'Custom Product' &&
							!str_starts_with($stat->name, $stat->comment)): ?>
						<div class="muted"><?= $stat->comment ?></div>
					<?php endif ?>
				</td>
				<td align="right"><?= number_format(@$stat->count_1d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_1d, 2) ?></td>
				<td align="right"><?= number_format(@$stat->count_30d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_30d, 2) ?></td>
				<td align="right"><?= number_format(@$stat->count_month, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_month, 2) ?></td>
			</tr>
			<?php endforeach ?>
			<tr>
				<td align="left"><strong>Total</strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->order_stats->total_1d, 2) ?></strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->order_stats->total_30d, 2) ?></strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->order_stats->total_month, 2) ?></strong></td>
			</tr>
		</tbody>
	</table>
	<?php endif ?>
		
	<?php if ($vd->renew_stats->items): ?>
	<h2>Renewals</h2>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<th></th>
				<th colspan="2" align="right"><?= $vd->day ?></th>
				<th colspan="2" align="right">Last 30 Days</th>
				<th colspan="2" align="right"><?= $vd->month ?></th>
			</tr>
			<tr>
				<th align="left">Store Item</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->renew_stats->items as $item_id => $stat): ?>
			<tr>
				<td align="left">
					<?= $stat->name ?>
					<?php if ($stat->comment && $stat->comment != $stat->name &&
							$stat->comment != 'Custom Product' &&
							!str_starts_with($stat->name, $stat->comment)): ?>
						<div class="muted"><?= $stat->comment ?></div>
					<?php endif ?>
				</td>
				<td align="right"><?= number_format(@$stat->count_1d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_1d, 2) ?></td>
				<td align="right"><?= number_format(@$stat->count_30d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_30d, 2) ?></td>
				<td align="right"><?= number_format(@$stat->count_month, 0) ?></td>
				<td align="right"><?= number_format(@$stat->billed_month, 2) ?></td>
			</tr>
			<?php endforeach ?>
			<tr>
				<td align="left"><strong>Total</strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->renew_stats->total_1d, 2) ?></strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->renew_stats->total_30d, 2) ?></strong></td>
				<td align="right"></td>
				<td align="right"><strong><?= number_format($vd->renew_stats->total_month, 2) ?></strong></td>
			</tr>
		</tbody>
	</table>
	<?php endif ?>
	
	<h2>Billing Totals</h2>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<th align="left">Total</th>
				<th align="right"><?= $vd->day ?></th>
				<th align="right">Last 30 Days</th>
				<th align="right"><?= $vd->month ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left">V2 Orders</td>
				<td align="right"><?= number_format($vd->order_stats->total_1d, 2) ?></td>
				<td align="right"><?= number_format($vd->order_stats->total_30d, 2) ?></td>
				<td align="right"><?= number_format($vd->order_stats->total_month, 2) ?></td>
			</tr>
			<tr>
				<td align="left">V2 Renewals</td>
				<td align="right"><?= number_format($vd->renew_stats->total_1d, 2) ?></td>
				<td align="right"><?= number_format($vd->renew_stats->total_30d, 2) ?></td>
				<td align="right"><?= number_format($vd->renew_stats->total_month, 2) ?></td>
			</tr>
			<tr>
				<td align="left">Version 1</td>
				<td align="right"><?= number_format($vd->legacy_stats_1d, 2) ?></td>
				<td align="right"><?= number_format($vd->legacy_stats_30d, 2) ?></td>
				<td align="right"><?= number_format($vd->legacy_stats_month, 2) ?></td>
			</tr>
			<tr>
				<td align="left">Overall</td>
				<td align="right"><strong><?= number_format($vd->overall_total_1d, 2) ?></strong></td>
				<td align="right"><strong><?= number_format($vd->overall_total_30d, 2) ?></strong></td>
				<td align="right"><strong><?= number_format($vd->overall_total_month, 2) ?></strong></td>
			</tr>
		</tbody>
	</table>
	
	<?php if ($vd->cancel_stats->items): ?>
	<h2>Cancellations</h2>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<th align="left">Store Item</th>
				<th align="right"><?= $vd->day ?></th>
				<th align="right">30 Days</th>
				<th align="right"><?= $vd->month ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->cancel_stats->items as $item_id => $stat): ?>
			<tr>
				<td align="left">
					<?= $stat->name ?>
					<?php if ($stat->comment && $stat->comment != $stat->name &&
							$stat->comment != 'Custom Product' &&
							!str_starts_with($stat->name, $stat->comment)): ?>
						<div class="muted"><?= $stat->comment ?></div>
					<?php endif ?>
				</td>
				<td align="right"><?= number_format(@$stat->count_1d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->count_30d, 0) ?></td>
				<td align="right"><?= number_format(@$stat->count_month, 0) ?></td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php endif ?>
	
	<h2>Other Stats</h2>
	<table cellpadding="2" border="1">
		<thead>
			<tr>
				<th align="left">Statistic</th>
				<th align="right"><?= $vd->day ?></th>
				<th align="right">30 Days</th>
				<th align="right"><?= $vd->month ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left">Premium PR</td>
				<td align="right"><?= $vd->pr_stats_1d->premium ?></td>
				<td align="right"><?= $vd->pr_stats_30d->premium ?></td>
				<td align="right"><?= $vd->pr_stats_month->premium ?></td>
			</tr>
			<tr>
				<td align="left">Basic PR</td>
				<td align="right"><?= $vd->pr_stats_1d->basic ?></td>
				<td align="right"><?= $vd->pr_stats_30d->basic ?></td>
				<td align="right"><?= $vd->pr_stats_month->basic ?></td>
			</tr>
			<tr>
				<td align="left">New Users</td>
				<td align="right"><?= $vd->register_stats_1d->all ?></td>
				<td align="right"><?= $vd->register_stats_30d->all ?></td>
				<td align="right"><?= $vd->register_stats_month->all ?></td>
			</tr>
			<tr>
				<td align="left">New Users (Verified)</td>
				<td align="right"><?= $vd->register_stats_1d->verified ?></td>
				<td align="right"><?= $vd->register_stats_30d->verified ?></td>
				<td align="right"><?= $vd->register_stats_month->verified ?></td>
			</tr>
			<tr>
				<td align="left">New Users (Active)</td>
				<td align="right"><?= $vd->register_stats_1d->active ?></td>
				<td align="right"><?= $vd->register_stats_30d->active ?></td>
				<td align="right"><?= $vd->register_stats_month->active ?></td>
			</tr>
			<tr>
				<td align="left">Active Users</td>
				<td align="right"><?= $vd->active_stats_1d ?></td>
				<td align="right"><?= $vd->active_stats_30d ?></td>
				<td align="right"><?= $vd->active_stats_month ?></td>
			</tr>
			<tr>
				<td align="left">Newsrooms Enabled</td>
				<td align="right"><?= $vd->newsroom_stats ?></td>
				<td align="right">-</td>
				<td align="right">-</td>
			</tr>
		</tbody>
	</table>

</div>