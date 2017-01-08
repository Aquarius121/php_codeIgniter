<div class="container-fluid">
	<div class="panel panel-default <?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'form-col', 'form-col-1') ?>">
		<div class="panel-body">
			<div class="row">

				<div class="col-lg-12">
					<?= $ci->load->view('manage/writing/partials/progress-bar') ?>
					<header>
						<div class="row">
							<div class="col-lg-12 page-title">
								<h2>Press Release Details</h2>
							</div>
						</div>
					</header>
					<hr class="marbot-30" />
		
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<div class="row">
							<div class="<?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'col-lg-8 col-md-8 form-col-1', 'col-lg-12') ?> ">
								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<select id="select-angle" name="writing_angle" data-required-name="Annoucement Nature"
											class="form-control selectpicker descriptive-bootstrap-select show-menu-arrow col-xs-12 marbot-20 has-placeholder"
											data-required-use-parent="1">
											<option class="status-false" value="">None</option>		
											<option class="writing-angle-option" 
												value="<?= Model_Writing_Order::ANGLE_PROBLEM ?>"
												<?= value_if_test(Model_Writing_Order::ANGLE_PROBLEM == 
													@$vd->wr_raw_data->writing_angle, 'selected') ?>
												data-content="Problem and Solution
													<span>Introduces a problem and presents the website 
													or product as a solution.</span>">
												Problem and Solution
											</option>

											<option class="writing-angle-option" 
												value="<?= Model_Writing_Order::ANGLE_DISCOUNT ?>"
												<?= value_if_test(Model_Writing_Order::ANGLE_DISCOUNT == 
													@$vd->wr_raw_data->writing_angle, 'selected') ?>
												data-content="Discount or Special
													<span>Discount offer or special offer announcement.</span>">
												Discount or Special
											</option>
											
											<option class="writing-angle-option" 
												value="<?= Model_Writing_Order::ANGLE_WEBSITE ?>"
												<?= value_if_test(Model_Writing_Order::ANGLE_WEBSITE == 
													@$vd->wr_raw_data->writing_angle, 'selected') ?>
												data-content="Website or Product Launch">
												Website or Product Launch
											</option>
											
											<option class="writing-angle-option" 
												value="<?= Model_Writing_Order::ANGLE_ANNOUNCEMENT ?>"
												<?= value_if_test(Model_Writing_Order::ANGLE_ANNOUNCEMENT == 
													@$vd->wr_raw_data->writing_angle, 'selected') ?>
												data-content="Special Company Announcement
													<span>Such as a company merge, company 
													acquisition, anniversary etc.</span>">
												Special Company Announcement
											</option>

											<option class="writing-angle-option" 
												value="<?= Model_Writing_Order::ANGLE_OTHER ?>"
												<?= value_if_test(Model_Writing_Order::ANGLE_OTHER == 
													@$vd->wr_raw_data->writing_angle, 'selected') ?>
												data-content="Other">
												Other
											</option>
										</select>							
										<script>

										$(function() {
											
											var select = $("#select-angle");
											select.on_load_select({ showContent: false });
											
											$(window).load(function() {
												select.addClass("required");
												select.addClass("has-placeholder");
												select.trigger("change");
											});
											
											select.on("change", function() {
												select.toggleClass("invalid", !select.val());
											});
											
										});
										
										</script>
										<strong class="placeholder">Nature of Annoucement</strong>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<textarea class="form-control in-text col-xs-12 required required-callback has-placeholder"
											rows="5" name="angle_detail" id="angle_detail" required
											placeholder="What Is Being Announced?"
											data-required-name="Purpose"
											data-required-callback="angle-detail-min-words"><?= 
											$vd->esc(@$vd->wr_raw_data->angle_detail) 
										?></textarea>
										<strong class="placeholder">What Is Being Announced?</strong>
										<p class="help-block" id="angle_detail_countdown_text">
											<span id="angle_detail_countdown">400</span> Characters Left. 
										<script>
								
										$(function() {
											
											$("#angle_detail").limit_length(400, 
												$("#angle_detail_countdown_text"), 
												$("#angle_detail_countdown"));
											
											required_js.add_callback("angle-detail-min-words", function(value) {
												var response = { valid: false, text: "must have at least 20 words" };
												response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){20,}/i.test(value);
												return response;
											});
											
										});
										
										</script>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<textarea class="form-control in-text col-xs-12 has-placeholder"
											rows="5" name="additional_comments" id="additional_comments"
											placeholder="Additional Comments"><?= 
											$vd->esc(@$vd->wr_raw_data->additional_comments) 
										?></textarea>
										<strong class="placeholder">Additional Comments</strong>
										<p class="help-block" id="additional_comments_countdown_text">
											<span id="additional_comments_countdown">400</span> Characters Left. 
										<script>
								
										$(function() {
											
											$("#additional_comments").limit_length(400, 
												$("#additional_comments_countdown_text"), 
												$("#additional_comments_countdown"));
											
										});
										
										</script>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<input type="text" name="primary_keyword" required
											class="form-control col-xs-12 in-text has-placeholder required"
											value="<?= $vd->esc(@$vd->wr_raw_data->primary_keyword) ?>"
											placeholder="Primary Keyword"
											data-required-name="Primary Keyword" />
										<strong class="placeholder">Primary Keyword</strong>
										<p class="help-block">This will be used in the headline of your press release.
											Enter the important word or phrase that you would like the writer to optimize your release for.</p>
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12 placeholder-container">
										<input type="text" name="tags" required id="tags"
											class="form-control col-xs-12 in-text has-placeholder required-callback"
											value="<?= $vd->esc(implode(', ', $vd->m_content->get_tags())) ?>"
											placeholder="Additional Tags"
											data-required-name="Additional Tags"
											data-required-callback="tags-count" />
										<strong class="placeholder">Additional Tags</strong>
										<p class="help-block">Additional important words or phrases that
											can be included in the press release. The tags will be
											displayed after the press release too. Separate each 
											word or phrase with a comma.</p>
										<script>
						
										$(function() {
											
											var tags = $("#tags");	
																	
											required_js.add_callback("tags-count", function(value) {

												var response = { valid: false, text: "must have between 3 and 12 tags" };
												var exploded = $.parse_comma_delim(tags.val());
												var index = {};

												$.each(exploded, function(idx, value) {
													var uniform = window.TAG_uniform(value);
													if (index[uniform] === undefined)
														index[uniform] = value;
												});

												exploded = $.map(index, function(value) {
													return value;
												});

												response.valid = exploded.length >= 3 && exploded.length <= 12;
												tags.val(exploded.join(", "));
												return response;

											});
											
										});
										
										</script>
									</div>
								</div>

								<div class="row form-group marbot-40">
								<?php $selected_beats = $vd->m_content ? $vd->m_content->get_beats() : array(); ?>
								<?php for ($i = 1; $i <= 2; $i++): ?>
								<?php $selected_beat_id = (int) @$selected_beats[$i-1]->id; ?>
									<div class="col-lg-6 select-category select-right placeholder-container">
										<select class="form-control selectpicker show-menu-arrow category col-xs-12 has-placeholder" 
											data-live-search="true"  data-size="10" 
											name="beats[]" data-required-name="Category">
											<option class="selectpicker-default" title="Select Category" value=""
												<?= value_if_test(!$selected_beat_id, 'selected') ?>>None</option>
											<?php foreach ($vd->beats as $group): ?>
											<?php if (!$group->is_listed) continue; ?>
											<optgroup label="<?= $vd->esc($group->name) ?>">
												<?php foreach ($group->beats as $beat): ?>
												<?php if (!$beat->is_listed) continue; ?>
												<option value="<?= $beat->id ?>"
													<?= value_if_test(($selected_beat_id === (int) $beat->id), 'selected') ?>>
													<?= $vd->esc($beat->name) ?>
												</option>
												<?php endforeach ?>
											</optgroup>
											<?php endforeach ?>
										</select>
										<?php if ($i == 1): ?>
											<strong class="placeholder">Category</strong>
										<?php endif ?>
									</div>
								<?php endfor ?>

								<script>
						
								$(function() {
							
									var selects = $("select.category");
									selects.on_load_select();
									var cat_1 = selects.eq(0);
									cat_1.attr("data-required-use-parent", "1");
										
									$(window).load(function() {
										cat_1.addClass("required");
									});
									
								});
								
								</script>
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
									<?= $ci->load->view('manage/writing/partials/editor-comments') ?>
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