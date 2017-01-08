<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" media="print" />

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Newsroom Stats</h2>
			</div>
			<div class="col-lg-6 page-title actions">
				<ul class="list-inline actions">
					<li><a href="manage/analyze/overall/report?date_start=<?= 
						$vd->dt_min->format('Y-m-d') ?>&amp;date_end=<?= 
						$vd->dt_max->format('Y-m-d') ?>" 
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
				</ul>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">


					<div class="content content-no-tabs">

						<h3 class="marbot-20"><span><?= $vd->esc($ci->newsroom->company_name) ?></span></h3>
						
						<div class="row">
							
							<div class="col-lg-4 big" id="stats-summary">
								<strong>
									<?php if ($vd->hits == $vd->hits_all_time): ?>
									<?= $vd->hits ?> Views
									<?php else: ?>
									<span class="status-info"><?= $vd->hits ?></span> out of 
										<span class="status-info"><?= $vd->hits_all_time ?></span>
									Views
									<?php endif ?>
								</strong>
							</div>
							<div class="col-lg-8">
								<form action="manage/analyze/overall" method="get">
									<div class="row">
										<div id="analyze-date-range">
											<div class="col-lg-3 col-xs-6 col-lg-offset-4 stats-input">
												<div class="input-group in-text-date in-text-add-on marbot-15">
													<input type="text" name="date_start" class="form-control col-lg-10" 
														data-date-format="yyyy-mm-dd" id="date-start"
														value="<?= $vd->dt_min->format('Y-m-d') ?>" />
													<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
												</div>
											</div>

											<div class="col-lg-3 col-xs-6 stats-input">
												<div class="input-group in-text-date in-text-add-on marbot-15">
													<input type="text" name="date_end" class="form-control  col-lg-10" 
														data-date-format="yyyy-mm-dd" id="date-end"
														value="<?= $vd->dt_max->format('Y-m-d') ?>" />
													<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
												</div>
											</div>

											<div class="col-lg-2 pull-right stats-button">	 					
												<button class="no-print btn btn-default col-lg-12 nomar" type="submit">
													Update
												</button>
											</div>
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
									</div>
								</form>
						
							</div>
						</div>
									
						<div class="row marbot-20">
							<div class="col-lg-12">
								<div class="chart">
									<?= $vd->views_chart ?>
								</div>
							</div>
						</div>

						<div class="row marbot-20">
							<div class="col-lg-12">
								<div class="chart">
									<?= $vd->hours_chart ?>
								</div>
							</div>
						</div>

						<div class="row">
							
							<div class="col-lg-8 col-xs-12 vector-map">
								<div id="world-map" class="marbot-15">
									<div class="with-border stats-loader"></div>
								</div>
								<div id="us-states-map">
									<div class="with-border stats-loader"></div>
								</div>
								<script>
								
								$(function() {
									
									var world_map_url = "manage/analyze/overall/world_map?date_start=<?= 
										$vd->dt_min->format('Y-m-d') ?>&date_end=<?= 
										$vd->dt_max->format('Y-m-d') ?>";
									
									var world_map = $("#world-map .stats-loader");
									world_map.load(world_map_url, function() {
										world_map.removeClass("stats-loader");
									});

									var us_states_map_url = "manage/analyze/overall/us_states_map?date_start=<?= 
										$vd->dt_min->format('Y-m-d') ?>&date_end=<?= 
										$vd->dt_max->format('Y-m-d') ?>";

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
										
										var locations_url = "manage/analyze/overall/geolocation?date_start=<?= 
											$vd->dt_min->format('Y-m-d') ?>&date_end=<?= 
											$vd->dt_max->format('Y-m-d') ?>";
										
										var locations = $("#geolocation .locations");
										locations.load(locations_url, function() {
											locations.removeClass("stats-loader");
										});

									});
									
									</script>
								</div>
							</div>
							
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
	$ci->add_eob($loader->render($render_basic));

?>