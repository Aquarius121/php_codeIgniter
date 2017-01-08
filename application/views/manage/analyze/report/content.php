<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" />
<?php if ($vd->report_resources): ?>
<?= $vd->report_resources ?>
<?php endif ?>

<?php if ($vd->is_printable): ?>
<div class="report-container printable content-analyze">
<?php else: ?>
<div class="report-container not-printable content-analyze">
<?php endif ?>

	<div class="printable-page">

		<?= $vd->report_pre_header ?>
		<?php if (!$vd->report_skip_header): ?>
		<?= $ci->load->view_return('manage/analyze/report/partials/header.php', 
			array('report_title' => 'Content Distribution Stats')) ?>
		<?php endif ?>
		<?= $vd->report_post_header ?>

		<?php if (!$vd->report_skip_header): ?>
		<?php if ($vd->report_header_text): ?>
			<div class="header-text with-border">
				<?= $vd->report_header_text ?>
			</div>
		<?php elseif (Model_Setting::value('dist_header_text')): ?>
			<div class="header-text with-border">
				<?= Model_Setting::value('dist_header_text') ?>
			</div>
		<?php endif ?>
		<?php endif ?>

		<div class="report-details with-border marbot">
			<div class="with-border">
				<div class="details-label with-border">Content</div>
				<div class="details-value"><?= $vd->esc($vd->m_content->title) ?></div>	
			</div>		
			<div class="with-border">
				<div class="details-label with-border">Published</div>
				<div class="details-value">
					<?php $dt_publish = Date::out($vd->m_content->date_publish) ?>
					<?= $dt_publish->format('M j, Y') ?>
					<span class="text-muted"><?= $dt_publish->format('H:i T'); ?></span>
				</div>
			</div>
		</div>

		<div id="analyze-sources" class="clearfix">
			<span class="source">
				<strong><?= $vd->hits ?><sup>1</sup></strong> <em>Views</em>
			</span>				
			<?php if (isset($vd->impressions)): ?>
			<span class="source">
				<strong><?= $vd->impressions + $vd->hits 
					?>+<sup>2</sup></strong> <em>Impressions</em>
			</span>
			<?php endif ?>
			<?php if ($vd->m_content->is_premium && $vd->m_content->is_published): ?>
			<span class="source" id="dist-sites">
				<?php if ($vd->m_content->is_legacy): ?>
				<strong>-</strong>
				<?php else: ?>
				<strong><?= (int) $vd->dist_count ?></strong>
				<?php endif ?>
				<em>Distribution</em>
			</span>
			<span class="source" id="google-results">
				<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
				<script>
				
				$(function() {

					var container = $("#google-results");
					var url = "manage/analyze/content/google_results/<?= 
						$vd->m_content->id ?>?&request_data_key=<?= 
						$ci->rdata->__key ?>";

					container.load(url, window.update_sources_bar);

				});
				
				</script>
			</span>
			<?php endif ?>
			<span class="source" id="twitter-shares">
				<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
				<script>
				
				$(function() {

					var container = $("#twitter-shares");
					var url = "manage/analyze/content/twitter_shares/<?= 
						$vd->m_content->id ?>?&request_data_key=<?= 
						$ci->rdata->__key ?>";

					container.load(url, window.update_sources_bar);

				});
				
				</script>
			</span>
			<span class="source" id="facebook-shares">
				<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
				<script>
				
				$(function() {

					var container = $("#facebook-shares");
					var url = "manage/analyze/content/facebook_shares/<?= 
						$vd->m_content->id ?>?&request_data_key=<?= 
						$ci->rdata->__key ?>";

					container.load(url, window.update_sources_bar);

				});
				
				</script>
			</span>
			<script>
			$(function() {

				$(window.update_sources_bar = function() {
				
					var sources = $("#analyze-sources");
					var sources_width = sources.width();
					var sources_spans = sources.children("span.source");
					var sources_count = sources_spans.size();
				
					sources_width -= 10 * (sources_count - 1);
					sources_width  = sources_width / sources_count;
					sources_width -= 2;
					sources_width  = Math.floor(sources_width);
					sources_spans.width(sources_width);
					
				});

			});
			</script>
		</div>

		<div class="row marbot-20">
			<div class="col-lg-12 grid-report">
				<div>1. Does not include the traffic to PR Newswire press releases.</div>
				<div>2. The number of times the content is viewed, or has the potential to be viewed, that we can track.</div>
				<div>3. Value can differ due to your google history, location and other factors.</div>
			</div>
		</div>

		<h2><?= Model_Content::full_type($vd->m_content->type) ?> Views by Date</h2>
					
		<div class="row marbot-30">
			<div class="col-lg-12">
				<div class="total-views-panel stats-loader" style="width: 940px;">
					<div class="chart marbot-20">
						<?= $vd->views_chart ?>
					</div>
				</div>
			</div>
		</div>

		<h2><?= Model_Content::full_type($vd->m_content->type) ?> Views by Time of Day</h2>
		<div class="h2-description">
			<?php if ($vd->report_is_branded): ?>
				Time data is shown using the your company timezone: <a href="<?= 
					$this->newsroom->url('manage/newsroom/company') ?>"><?= 
					$vd->esc(TimeZone::abbreviation($this->newsroom->timezone)) ?></a>
			<?php else: ?>
				Time data is shown using the your following timezone: 
				<span class="status-info"><?= $vd->esc(TimeZone::abbreviation($this->newsroom->timezone)) ?></span>
			<?php endif ?>
		</div>

		<div class="row marbot-30">
			<div class="col-lg-12">
				<div class="total-views-panel stats-loader" style="width: 940px;">
					<div class="chart marbot-20">
						<?= $vd->hours_chart ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ($vd->is_printable): ?>
		<?= $vd->report_pre_footer ?>
		<?php if (!$vd->report_skip_footer): ?>
		<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
		<?php endif ?>
		<?= $vd->report_post_footer ?>
		<?php endif ?>

	</div>

	<div class="printable-page">

		<?php if ($vd->is_printable): ?>
		<?= $vd->report_pre_header ?>
		<?php if (!$vd->report_skip_header): ?>
		<?= $ci->load->view('manage/analyze/report/partials/header.php') ?>
		<?php endif ?>
		<?= $vd->report_post_header ?>
		<?php endif ?>

		<h2>Visitors Grouped by Location</h2>

		<div class="row">
			
			<div class="col-lg-8 col-md-8 col-sm-8 bordered vector-map">
				<div id="world-map" class="marbot-15">
					<div class="with-border stats-loader"></div>
				</div>
				<div id="us-states-map">
					<div class="with-border stats-loader"></div>
				</div>
				<script>
				
				$(function() {
					
					var is_for_pdf = 1;
					var world_map_url = "manage/analyze/content/world_map/<?= 
						$vd->m_content->id ?>/561px/300px?request_data_key=<?= 
						$ci->rdata->__key ?>";

					var world_map = $("#world-map .stats-loader");
					world_map.load(world_map_url, function() {
						world_map.removeClass("stats-loader");
					});

					var us_states_map_url = "manage/analyze/content/us_states_map/<?= 
						$vd->m_content->id ?>/561px/300px?request_data_key=<?= 
						$ci->rdata->__key ?>";

					var us_states_map = $("#us-states-map .stats-loader");
					us_states_map.load(us_states_map_url, function() {
						us_states_map.removeClass("stats-loader");
					});
					
				});
				
				</script>
			</div>

			<div class="col-lg-4 col-md-4 col-sm-4 with-border" id="geolocation">
				<div class="locations stats-loader"></div>
				<script>
				
				$(function() {
					
					var locations_url = "manage/analyze/content/geolocation/<?= 
						$vd->m_content->id ?>?request_data_key=<?= 
						$ci->rdata->__key ?>";
					
					var locations = $("#geolocation .locations");
					locations.load(locations_url, function() {
						locations.removeClass("stats-loader");
						if (locations.is(":empty")) {
							$("#geolocation").removeClass("with-border");
						}
					});
					
				});
				
				</script>
			</div>
			
		</div>

		<?php if ($vd->is_printable): ?>
		<?= $vd->report_pre_footer ?>
		<?php if (!$vd->report_skip_footer): ?>
		<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
		<?php endif ?>
		<?= $vd->report_post_footer ?>
		<?php endif ?>

	</div>

	<div class="printable-page">

		<?php if ($vd->is_printable): ?>
		<?= $vd->report_pre_header ?>
		<?php if (!$vd->report_skip_header): ?>
		<?= $ci->load->view('manage/analyze/report/partials/header.php') ?>
		<?php endif ?>
		<?= $vd->report_post_header ?>
		<?php else: ?>
		<div class="clearfix marbot-20">&nbsp;</div>
		<?php endif ?>

		<h2>Search Engine Visibility</h2>

		<div class="distribution-search clearfix">					
			<a class="distribution-google-search pull-left with-border" href="<?= Google_Search_Result_Count::url($vd->m_content->title)
				?>"><img src="<?= $vd->assets_base ?>im/distribution-google.png" /></a>
			<a class="distribution-google-news pull-left with-border" href="<?= Google_News_Search_Results::url($vd->m_content->title)
				?>"><img src="<?= $vd->assets_base ?>im/distribution-google-news.png" /></a>	
			<a class="distribution-bing pull-left with-border" href="<?= Bing_Search_Result_Count::url($vd->m_content->title)
				?>"><img src="<?= $vd->assets_base ?>im/distribution-bing.png" /></a>	
			<a class="distribution-yahoo pull-left with-border" href="<?= Yahoo_Search_Result_Count::url($vd->m_content->title)
				?>"><img src="<?= $vd->assets_base ?>im/distribution-yahoo.png" /></a>	
			<a class="distribution-duckduckgo pull-left with-border" href="<?= DuckDuckGo_Search_Result_Count::url($vd->m_content->title)
				?>"><img src="<?= $vd->assets_base ?>im/distribution-duckduckgo.png" /></a>	
		</div>

		<h2>Online Distribution Pick-Ups</h2>
		<div class="h2-description">
			The following links are a sampling of your release as it appears
			on the news and media sites listed below. 
		</div>

		<div class="distribution-sites-container with-border table-responsive" 
			id="distribution-anchor">
			<table class="distribution-sites table">
				<tr>
				<?php foreach ($vd->services as $k => $service): ?>
				<?php if ($k % 2 === 0 && $k > 0): ?>
				<?php if ($vd->is_printable && ($k === 24 || (($k-24) % 28 === 0 && $k > 30))): ?>
					</tr></table></div>
						<?= $vd->report_pre_footer ?>
						<?php if (!$vd->report_skip_footer): ?>
						<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
						<?php endif ?>
						<?= $vd->report_post_footer ?>
					</div>
					<div class="printable-page">
						<?= $vd->report_pre_header ?>
						<?php if (!$vd->report_skip_header): ?>
						<?= $ci->load->view('manage/analyze/report/partials/header.php') ?>
						<?php endif ?>
						<?= $vd->report_post_header ?>
						<h2>Online Distribution Pick-Ups (Continued)</h2>
						<div class="h2-description">
							The following links are a sampling of your release as it appears
							on the news and media sites listed below. 
						</div>
					<div class="distribution-sites-container with-border">
					<table class="distribution-sites table">
					<tr>
				<?php else: ?>
				</tr><tr>
				<?php endif ?>
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



		<?php if (count($vd->cbc_contacts)): ?>
		<div class="printable-page">
			<?= $vd->report_pre_header ?>
			<?php if (!$vd->report_skip_header): ?>
			<?= $ci->load->view('manage/analyze/report/partials/header.php') ?>
			<?php endif ?>
			<?= $vd->report_post_header ?>
			<h2>Media Outreach Contacts</h2>
			<div class="h2-description">
				The following media database contacts have been 
				informed through email about your press release.
			</div>
			<div class="media-outreach-container table-responsive">
				<table class="media-outreach-contacts table">
					<tr>
					<?php foreach ($vd->cbc_contacts as $k => $contact): ?>
					
					<?php if ($k % 2 === 0 && $k > 0): ?>
					<?php if ($vd->is_printable && ($k === 34 || (($k-34) % 36 === 0 && $k > 40))): ?>
						</tr></table></div>
							<?= $vd->report_pre_footer ?>
							<?php if (!$vd->report_skip_footer): ?>
							<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
							<?php endif ?>
							<?= $vd->report_post_footer ?>
						</div>
						<div class="printable-page">
							<?= $vd->report_pre_header ?>
							<?php if (!$vd->report_skip_header): ?>
							<?= $ci->load->view('manage/analyze/report/partials/header.php') ?>
							<?php endif ?>
							<?= $vd->report_post_header ?>
							<h2>Media Outreach Contacts (Continued)</h2>
							<div class="h2-description">
								The following media database contacts have been 
								informed through email about your press release.
							</div>
						<div class="media-outreach-container with-border">
						<table class="media-outreach-contacts table">
						<tr>
					<?php else: ?>
					</tr><tr>
					<?php endif ?>
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

			<?= $vd->report_pre_footer ?>
			<?php if (!$vd->report_skip_footer): ?>
			<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
			<?php endif ?>
			<?= $vd->report_post_footer ?>

		</div>
		<?php endif ?>

		<img class="sleep" src="sleep/2" />

		<?= $vd->report_pre_footer ?>
		<?php if (!$vd->report_skip_footer): ?>
		<?= $ci->load->view('manage/analyze/report/partials/footer.php') ?>
		<?php endif ?>
		<?= $vd->report_post_footer ?>

	</div>

</div>

<?php 

	$render_basic = $ci->is_development();

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/jqvmap/jquery.vmap.min.js');
	$loader->add('lib/jqvmap/maps/jquery.vmap.world.js');
	$loader->add('lib/jqvmap/maps/jquery.vmap.usa.js');
	$loader->add('lib/bootbox.min.js');	
	$ci->add_eob($loader->render($render_basic));

?>