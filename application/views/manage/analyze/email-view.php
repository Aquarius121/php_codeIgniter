<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" media="print" />

<ul class="breadcrumb no-print nomarbot">
	<li><a href="manage/analyze">Analytics</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/analyze/email">Email Stats</a> <span class="divider">&raquo;</span></li>
	<li class="active"><?= $vd->esc($vd->campaign->name) ?></li>
</ul>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Email Stats</h1>
			</div>
			<div class="col-lg-6 actions">
				<ul class="list-inline actions">
					<li><a href="manage/analyze/email/report/<?= $vd->campaign->id ?>" 
						class="btn btn-primary btn-with-icon">
						<img src="<?= $vd->assets_base ?>im/fugue-icons/blue-document-pdf-text.png" />
						Export as PDF
					</a></li>
					<li><a href="javascript:void(0)" class="btn btn-default" id="print">Print</a></li>
					<script> 
					
					$(function() {
						
						$("#print").on("click", function() {
							window.print();
						});
						
					});
					
					</script>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
				
					<div class="row">
						<div class="col-lg-12" id="double-stats-summary">
							<legend><?= $vd->esc($vd->campaign->name) ?></legend>
							<div class="stats-summary">
								<strong><?= $vd->views ?>+ Views</strong>*
								<?php if ($vd->clicks): ?>
								 	and <strong><?= $vd->clicks ?> Clicks</strong>
								<?php endif ?>
							</div>
						</div>
					</div>
					
					<?= $this->load->view('manage/analyze/partials/email-view-results') ?>
					
				</div>
			</div>
		
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					* Some email clients do not allow views to be tracked.<br />
					&dagger; Viewed status is based on clicked status.
				</div>
			</div>
		</div>
	</div>
		
	<?= $vd->chunkination->render() ?>
	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Contacts
	</p>
</div>

<script>

$(function() {

	var hid = $.create("input");
	var search_form = $(".navbar-search");
	hid.attr('type', 'hidden');
	hid.attr('name', 'campaign_id');
	hid.attr('value', '<?= $vd->campaign->id ?>');
	search_form.append(hid);

});

</script>