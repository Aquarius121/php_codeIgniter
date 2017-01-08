<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Upcoming Renewals</h1>
				</div>
				<div class="span6">
					<a href="admin/analytics/reports/upcoming_renewals/download" class="btn pull-right">
						<img src="<?= $vd->assets_base ?>im/reports/download.png"> 
						Download
					</span>
					</a>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="analytics-report-content">

	<?php 

	$date_mo = Date::utc()->format('Y-m');
	$total_mo = 0;

	foreach ($vd->results as $renewal)
	{
		$terminates = Date::utc($renewal->date_termination);
		if ($terminates->format('Y-m') == $date_mo)
			$total_mo += $renewal->total_with_discount;
	}

	?>

	<div class="status-info-muted marbot">
		Renewals due this month:
		&nbsp;<strong class="status-info">$ <?= 
		number_format($total_mo, 2) ?></strong>
	</div>

	<table class="report-table">
		<thead>
			<tr>
				<th class="ta-left">Account</th>				
				<th class="ta-left">Item</th>
				<th class="ta-left">Created</th>
				<th class="ta-left">Renews</th>
				<th class="ta-left">Value</th>
				<th class="ta-left">Status</th>
				<th class="ta-left">Comment</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->results as $renewal): ?>
				<tr>
					<td class="ta-left">
						<a href="admin/users/view/<?= $renewal->user->id ?>"><?= 
							$vd->esc($renewal->user->email) ?></a>
					</td>
					<td class="ta-left">
						<?= $renewal->item->name ?>
					</td>
					<td>
						<?php $dt = Date::utc($renewal->date_created) ?>
						<?= $dt->format(Date::FORMAT_LOG); ?>
					</td>
					<td>
						<?php $dt = Date::utc($renewal->date_termination) ?>
						<?= $dt->format(Date::FORMAT_LOG); ?>
					</td>
					<td>
						<?= number_format($renewal->total_with_discount, 2) ?>
					</td>
					<td>
						<?php if ($renewal->is_on_hold): ?>
						<strong class="status-false">Failure</strong>
						<?php else: ?>
						<strong class="status-true">Scheduled</strong>
						<?php endif ?>
					</td>
					<td>
						<?php if ($renewal->is_on_hold): ?>
							<?= $renewal->is_on_hold ?> of <?= Renewal::AUTO_RENEW_ATTEMPTS ?> failures.
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

</div>