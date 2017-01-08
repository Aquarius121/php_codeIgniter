<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" media="print" />

<ul class="breadcrumb no-print">
	<li><a href="manage/analyze">Analytics</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/analyze/content/<?= $vd->m_content->type ?>"><?= 
		Model_Content::full_type_plural($vd->m_content->type) ?></a> 
		<span class="divider">&raquo;</span></li>
	<li class="active"><?= $vd->esc($vd->m_content->title) ?></li>
</ul>

<div class="container-fluid no-print content-analyze">
	<header class="single-col-bigger">
		<div class="row">
			<div class="col-lg-4 page-title">
				<h2>Content Stats</h2>
			</div>
			<div class="col-lg-8 actions">
				
				<?php if ($vd->m_content->is_published && $vd->m_content->is_premium 
						&& $vd->m_content->is_legacy && $vd->m_content->report_url): ?>
				<a href="<?= $vd->m_content->report_url ?>" 
					class="btn btn-default btn-with-icon">
					<img src="<?= $vd->assets_base ?>im/fugue-icons/blue-document-pdf-text.png" />
					Distribution Report
				</a>
				<?php endif ?>

				<a href="manage/analyze/content/report/<?= 
							$vd->m_content->id ?>?date_start=<?= 
							$vd->dt_min->format('Y-m-d') ?>&amp;date_end=<?= 
							$vd->dt_max->format('Y-m-d') ?>" 
					class="btn btn-primary btn-with-icon">
					<img src="<?= $vd->assets_base ?>im/fugue-icons/blue-document-pdf-text.png" />
					Download PDF
				</a>

			</div>
		</div>
	</header>
	

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default single-col-bigger">
				<div class="panel-body">

					<div class="row relative">				
						<div class="col-lg-4">
							<h3 class="nomar marbot-30 pad-5v" id="stats-summary">
								<?= $vd->esc($vd->m_content->title) ?>
							</h2>
						</div>
						<div class="col-lg-8 push-down push-right">
							<form action="manage/analyze/content/view/<?= $vd->m_content->id ?>" method="get">
								<div class="col-lg-3 col-xs-6 col-lg-offset-4 stats-input" id="analyze-date-range">
									<div class="input-group in-text-date in-text-add-on marbot-15">
										<input type="text" name="date_start" class="form-control" 
											data-date-format="yyyy-mm-dd" id="date-start"
											value="<?= $vd->dt_min->format('Y-m-d') ?>" />
										<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
									</div>
								</div>
								<div class="col-lg-3 col-xs-6 stats-input">
									<div class="input-group in-text-date in-text-add-on marbot-15">
										<input type="text" name="date_end" class="form-control" 
											data-date-format="yyyy-mm-dd" id="date-end"
											value="<?= $vd->dt_max->format('Y-m-d') ?>" />
										<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
									</div>
								</div>
								<div class="col-lg-2 pull-right stats-button">
									<button class="no-print btn btn-default col-lg-12 nomar marbot-10" type="submit">
										Update
									</button>
								</div>
								<script>
								
								$(function() {
									
									var date_start = $("#date-start");
									var date_start_i = date_start.next("span");
									var date_end = $("#date-end");
									var date_end_i = date_end.next("span");
									
									var date_fields = $();
									date_fields = date_fields.add(date_start);
									date_fields = date_fields.add(date_end);
									
									var date_icons = $();
									date_icons = date_icons.add(date_start_i);
									date_icons = date_icons.add(date_end_i);
									
									$(date_fields).datepicker();
									$(date_icons).on("click", function() {
										$(this).prev("input").datepicker("show");
									});
									
								});
								
								</script>
							</form>
						</div>
					</div>
					
					<div id="analyze-sources" class="row marbot-5 clearfix">
						<?php if ($vd->source_count == 4): ?>
							<?php $source_class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12' ?>
						<?php elseif ($vd->source_count == 3): ?>
							<?php $source_class = 'col-lg-4 col-md-4 col-sm-4 col-xs-12' ?>
						<?php else: ?>
							<?php $source_class = 'col-lg-2 col-md-4 col-sm-4 col-xs-12' ?>
						<?php endif ?>
						<div class="<?= $source_class ?>">
							<span class="source">
								<strong><?= $vd->hits ?><sup>1</sup></strong> <em>Views</em>
								<?php if (Auth::is_admin_online()): ?>
								<a id="views-feed-extended" 
									class="sources-configure" 
									data-id="<?= $vd->m_content->id ?>"></a>
								<script>

								$(function() {

									$("#views-feed-extended").on("click", function() {
										$(document.body).addClass("wait-cursor");
										$.get("manage/analyze/content/views_feed_extended/<?= $vd->m_content->id ?>", function(html) {
											$(document.body).removeClass("wait-cursor");
											bootbox.alert(html);
										});
									});

								});

								</script>
								<?php endif ?>
							</span>
						</div>
						<?php if (isset($vd->impressions)): ?>
						<div class="<?= $source_class ?>">
							<span class="source">
								<strong><?= $vd->impressions + $vd->hits
									?>+<sup>2</sup></strong> <em>Impressions</em>
							</span>
						</div>
						<?php endif ?>
						<?php if ($vd->m_content->is_premium && $vd->m_content->is_published): ?>
						<div class="<?= $source_class ?>">
							<span class="source" id="dist-sites">
								<?php if ($vd->m_content->is_legacy): ?>
								<strong>-</strong>
								<?php else: ?>
								<strong><?= (int) $vd->dist_count ?></strong>
								<?php endif ?>
								<?php if ($vd->dist_count): ?>
								<a href="#"><em>Published</em></a>
								<?php else: ?>
								<em>Published</em>
								<?php endif ?>
							</span>
						</div>
						<script>
						
						$(function() {

							$.fn.slideTo = function() {
								var offset = $(this[0]).offset().top - 100;
								$("html,body").animate({ scrollTop: offset });
							};

							$("#dist-sites a").on("click", function(ev) {
								$("#distribution-anchor").slideTo();
								ev.preventDefault();						
							});
							
						});
						
						
						</script>
						<div class="<?= $source_class ?>">
							<span class="source" id="google-results">
								<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
								<script>
								
								$(function() {

									var container = $("#google-results");
									var url = "manage/analyze/content/google_results/<?= $vd->m_content->id ?>";
									container.load(url, window.update_sources_bar);

								});
								
								</script>
							</span>
						</div>
						<?php endif ?>
						<div class="<?= $source_class ?>">
							<span class="source" id="twitter-shares">
								<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
								<script>
								
								$(function() {

									var container = $("#twitter-shares");
									var url = "manage/analyze/content/twitter_shares/<?= $vd->m_content->id ?>";
									container.load(url, window.update_sources_bar);

								});
								
								</script>
							</span>
						</div>
						<div class="<?= $source_class ?>">
							<span class="source" id="facebook-shares">
								<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
								<script>
								
								$(function() {

									var container = $("#facebook-shares");
									var url = "manage/analyze/content/facebook_shares/<?= $vd->m_content->id ?>";
									container.load(url, window.update_sources_bar);

								});
								
								</script>
							</span>
						</div>
					</div>

					<div class="marbot-20">
						<div class="row clearfix charts-row">
							<div class="col-lg-12"> 
								<div class="stat-charts">
									
									<div class="total-views-panel stats-loader">
										<div class="chart marbot-20">
											<?= $vd->views_chart ?>
										</div>
									</div>

									<div class="traffic-sources">
										<div class="traffic-sources-panel">
											<?= $ci->load->view('manage/analyze/content/partials/pie-chart') ?>
										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>

					<div class="marbot-20">
						<div class="clearfix">
							<div class="col-lg-12 stats-loader">
								<div class="chart marbot-20">
									<?= $vd->hours_chart ?>
								</div>
							</div>
						</div>
					</div>

					<div class="row marbot-30">
						
						<div class="col-lg-8 col-xs-12 vector-map">
							<div id="world-map" class="marbot-15">
								<div class="with-border stats-loader"></div>
							</div>
							<div id="us-states-map">
								<div class="with-border stats-loader"></div>
							</div>
							<script>
							
							$(function() {
								
								var world_map_url = "manage/analyze/content/world_map/<?= 
									$vd->m_content->id ?>";
								
								var world_map = $("#world-map .stats-loader");
								world_map.load(world_map_url, function() {
									world_map.removeClass("stats-loader");
								});

								var us_states_map_url = "manage/analyze/content/us_states_map/<?= 
									$vd->m_content->id ?>";

								var us_states_map = $("#us-states-map .stats-loader");
								us_states_map.load(us_states_map_url, function() {
									us_states_map.removeClass("stats-loader");
								});
								
							});
							
							</script>
						</div>

						<div class="col-lg-4 col-xs-12">
							<div class="marbot-15 hidden-lg"></div>
							<div class="with-border" id="geolocation">
								<div class="locations stats-loader"></div>
								<script>
								
								$(function() {
									
									var locations_url = "manage/analyze/content/geolocation/<?= 
										$vd->m_content->id ?>";
									
									var locations = $("#geolocation .locations");
									locations.load(locations_url, function() {
										locations.removeClass("stats-loader");

										if (locations.is(':empty'))
											$("#geolocation").removeClass('with-border');

									});
									
								});
								
								</script>
							</div>
						</div>
						
					</div>

					<?php if (count($vd->services)): ?>
					<h3>Distribution Sites</h3>
					<div class="distribution-sites-container table-responsive" id="distribution-anchor">
						<table class="distribution-sites table">
							<tr>
							<?php foreach ($vd->services as $k => $service): ?>
							<?php if ($k % 2 === 0 && $k > 0): ?>
							</tr><tr>
							<?php endif ?>
								<td>
									<?php if ($service->logo_image_id): ?>
									<?php $lo_im = Model_Image::find($service->logo_image_id); ?>
									<?php $lo_variant = $lo_im->variant('dist-finger'); ?>
									<?php $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
									<a href="<?= $vd->esc($service->content_url) ?>" target="_blank">
										<img src="<?= $lo_url ?>" />
									</a>
									<?php else: ?>
									<a href="<?= $vd->esc($service->content_url) ?>" target="_blank">
										<div class="fin-service-blank"></div>
									</a>
									<?php endif ?>
								</td>
								<td class="ta-left">
									<div class="fin-service-link">
										<a href="<?= $vd->esc($service->url) ?>" target="_blank">
											<?= $vd->esc($service->name) ?>
										</a>
									</div>
									<a href="<?= $vd->esc($service->content_url) ?>" target="_blank">
										View Press Release
									</a>
								</td>
							<?php endforeach ?>
							</tr>
						</table>
					</div>
					<?php endif ?>
					
					<div class="row">
						<div class="col-lg-12 grid-report">
							<div class="marbot-20"></div>
							<div>1. Does not include the traffic to PR Newswire press releases.</div>
							<div>2. The number of times the content is viewed, or has the potential to be viewed, that we can track.</div>
							<div>3. Value can differ due to your google history, location and other factors.</div>
							<div>4. Considers tweets containing the title or link within the last 7 days.</div>
						</div>
					</div>


					<?php if (count($vd->cbc_contacts)): ?>
					<h3>Media Outreach Contacts</h3>
					<div class="media-outreach-container table-responsive">
						<table class="media-outreach-contacts table">
							<tr>
							<?php foreach ($vd->cbc_contacts as $k => $contact): ?>
							<?php if ($k % 2 === 0 && $k > 0): ?>
							</tr><tr>
							<?php endif ?>
								<td class="ta-left">
									<div class="contact-name">
										<?= $vd->esc($contact->name()) ?>
									</div>
									<?= $vd->esc($contact->company_name) ?>
								</td>
							<?php endforeach ?>
							</tr>
						</table>
					</div>
					<?php endif ?>
				</div>
				
			</div>
		</div>
	</div>
