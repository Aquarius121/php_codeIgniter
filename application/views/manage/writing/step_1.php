<div class="container-fluid">
	<div class="panel panel-default <?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'form-col', 'form-col-1') ?>">
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
		
					<?= $ci->load->view('manage/writing/partials/progress-bar') ?>
					<header>
						<div class="row">
							<div class="col-lg-12 page-title">
								<h2>Company Information</h2>
							</div>
						</div>
					</header>
					<hr class="marbot-30 visible-md visible-lg" />
					
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<div class="row">
							<div class="<?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'col-lg-8 col-md-8 form-col-1', 'col-lg-12') ?> ">
								<div class="row form-group">
									<div class="col-lg-12 col-xs-12">
										<div class="placeholder-container">
											<input type="text" name="company_name" required
												class="col-xs-12 col-lg-12 in-text form-control has-placeholder required"
												value="<?= $vd->esc(@$ci->newsroom->company_name) ?>"
												placeholder="Company Name" data-required-name="Company Name" />
											<strong class="placeholder">Company Name</strong>
										</div>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 placeholder-container">
										<input class="col-lg-12 col-xs-12 has-placeholder in-text form-control  required" name="address_street" 
											placeholder="Street Address" type="text" required
											value="<?= $vd->esc(@$vd->m_profile->address_street) ?>" />
										<strong class="placeholder">Street Address</strong>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12  placeholder-container">
										<input class="has-placeholder in-text form-control col-xs-12"  name="address_apt_suite"
											type="text" placeholder="Apt / Suite" 
											value="<?= $vd->esc(@$vd->m_profile->address_apt_suite) ?>" />
										<strong class="placeholder">Apt / Suite</strong>
									</div>
								</div>

								<div class="row form-group">
									
									<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 placeholder-container">
										<input class="has-placeholder in-text form-control col-xs-12 required" type="text" 
											name="address_city" placeholder="City" required
											value="<?= $vd->esc(@$vd->m_profile->address_city) ?>" />
										<strong class="placeholder">City</strong>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 placeholder-container">
										<input class="has-placeholder in-text form-control col-xs-12" type="text" 
											name="address_state" placeholder="State / Region"
											value="<?= $vd->esc(@$vd->m_profile->address_state) ?>" />
										<strong class="placeholder">State / Region</strong>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 placeholder-container">
										<input class="has-placeholder in-text form-control col-xs-12" type="text" 
											name="address_zip" placeholder="Zip Code"
											value="<?= $vd->esc(@$vd->m_profile->address_zip) ?>" />
										<strong class="placeholder">Zip Code</strong>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<select id="select-country" name="address_country_id" data-required-name="Country"
											class="form-control selectpicker show-menu-arrow col-lg-12 col-xs-12 marbot-20 has-placeholder"
											data-required-use-parent="1">
											<option class="selectpicker-default" title="Select Country" value=""
												<?= value_if_test(!@$vd->m_profile->address_country_id, 'selected') ?>>None</option>
											<?php foreach ($vd->common_countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->m_profile->address_country_id == $country->id), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
											<option data-divider="true"></option>
											<?php foreach ($vd->countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->m_profile->address_country_id == $country->id && 
													!$country->is_common), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
										</select>
										<script>

										$(function() {
											
											var select = $("#select-country");
											select.on_load_select({ size: 10 });
											$(window).load(function() {
												select.addClass("required");
												select.trigger("change");
											});
											
											select.on("change", function() {
												select.toggleClass("invalid", !select.val());
											});
											
										});
										
										</script>
										<strong class="placeholder">Country</strong>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<input type="text" name="phone"
											class="col-xs-12 in-text form-control has-placeholder required"
											value="<?= $vd->esc(@$vd->m_profile->phone) ?>"
											placeholder="Phone" data-required-name="Phone" />
										<strong class="placeholder">Phone</strong>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<input type="url" name="website" required
											class="col-xs-12 in-text form-control has-placeholder required"
											value="<?= $vd->esc(@$vd->m_profile->website) ?>"
											placeholder="Website" data-required-name="Website" />
										<strong class="placeholder">Website</strong>
									</div>
								</div>

								
								<div class="row form-group">
									<div class="col-lg-12 placeholder-container placeholder-simple writing-process-logo">
										<input type="hidden" id="logo-image-id" name="logo_image_id" 
											value="<?= @$vd->m_custom->logo_image_id ?>" />
										<div class="row has-placeholder valid">
											<div class="col-lg-3 image-upload-left image-container scaled nopad marbot-10">
												<?php if (@$vd->m_custom->logo_image_id): ?>
												<?php $lo_im = Model_Image::find($vd->m_custom->logo_image_id); ?>
												<?php $lo_variant = $lo_im->variant('header-thumb'); ?>
												<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
												<img id="logo-image-thumb" src="<?= $lo_url ?>" />
												<?php else: ?>
												<img id="logo-image-thumb" class="loader blank" />
												<?php endif ?>
											</div>
											<div class="col-lg-8">
												<div class="row">
													<div class="col-lg-12">
														<div class="row no-overflow marbot-5" id="logo-image-upload">
															<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 file-upload-faker">
																<div class="fake row input-group nomar-left">
																	<div class="text-input">
																		<input type="text" placeholder="Select Image" class="form-control in-text col-lg-12 fake-text" />
																	</div>
																	<div class="input-group-btn">
																		<button class="btn btn-primary fake-button" type="button">Browse</button>
																	</div>
																</div>
																<div class="real row">
																	<input class="in-text col-lg-12 real-file required-no-submit" type="file" name="image" />
																</div>
															</div>
															<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 nopad-left">
																<button type="button" class="file-upload-faker-button btn btn-default col-lg-12 remove-button">
																	Remove
																</button>
															</div>
														</div>
														<p class="help-block">
															<?php $v_header = $ci->conf('v_sizes', 'header') ?>
															We enforce a <span class="darker">maximum size of 
															<?= $v_header->width ?>x<?= $v_header->height ?></span>.
															The image will be resized if it exceeds that.
															Transparency will be preserved.
														</p>
													</div>
												</div>
											</div>
										</div>				
										<strong class="placeholder">Company Logo</strong>
										<script>
					
										$(function() {
											
											var li_upload = $("#logo-image-upload");
											
											li_upload.find(".real-file").on("change", function() {
												
												var real_file = $(this);
												var fake_text = li_upload.find(".fake-text");
												var li_thumb = $("#logo-image-thumb");
												
												fake_text.removeClass("error");
												fake_text.val(real_file.val());
												real_file.attr("disabled", true);
												li_thumb.removeClass("blank");
												li_thumb.addClass("loader");
												li_thumb.removeAttr("src");
												
												var image_id_input = $("input#logo-image-id");
												
												var on_upload = function(res) {
													
													if (res.status)
													{
														real_file.attr("disabled", false);
														image_id_input.val(res.image_id);
														li_thumb.removeClass("loader");
														li_thumb.attr("src", res.files["header-thumb"]);
													}
													else
													{
														fake_text.addClass("error");
														real_file.attr("disabled", false);
													}
													
												};
												
												real_file.ajax_upload({
													callback: on_upload,
													url: "manage/image/upload",
													data: { variants: ["header", "header-thumb",
														"header-finger", "header-sidebar"] }
												});
												
											});

											li_upload.find(".remove-button").on("click", function() {
												
												$("input#logo-image-id").val("");
												li_upload.find(".fake-text").val("");
												
												var li_thumb = $("#logo-image-thumb");
												li_thumb.addClass("loader blank");
												li_thumb.removeAttr("src");
												
											});
											
										});
										
										</script>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<textarea class="form-control in-text col-xs-12 required required-callback has-placeholder"
											rows="5" name="summary" id="summary" required 
											placeholder="Short Company Description" data-required-name="Summary"
											data-required-callback="summary-min-words"><?= 
											$vd->esc(@$vd->m_profile->summary) 
										?></textarea>
										<strong class="placeholder">Short Company Description</strong>
										<p class="help-block" id="summary_countdown_text">
											<span id="summary_countdown">250</span> Characters Left. 
											This will be visible on the sidebar.</p>
										<script>
								
										$(function() {
											
											$("#summary").limit_length(250, 
												$("#summary_countdown_text"), 
												$("#summary_countdown"));
											
											required_js.add_callback("summary-min-words", function(value) {
												var response = { valid: false, text: "must have at least 10 words" };
												response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){10,}/i.test(value);
												return response;
											});
											
										});
										
										</script>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12">
										<button type="submit" name="is_continue" value="1"
											class="btn btn-primary marbot-30">Continue</button>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 form-col-2">
								<div class="aside_tips" id="locked_aside">
									<?= $ci->load->view('manage/writing/partials/editor-comments', array('vd' => $vd), true) ?>
								</div>
							</div>
						</div>
					</form>

					<?php 

						$loader = new Assets\JS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('js/required.js');
						$loader->add('lib/jquery.lockfixed.js');
						$render_basic = $ci->is_development();
						$ci->add_eob($loader->render($render_basic));

					?>
					
					<script>
					
					$(function() {

						if (is_desktop()) {
							var options = { offset: { top: 100 } };
							$.lockfixed("#locked_aside", options);
						}

					});
					
					</script>
				</div>
			</div>
		</div>
		
	</div>
</div>