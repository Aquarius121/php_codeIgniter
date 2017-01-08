<?php if ($vd->m_content && !$vd->duplicate): ?>
<input type="hidden" name="id" value="<?= $vd->m_content->id ?>" />
<?php endif ?>

<div class="row">
	<div class="col-lg-8 col-md-7 form-col-1">
		<div class="panel panel-default">
			<div class="panel-body">
		
				<fieldset class="form-section select-distribution">
					<legend>Select Distribution</legend>
					<ul class="nopad marbot-15">

						<div class="required-callback"
							data-required-callback="distribution-choice"
							data-required-name="distribution"></div>

						<script>

						$(function() {

							required_js.add_callback("distribution-choice", function(value) {
								var response = { valid: false, text: " must be selected" };
								var is_premium_radios = $(".is-premium-radio");
								var radio = is_premium_radios.filter(":checked");
								response.valid = !! radio.length;
								return response;
							});

						});

						</script>

						<input type="hidden" name="is_premium" id="is-premium-save" value="1" 
							<?= value_if_test($vd->m_content && !$vd->m_content->is_premium, 'disabled') ?> />

						<?= $ci->load->view('manage/publish/partials/distribution/basic') ?>
						<?= $ci->load->view('manage/publish/partials/distribution/premium') ?>
						<?= $ci->load->view('manage/publish/partials/distribution/premium_plus') ?>
						<?= $ci->load->view('manage/publish/partials/distribution/premium_plus_state') ?>
						<?= $ci->load->view('manage/publish/partials/distribution/premium_plus_national') ?>
						<?= $ci->load->view('manage/publish/partials/distribution/premium_financial') ?>
						
					</ul>
				</fieldset>

				<?= $ci->load->view('manage/publish/partials/distribution/customize') ?>
				<?= $ci->load->view('manage/publish/partials/distribution/js') ?>

				<?= $ci->load->view('manage/publish/partials/pr-basic-information') ?>
				<?= $ci->load->view('manage/publish/partials/press-contact') ?>
				<?= $ci->load->view('manage/publish/partials/supporting-quote') ?>
				<?= $ci->load->view('manage/publish/partials/source') ?>
				<?= $ci->load->view('manage/publish/partials/tags') ?>
				<?= $ci->load->view('manage/publish/partials/web-images', 
					array('meta_extension' => array('manage/publish/partials/web-images-item-prn'),
					      'extension'      => array('manage/publish/partials/web-images-prn'))) ?>
				<?= $ci->load->view('manage/publish/partials/web-files') ?>
				<?= $ci->load->view('manage/publish/partials/relevant-resources') ?>
				<?= $ci->load->view('manage/publish/partials/web-video', 
					array('extension' => array('manage/publish/partials/web-video-prn.php'))) ?>
				<?= $ci->load->view('manage/publish/partials/outreach') ?>
				<?= $ci->load->view('manage/publish/partials/social-media') ?>

			</div>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-5 form-col-2">
		<div id="locked_aside">
			<div class="panel panel-default">
				<div class="panel-body">

					<div class="aside-properties">

						<?= $this->load->view('manage/publish/partials/status') ?>

						<div class="aside-properties aside-links guidelines">
							<div>
								<img src="<?= $vd->assets_base ?>im/editorial-process.png" />
								<a target="_blank" href="<?= $ci->website_url('editorial-process') ?>">Editorial Process</a>
							</div>
							<div>
								<img src="<?= $vd->assets_base ?>im/content-guidelines.png" />
								<a target="_blank" href="<?= $ci->website_url('content-guidelines') ?>">Content Guidelines</a>
							</div>
						</div>

						<fieldset class="ap-block ap-properties nomarbot">
						
							<legend>Select Industries</legend>
							<div class="select-beats select-beats-scroll">
								<?= $this->load->view('manage/publish/partials/select-three-beats') ?>
							</div>

							<div class="row form-group">
								<div class="col-lg-12">
									<div class="pull-right">
										<a href="#" class="add-industry">+ Add another industry</a>
									</div>
								</div>
							</div>

							<script>
							
							defer(function() {
								
								var scroll = $(".select-beats-scroll");
								var selects = $("#locked_aside select.category");
								selects.on_load_select();
									
								$(window).load(function() {
									selects.eq(0).addClass("required");
								});

								// ---------------------------

								var add_industry = $(".add-industry");
								var max_additional = 3;

								add_industry.on("click", function(ev){
									
									ev.preventDefault();
									
									var row = $(".select-category");
									var new_sel = $.create(row[0].tagName);
									new_sel.attr("class", row.attr("class"));
									new_sel.html(row.eq(0).html());
									new_sel.find("select.category").val("")
										.removeClass("required")
										.detach().replaceAll(new_sel.children())
										.on_load_select();
									var new_row = $.create("div");
									new_row.addClass("row form-group");
									new_row.append(new_sel);

									$(".select-beats").append(new_row);
									if (--max_additional === 0)
										add_industry.remove();

									// scroll to bottom
									scroll.scrollTop(99999);
							
								});
								
							});
							
							</script>

							<?= $ci->load->view('manage/publish/partials/save-buttons') ?>

							<?php if (Auth::is_admin_online()): ?>
							<div class="marbot-30"></div>
							<label class="checkbox-container marbot-15">
								<input type="checkbox" name="force_schedule" value="1" />
								<span class="checkbox"></span>
								<span>Force schedule</span>								
							</label>
							<p class="help-block nopad nomarbot">Schedules the press release without 
								waiting for payment confirmation.</p>
							<?php endif ?>

						</fieldset>
					
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php 

		$render_basic = $ci->is_development();

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/moment.min.js');
		$ci->add_eob($loader->render($render_basic));

		$loader = new Assets\CSS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('css/rocker.css');
		$ci->add_eob($loader->render($render_basic));

		if (Auth::is_admin_online())
		{
			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/js-beautify/beautify-html.js');
			$loader->add('lib/ace-editor/ace.js');
			$loader->add('js/rocker.js');
			$ci->add_eob($loader->render($render_basic));
		}

	?>

	<script>
	
	$(function() {

		if (is_desktop()) {
			var options = { offset: { top: 100 } };
			$.lockfixed("#locked_aside", options);
		}

	});
	
	</script>

	<?php if ($vd->requires_downstream_update): ?>
	<?= $ci->load->view_html('manage/publish/partials/downstream_update'); ?>
	<?php endif ?>
	
</div>