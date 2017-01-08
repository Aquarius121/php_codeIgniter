<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Status Update</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="analytics-report-content">

	<div class="marbot-5"><img src="<?= $vd->assets_base ?>im/reports/download.png"> <a href="admin/analytics/reports/status_update/transaction">transactions.csv</a></div>
	<div class="marbot-5"><img src="<?= $vd->assets_base ?>im/reports/download.png"> <a href="admin/analytics/reports/status_update/new_users">new_users.csv</a></div>
	<div class="marbot-5"><img src="<?= $vd->assets_base ?>im/reports/download.png"> <a href="admin/analytics/reports/status_update/cancellation">cancellations.csv</a></div>
	<div class="marbot-5"><img src="<?= $vd->assets_base ?>im/reports/download.png"> <a href="admin/analytics/reports/status_update/bill_failure">bill_failures.csv</a></div>

	<?= $ci->load->view('email/owner_report') ?>

</div>