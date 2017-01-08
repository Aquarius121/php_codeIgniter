<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Reports</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="analytics-reports">

	<h2 class="marbot status-black">General</h2>
	<div class="marbot-20">

		<div class="analytics-report analytics-report-table">
			<a href="admin/analytics/reports/status_update">Daily Status Update</a>
		</div>

		<div class="analytics-report analytics-report-table">
			<a href="admin/analytics/reports/upcoming_renewals">Upcoming Renewals</a>
		</div>

		<div class="analytics-report analytics-report-table">
			<a href="admin/analytics/reports/press_releases">Press Releases</a>
		</div>

		<div class="analytics-report analytics-report-table">
			<a href="admin/analytics/reports/pr_planner">PR Planner</a>
		</div>

	</div>

	<h2 class="marbot status-black">Sales</h2>
	<div class="marbot-20">

		<?php foreach (array(0,1,2) as $offset): ?>
		<?php $dt = Date::months(-$offset); ?>
		<div class="analytics-report analytics-report-table">
			<a href="admin/analytics/reports/sales_summary/<?= $offset ?>">Sales Summary (<?= $dt->format('F') ?>)</a>
		</div>
		<?php endforeach ?>

	</div>

</div>