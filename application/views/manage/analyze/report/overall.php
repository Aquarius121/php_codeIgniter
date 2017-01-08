<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" />
<div class="printable">

	<?= $ci->load->view('manage/analyze/report/partials/header.php',
		array('report_title' => 'Newsroom Stats'), true) ?>

	<div class="container-fluid">
		<div class="row">
		<div class="col-lg-12">
			<h3><?= $vd->esc($ci->newsroom->company_name) ?> Newsroom</h3>

				<div class="row">

					<div class="col-lg-4 col-md-4 stats-summary-report" id="stats-summary">
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

					<div class="col-lg-8 col-md-8 date-range-report">
						<form action="manage/analyze/overall" method="get">
							<div class="row">
								<div id="analyze-date-range">
									<div class="col-lg-3 col-md-3 col-xs-6 col-lg-offset-4 stats-input">
										<div class="input-group in-text-date in-text-add-on marbot-15">
											<input type="text" name="date_start" class="form-control col-lg-10" 
												data-date-format="yyyy-mm-dd" id="date-start"
												value="<?= $vd->dt_min->format('Y-m-d') ?>" />
											<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
										</div>
									</div>

									<div class="col-lg-3 col-md-3 col-xs-6 stats-input">
										<div class="input-group in-text-date in-text-add-on marbot-15">
											<input type="text" name="date_end" class="form-control  col-lg-10" 
												data-date-format="yyyy-mm-dd" id="date-end"
												value="<?= $vd->dt_max->format('Y-m-d') ?>" />
											<span class="input-group-addon"><i class="fa fa-fw fa-calendar"></i></span>
										</div>
									</div>
								</div>								
							</div>
						</form>
				
					</div>
								
				</div>
							
				<div class="row">
					<div class="col-lg-12">
						<div class="chart" style="width: 720px; height: 270px;">
							<?= $vd->views_chart ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="chart" style="width: 720px; height: 270px;">
							<?= $vd->hours_chart ?>
						</div>
					</div>
				</div>

				<div class="row-fluid report printable-break-before printable-break-avoid">
					<div class="span12">
						<h3>Top Locations</h3>
						<div class="locations" style="width: 720px; height: 270px;">
							<?= $ci->report_geolocation() ?>
						</div>
					</div>				
				</div>

			</div>
		</div>
	</div>

	<img class="sleep" src="sleep/2" />

</div>