</div>

<script>
	
defer(function() {

	$(window).on('load', function() {

		var explanations = [
			'Does not include the traffic to PR Newswire press releases.',
			'The number of times the content is viewed, or has the potential to be viewed, that we can track.',
			'Value can differ due to your google history, location and other factors.',
			'Considers tweets containing the title or link within the last 7 days.',
		];

		var $explanation = null;
		var $conditions = $('span.source strong sup');
		$conditions.each(function() {
			var $condition = $(this);
			var $source = $condition.parents('span.source');
			var index = parseInt($condition.text())-1;
			var text = explanations[index];
			$source.on('mouseover', function() {
				window.enableFloatingText(explanations[index]);
			});
			$source.on('mouseout', function() {
				window.disableFloatingText();
			});
		});

	});

});

</script>

<?php 

	$render_basic = $ci->is_development();

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/jqvmap/jqvmap.css');
	$loader->add('lib/bootstrap-timepicker.css');
	$loader->add('lib/bootstrap-datepicker.css');
	$ci->add_eob($loader->render($render_basic));

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-timepicker.js');
	$loader->add('lib/bootstrap-datepicker.js');
	$loader->add('lib/jqvmap/jquery.vmap.min.js');
	$loader->add('lib/jqvmap/maps/jquery.vmap.world.js');
	$loader->add('lib/jqvmap/maps/jquery.vmap.usa.js');
	$loader->add('lib/bootbox.min.js');	
	$ci->add_eob($loader->render($render_basic));

?>