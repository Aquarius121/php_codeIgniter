<style>

table {
	border-collapse: collapse;
	border: none;
	margin-bottom: 20px;
}

table th,
table td {
	border: 1px solid #ccc;
	padding: 2px 6px;
	vertical-align: top;
}

div.container {
	font-family: sans-serif;
	padding: 10px 2px;	
}

table th:first-child,
table td:first-child {
	padding-right: 12px;
}

div.header-text {
	margin-bottom: 20px;
}

div.muted {
	color: #666;
}

</style>

<div class="container">
	
	<div class="header-text">
		<div><?= $vd->date_start->format(Date::FORMAT_MYSQL) ?></div>
		<div><?= $vd->date_end->format(Date::FORMAT_MYSQL) ?></div>
	</div>

	<?php if ($vd->order_stats): ?>
		<div style="margin-bottom:10px">Order Stats</div>
		<table cellpadding="2" border="1">
			<thead>
				<th align="left">Store Item</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
			</thead>
			<tbody>
				<?php $total = 0; ?>
				<?php foreach ($vd->order_stats as $item_id => $stat): ?>
				<?php $total += $stat->billed; ?>
				<tr>
					<td align="left">
						<?= $stat->name ?>
						<?php if ($stat->comment): ?>
							<div class="muted"><?= $stat->comment ?></div>
						<?php endif ?>
					</td>
					<td align="right"><?= number_format($stat->count, 0) ?></td>
					<td align="right"><?= number_format($stat->billed, 2) ?></td>
				</tr>
				<?php endforeach ?>
				<tr>
					<td colspan="2"></td>
					<td align="right" style="font-weight:bold">
						<?= number_format($total, 2) ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif ?>

	<?php if ($vd->renew_stats): ?>
		<div style="margin-bottom:10px">Renew Stats</div>
		<table cellpadding="2" border="1">
			<thead>
				<th align="left">Store Item</th>
				<th align="right">Count</th>
				<th align="right">Billed</th>
			</thead>
			<tbody>
				<?php $total = 0; ?>
				<?php foreach ($vd->renew_stats as $item_id => $stat): ?>
				<?php $total += $stat->billed; ?>
				<tr>
					<td align="left">
						<?= $stat->name ?>
						<?php if ($stat->comment): ?>
							<div class="muted"><?= $stat->comment ?></div>
						<?php endif ?>
					</td>
					<td align="right"><?= number_format($stat->count, 0) ?></td>
					<td align="right"><?= number_format($stat->billed, 2) ?></td>
				</tr>
				<?php endforeach ?>
				<tr>
					<td colspan="2"></td>
					<td align="right" style="font-weight:bold">
						<?= number_format($total, 2) ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif ?>

	<?php if ($vd->pr_stats || $vd->register_stats): ?>
	<table cellpadding="2" border="1">
		<thead>
			<th align="left">Statistic</th>
			<th align="right">Count</th>
		</thead>
		<tbody>
			<?php if ($vd->pr_stats): ?>
			<tr>
				<td align="left">Premium PR</td>
				<td align="right"><?= $vd->pr_stats->premium ?></td>
			</tr>
			<tr>
				<td align="left">Basic PR</td>
				<td align="right"><?= $vd->pr_stats->basic ?></td>
			</tr>
			<?php endif ?>
			<?php if ($vd->register_stats): ?>
			<tr>
				<td align="left">New Users</td>
				<td align="right"><?= $vd->register_stats->all ?></td>
			</tr>
			<tr>
				<td align="left">New Users (Verified)</td>
				<td align="right"><?= $vd->register_stats->verified ?></td>
			</tr>
			<?php endif ?>
		</tbody>
	</table>
	<?php endif ?>

</div>