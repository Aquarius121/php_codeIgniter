<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-12">
				<h2>Who Should See Your Pitch?</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= $ci->load->view('manage/contact/pitch/partials/progress-bar') ?>
					<div class="col-lg-8 col-lg-offset-2 form-col-1">
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<input type="hidden" name="order_type" id="order-type" value="<?= $vd->esc(@$vd->pw_raw_data->order_type) ?>" />
        				<fieldset>
							<div id="pitch-writing-or-outreach" 
								class="pitch-writing-or-outreach ta-right 
								<?= value_if_test(@$vd->is_already_submitted, 'locked') ?>">
								<div class="col-xs-12 col-lg-5 pitch-select-outreach <?= value_if_test(@$vd->pw_raw_data->order_type == 
									Model_Pitch_Session::ORDER_TYPE_OUTREACH || !@$vd->pw_raw_data->order_type, 'active') ?>" 
									data-order-type="<?= Model_Pitch_Session::ORDER_TYPE_OUTREACH ?>"
									id="pitch-select-outreach">
									<img src="<?= $vd->assets_base ?>im/pw-outreach.png" />
									<h3>media outreach</h3>
									<div class="text-muted">Pitch writing with distribution to carefully
										selected contacts within your target industry(s).</div>
									<div class="pricing">
										<?php if ($vd->media_outreach_credits && $vd->media_outreach_credits->available()): ?>
											<?= $vd->media_outreach_credits->available() ?> Credit(s) Available
										<?php else: ?>
											Available for $<?= number_format($vd->media_outreach_item->price, 2) ?>
										<?php endif ?>
									</div>
								</div>
								<div class="col-xs-12 col-lg-5 pitch-select-writing <?= value_if_test(@$vd->pw_raw_data->order_type == 
									Model_Pitch_Session::ORDER_TYPE_WRITING, 'active') ?>" id="pitch-select-writing"
									data-order-type="<?= Model_Pitch_Session::ORDER_TYPE_WRITING ?>">
									<img src="<?= $vd->assets_base ?>im/pw-writing.png" />
									<h3>writing only</h3>
									<div class="text-muted">We write the pitch. You send it out to contacts that you've added or found in the media database. </div>
									<div class="pricing">
										<?php if ($vd->pitch_writing_credits && $vd->pitch_writing_credits->available()): ?>
											<?= $vd->pitch_writing_credits->available() ?> Credit(s) Available
										<?php else: ?>
											Available for $<?= number_format($vd->pitch_writing_item->price, 2) ?>
										<?php endif ?>
									</div>
								</div>
							</div>
							<script>

							$(function() {

								var select_writing  = $("#pitch-select-writing");
								var select_outreach = $("#pitch-select-outreach");
								var choice_writing  = $("#pitch-choice-writing");
								var choice_outreach = $("#pitch-choice-outreach");
								var order_type      = $("#order-type");
								var choices         = $("#pitch-writing-or-outreach");

								select_writing.on("click", function() {
									select_writing.addClass("active");
									select_outreach.removeClass("active");
									order_type.val(select_writing.data("order-type"));
									choice_outreach.slideUp();
									choice_writing.slideDown();
									choice_writing.find("input").removeClass("required-disabled do-not-submit");
									choice_writing.find("select").removeClass("required-disabled do-not-submit");
									choice_outreach.find("input").addClass("required-disabled do-not-submit");
									choice_outreach.find("select").addClass("required-disabled do-not-submit");
								});

								select_outreach.on("click", function() {
									select_outreach.addClass("active");
									select_writing.removeClass("active");
									order_type.val(select_outreach.data("order-type"));
									choice_writing.slideUp();
									choice_outreach.slideDown();
									choice_writing.find("input").addClass("required-disabled do-not-submit");
									choice_writing.find("select").addClass("required-disabled do-not-submit");
									choice_outreach.find("input").removeClass("required-disabled do-not-submit");
									choice_outreach.find("select").removeClass("required-disabled do-not-submit");
								});

								window.required_js.on_submit = function() {
									choice_writing.find(".do-not-submit").prop("disabled", true);
									choice_outreach.find(".do-not-submit").prop("disabled", true);
								};

								if (select_writing.hasClass("active"))
									select_writing.click();
								if (select_outreach.hasClass("active"))
									select_outreach.click();

								if (choices.hasClass("locked")) {
									select_outreach.off("click");
									select_writing.off("click");
								}

							});

							</script>
						</fieldlist>
						<div class="pitch-choice-writing" id="pitch-choice-writing"></div>
						<div class="pitch-choice-outreach" id="pitch-choice-outreach">

							<fieldset>
								<legend>
									Select the industry to target 
									<a class="tl" href="#" title="Select the industry most relevant
										to your press release. Each added industry will include 100 media contacts; adding
										a 2nd category will add another 100 media contacts and is an added fee.">
										<i class="fa fa-fw fa-question-circle"></i>
									</a>
								</legend>
								<div class="row form-group">
									<div class="<?= value_if_test(!@$vd->is_already_submitted || @$vd->pw_raw_data->beat_2_id, 'col-lg-6 col-md-6', 'col-lg-12') ?>">
										<select class="form-control show-menu-arrow category selectpicker" 
											name="beat_1_id" data-required-name="Industry" id="select-industry"
											data-required-use-parent="1">
											<option class="selectpicker-default" title="Select Industry" value=""
												<?= value_if_test(!@$vd->pw_raw_data->beat_1_id, 'selected') ?>
											>None</option>
											<?php foreach ($vd->beats as $group): ?>
											<optgroup label="<?= $vd->esc($group->name) ?>">
											<?php foreach ($group->beats as $beat): ?>
												<option value="<?= $beat->id ?>"
												<?= value_if_test(@$vd->pw_raw_data->beat_1_id === $beat->id,
													'selected') ?>>
													<?= $vd->esc($beat->name) ?>
												</option>
												<?php endforeach ?>
											</optgroup>
											<?php endforeach ?>
										</select>
										<script>

											$(function() {

												var select = $("#select-industry");
												select.on_load_select({ size: 10 });
												window.on_load_select(function() {
													select.addClass("required");
												});

											});

										</script>
									</div>
									

									<?php if (!@$vd->is_already_submitted || @$vd->pw_raw_data->beat_2_id): ?>
									<div class="col-lg-6 col-md-6">
										<div class="<?= value_if_test(@$vd->pw_raw_data->beat_2_id, 'dnone') ?>" id="add_category2">
											<a class="btn-insert" href="#" id="add_category2_link">
												<i class="icon-plus"></i> 
												<span>2nd Industry <b>$<?= $vd->second_cat_item->price ?></b> 
													<span class="btn-small">(+100 Contacts)</span>
												</span>
											</a>
										</div>

										<div class="relative <?= value_if_test(!@$vd->pw_raw_data->beat_2_id, 'dnone') ?>" id="category2">
											<select class="form-control show-menu-arrow category selectpicker pull-right" 
												name="beat_2_id" id="select-industry2">
												<option class="selectpicker-default" title="Select Industry" value=""
													<?= value_if_test(!@$vd->pw_raw_data->beat_2_id, 'selected') ?>
													>None</option>
												<?php foreach ($vd->beats as $group): ?>
												<optgroup label="<?= $vd->esc($group->name) ?>">
													<?php foreach ($group->beats as $beat): ?>
													<option value="<?= $beat->id ?>"
														<?= value_if_test((@$vd->pw_raw_data->beat_2_id === $beat->id), 'selected') ?>>
														<?= $vd->esc($beat->name) ?>
													</option>
													<?php endforeach ?>
												</optgroup>
												<?php endforeach ?>
											</select>
											<script>
											
												$(function() {

													var select = $("#select-industry2");
													select.on_load_select({ size: 10 });

												});
												
											</script>
											<?php if (!@$vd->is_already_submitted): ?>
												<button class="btn btn-mini btn-danger" id="remove_button"
													type="button">X</button>
											<?php endif ?>
										</div>
										<script>

											$(function() {

												$("#add_category2_link").click(function(ev) {
													ev.preventDefault();
													$("#add_category2").fadeOut(function() {
														$("#category2").fadeIn();
													});
												});	
												
												$("#remove_button").click(function(ev) {
													ev.preventDefault();
													$("#select-industry2").val("").trigger("change");
													$("#category2").fadeOut(function() {
														$("#add_category2").fadeIn();	
													});
												});

											});

										</script>
									</div>
									<?php endif ?>
								</div>
							</fieldset>
						
					

							<fieldset>
								<legend>
									Include the location of your story 
									<a class="tl" rel="tooltip" href="#" title='This location will help us select the
										proper media. Please be aware that we may pitch out to other U.S. regions if "Strictly
										Local Distribution Only" is not selected below.'>
										<i class="fa fa-fw fa-question-circle"></i>
									</a>
								</legend>
								<div class="row form-group">
									<div class="col-lg-6 col-md-6">
										<input type="text" class="form-control in-text required marbot-5" name="city" id="city" 
											placeholder="City" data-required-name="City"
											value="<?= $vd->esc(@$vd->pw_raw_data->city) ?>">
									</div>

									<div class="col-lg-6 col-md-6">
										<select class="form-control show-menu-arrow selectpicker category" 
											name="state_id" data-required-name="State" id="select-state"
											data-required-use-parent="1">
											<option class="selectpicker-default" title="Select State" value=""
												<?= value_if_test(!@$vd->pw_raw_data->state_id, 'selected') ?>>None</option>
											<?php foreach ($vd->states as $state): ?>
											<option value="<?= $state->id ?>"
												<?= value_if_test((@$vd->pw_raw_data->state_id === $state->id), 'selected') ?>>
												<?= $vd->esc($state->name) ?>
											</option>
											<?php endforeach ?>
										</select>
										<script>

											$(function() {

												var select = $("#select-state");
												select.on_load_select({ size: 10 });
												window.on_load_select(function() {
													select.addClass("required");
												});

											});

										</script>
									</div>
							</fieldset>

							<fieldset>
								<div class="row form-group">
									<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2">
										<legend>
											Select your distribution reach 
											<a class="tl" href="#" title="Select your type of distribution reach.
												National U.S. Distribution includes an extra 200 contacts and is 
												an added fee to order." rel="tooltip">
												<i class="fa fa-fw fa-question-circle"></i>
											</a>
										</legend>
								
										<select class="form-control show-menu-arrow selectpicker category" 
											name="distribution" data-required-name="Distribution Reach" 
											data-required-use-parent="1" id="select-distribution">
											<?php if ( ! @$vd->is_already_submitted || @$vd->pw_raw_data->distribution !== 
														Model_Pitch_Order::DISTRIBUTION_NATIONAL): ?>
											<option class="selectpicker-default" title="Select an Option" value=""
												<?= value_if_test(!@$vd->pw_raw_data->distribution, 'selected') ?>
												>Select an Option</option>
											<option value="<?= Model_Pitch_Order::DISTRIBUTION_LOCAL ?>"
												<?= value_if_test(@$vd->pw_raw_data->distribution === 
														Model_Pitch_Order::DISTRIBUTION_LOCAL, 'selected') ?>
												><?= Model_Pitch_Order::distribution_title(Model_Pitch_Order::DISTRIBUTION_LOCAL);
												 ?></option>
											<option value="<?= Model_Pitch_Order::DISTRIBUTION_LOCAL_REGIONAL ?>"
											<?= value_if_test(@$vd->pw_raw_data->distribution === 
														Model_Pitch_Order::DISTRIBUTION_LOCAL_REGIONAL, 'selected') ?>
											><?= Model_Pitch_Order::distribution_title(Model_Pitch_Order::
															DISTRIBUTION_LOCAL_REGIONAL);?></option>
											<?php endif ?>

											<?php if ( ! @$vd->is_already_submitted || @$vd->pw_raw_data->distribution === 
														Model_Pitch_Order::DISTRIBUTION_NATIONAL): ?>
											<option value="<?= Model_Pitch_Order::DISTRIBUTION_NATIONAL ?>"
												<?= value_if_test(@$vd->pw_raw_data->distribution === 
														Model_Pitch_Order::DISTRIBUTION_NATIONAL, 'selected') ?>
												><?= Model_Pitch_Order::distribution_title(Model_Pitch_Order::
													DISTRIBUTION_NATIONAL) ?> +$<?= $vd->nation_dist_item->price ?></option>
											<?php endif ?>
										</select>
										<script>

											$(function() {

												var select = $("#select-distribution");
												select.on_load_select({ size: 10 });
												window.on_load_select(function() {
													select.addClass("required");
													select.trigger("change");
												});

											});

										</script>
									</div>
								</div>

								<div class="row marbot-40"></div>
							</fieldset>
						</div>
								
						<div>
							<fiedset>
								<div class="row form-group">
									<div class="col-lg-3 col-xs-6">
										<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/1"
											class="btn btn-default ta-center col-lg-12"><b>&laquo; Back</b></a>
										</a>
									</div>

									<div class="col-lg-3 col-lg-offset-6 col-xs-6">
										<button type="submit" class="btn btn-primary pull-right col-lg-12 nomar" 
											name="is_continue" value="1">
											Continue &raquo;</button>
									</div>			
								</div>

							</fieldset>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>