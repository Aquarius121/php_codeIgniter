<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12 page-title">
			<div class="panel panel-default">
				<div class="panel-body">

					<div class="hidden-xs">
						<div id="report-generate">
							<div class="creating">
								<h2>Creating Report.</h2>
								<h3>This process can take several minutes.</h3>
								<img src="<?= $vd->assets_base ?>im/loader-line-large.gif" />
							</div>
						</div>
					</div>

					<div class="visible-xs">
						<div id="report-generate" class="xs">
							<div class="creating">
								<h2>Creating Report.</h2>
								<h3>This process can take several minutes.</h3>
								<img src="<?= $vd->assets_base ?>im/loader-line-large.gif" />
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(function() {	

	setTimeout(function() {

		var return_url = <?= json_encode($vd->return_url) ?>;
		var generate_url = <?= json_encode($vd->generate_url) ?>;
		$.post(generate_url, { indirect: 1 }, function(res) {
			
			if (res && res.download_url)
			     window.location = res.download_url;
			else window.location = return_url;

		});
		
	}, 2000);

});
</script>