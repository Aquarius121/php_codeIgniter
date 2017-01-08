<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" media="print" />

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Search Results
					<p class="text-muted search-subtitle">
						Campaign: <span><a href="manage/analyze/email/view/<?= $vd->campaign->id ?>">
							<?= $vd->esc($vd->campaign->name) ?></a></span>
					</p>
				</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">					
				<?= $this->load->view('manage/analyze/partials/email-view-results') ?>				
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
		of <?= $vd->chunkination->total() ?> search results
	</p>
</div>

<script>

var hid = $.create("input");
var search_form = $(".navbar-search");
hid.attr('type', 'hidden');
hid.attr('name', 'campaign_id');
hid.attr('value', '<?= $vd->campaign->id ?>');
search_form.append(hid);

</script>