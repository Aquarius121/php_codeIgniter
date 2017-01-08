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
				<h2>Almost Done, Please Review Your Details</h2>
			</div>
		</div>
	</header>

	<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
		<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="col-lg-12">
						<?= $ci->load->view('manage/contact/pitch/partials/progress-bar') ?>

					
						<div class="<?= value_if_test(@$vd->is_already_submitted, 'col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-xs-12', 'col-lg-8 col-md-7 col-xs-12 mobile-nopad') ?>">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Media Outreach Summary</h3>
								</div>

								<div class="panel-body pad-top-10">
									<ul class="summary-list">
										<li>
											<dl class="marbot-15">
												<dt>Estimated Delivery Date: </dt> 
												<dd>
													<span id='delivery_date_standard' class="
													<?= value_if_test(@$vd->pw_raw_data->delivery == 
														Model_Pitch_Order::DELIVERY_RUSH, 'hidden') ?>">
													<?= $vd->date_after_3_days->format('F d, Y') ?>
													</span>
													<span id="delivery_date_rush" class="
														<?= value_if_test(!@$vd->pw_raw_data->delivery, 'hidden') ?>
														<?= value_if_test(@$vd->pw_raw_data->delivery == 
															Model_Pitch_Order::DELIVERY_STANDARD, 'hidden') ?>">
														<?= $vd->date_after_24_hours->format('F d, Y') ?> (RUSH)
													</span>
												</dd>
											</dl>

											<?php if ( ! @$vd->is_already_submitted || @$vd->pw_raw_data->delivery === 
														Model_Pitch_Order::DELIVERY_STANDARD): ?>
											<dl>
												<label class="radio-container louder">
													<input type="radio" name="delivery" id="is-standard" value="standard" 
														class="is_delivery_radio" 
													<?= value_if_test(!@$vd->pw_raw_data->delivery, 'checked') ?>
													<?= value_if_test(@$vd->pw_raw_data->delivery == 
														Model_Pitch_Order::DELIVERY_STANDARD, 'checked') ?>
													/>
													<span class="radio"></span>
													<dt><?= ucwords(Model_Pitch_Order::DELIVERY_STANDARD) ?>:</dt>
													<dd>2-3 days</dd>  
												</label>
											</dl>
											<?php endif ?>

											<?php if ( ! @$vd->is_already_submitted || @$vd->pw_raw_data->delivery === 
														Model_Pitch_Order::DELIVERY_RUSH): ?>
											<dl>
												<label class="radio-container louder">
													<input type="radio" name="delivery" id="is-rush" value="rush"
														class="is_delivery_radio"   
													<?= value_if_test(@$vd->pw_raw_data->delivery == 
														Model_Pitch_Order::DELIVERY_RUSH, 'checked') ?>
													/>
													<span class="radio"></span>
													<dt>Add <?= ucwords(Model_Pitch_Order::DELIVERY_RUSH) ?>:</dt>
													<dd>24 hours (Monday to Friday)
														<span class="label label-info">+$<?= $vd->rush_item->price ?></span>
													</dd>
												</label>
											</dl>
											<?php endif ?>

										</li>
										<li>
											<dl>
												<dt>Selected Content:</dt> 
												<dd><?= Model_Content::full_type(@$vd->m_content->type) ?></dd>
											</dl>
											<dl>
												<dt>Title:</dt> 
												<dd><?= $vd->esc(@$vd->m_content->title) ?></dd>
											</dl>												
										</li>
										<li>
											<?php if (@$vd->pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
											<dl>
												<dt>Selected Industry:</dt> 
												<dd><?= @$vd->beat_1_name ?></dd>
											</dl>
											<?php if (@$vd->beat_2_name): ?>
											<dl>
												<dt>Second Industry:</dt> 
												<dd>
													<?= @$vd->beat_2_name ?>
													<?php if (!$vd->is_already_submitted): ?>
														<span class="label label-info">
					                                	+$<?= $vd->second_cat_item->price ?></span>
					                        <?php endif ?>
				                        </dd>
											</dl>
											<?php endif ?>
											<?php endif ?>
											<dl>
												<dt>Keyword Describing Product or Service:</dt> 
												<dd><?= $vd->esc(@$vd->pw_raw_data->keyword) ?></dd>
											</dl>
										</li>
										<?php if (@$vd->pw_raw_data->order_type == Model_Pitch_Session::ORDER_TYPE_OUTREACH): ?>
										<li>
											<dl>
												<dt>Location of your story:</dt> 
												<dd>
													<?= @$vd->esc(@$vd->pw_raw_data->city) ?>, 
													<?= @$vd->state_name ?>
												</dd>
											</dl>												
											<dl>
												<dt>Selected distribution:</dt> 
												<dd><?= @$vd->distribution_title ?> 
												<?php if (!$vd->is_already_submitted && @$vd->pw_raw_data->distribution == 'national'): ?>
													<span class="label label-info">
														+$<?= $vd->nation_dist_item->price ?></span></dd>
												<?php endif ?>
											</dl>
										</li>
										<?php endif ?>
										<li>
											<dl>
												<dd>
													<p class="pitch-highlight">
														<strong>Pitch Highlight:</strong> 
														<?= nl2br($vd->esc(@$vd->pw_raw_data->pitch_highlight)) ?>
													</p>
												</dd>
											</dl>
											<dl>
												<dt>Additional Comments:</dt> 
												<dd>
													<?= nl2br($vd->esc(@$vd->pw_raw_data->additional_comments)) ?>
												</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<?php if ( ! @$vd->is_already_submitted): ?> 
							<div class="col-lg-4 col-md-5 col-xs-12 mobile-nopad">
								<div class="panel panel-default" id="locked_aside">
									<div class="panel-heading">
										<h3 class="panel-title">Order Summary</h3>
									</div>

									<div class="panel-body aside-order-summary">
										<ul class="aside-order-summary-list nopad">
											<li class="noborder">
												<div class="row">
													<div class="col-lg-8 col-xs-8 order_item">
														<?= $vd->esc($vd->pw_item->name) ?>
														<?php if ($vd->credit_available): ?>
														<div class="text-muted smaller">Consumes 1 credit <br />
															(<?= $vd->held_credits->available() ?> available)</div>	
														<?php endif ?>											
													</div>
													<div class="col-lg-4 col-xs-4 text-right">
														<span class="order-value">$<?= $vd->pw_item->price ?></span>
													</div>
												</div>
											</li>
											<li class="<?= value_if_test( ! @$vd->pw_raw_data->beat_2_id, 'hidden') ?>">
												<div class="row">
													<div class="col-lg-8 col-xs-8 order_item">
														<?= $vd->esc($vd->second_cat_item->name) ?>
													</div>
													<div class="col-lg-4 col-xs-4 text-right nopad-left">
														<span class="order-value order-value-additional">
															+$<?= $vd->second_cat_item->price ?></span>
													</div>
												</div>
											</li>
											<li class="
												<?= value_if_test(@$vd->pw_raw_data->distribution != "national", 'hidden')?>">
												<div class="row">
													<div class="col-lg-8 col-xs-8 order_item">
														<?= $vd->esc($vd->nation_dist_item->name) ?>
													</div>
													<div class="col-lg-4 col-xs-4 text-right nopad-left">
														<span class="order-value order-value-additional">
															+$<?= $vd->nation_dist_item->price ?></span>
													</div>
												</div>
											</li>
											<li id="rush_order_price" class="<?= value_if_test(@$vd->pw_raw_data->delivery != "rush", 'hidden') ?>">
												<div class="row ">
													<div class="col-lg-8 col-xs-8 order_item">
														<?= $vd->esc($vd->rush_item->name) ?>
													</div>
													<div class="col-lg-4 col-xs-4 text-right nopad-left">
														<span class="order-value order-value-additional">
															+$<?= $vd->rush_item->price ?></span>
													</div>
												</div>
											</li>
										</ul>
										<div class="aside-order-summary-footer">
											<div class="row">
												<div class="col-lg-8 col-xs-8 sub-total">
													Subtotal
												</div>
												<div class="col-lg-4 col-xs-4 text-right sub-total">
													<strong>$<span id="subtotal_amount"><?= $vd->sub_total ?></span></strong>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif ?>

					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-lg-8 col-md-7">
									<div class="col-lg-3 col-xs-6">
										<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/3"
												class="col-lg-12 ta-center btn btn-default col-lg-offset-2"><b>&laquo; Back</b></a>
									</div>
								
									<div class="col-lg-3 col-lg-offset-6 col-xs-6">
										<?php if (@$vd->is_already_submitted): ?>
											<button type="submit" class="col-lg-12 btn btn-primary nomar pull-right" 
												name="is_save" value="1">
												Save &raquo;</button>
										<?php else: ?>
											<button type="submit" class="col-lg-12 btn btn-primary nomar pull-right" 
												name="is_continue" value="1">
												Continue &raquo;</button>
										<?php endif ?>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

				

			</div>
		</form>							
	</section>

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
		
			var is_rush_radio = $("#is-rush");
			var is_delviery_radios = $(".is_delivery_radio");
			var handle_delivery_radios = function() {
				var is_rush = is_rush_radio.is(":checked");
				if (is_rush) {
					$("#delivery_date_standard").addClass("hidden");
					$("#delivery_date_rush").removeClass("hidden");
					$("#rush_order_price").removeClass("hidden");
					var total = parseInt(document.getElementById('subtotal_amount').innerHTML);
					var rush_price = parseInt(<?= $vd->rush_item->price ?>)
					total += rush_price;
					document.getElementById('subtotal_amount').innerHTML = total;
				} else {
					$("#delivery_date_standard").removeClass("hidden");
					$("#delivery_date_rush").addClass("hidden");
					$("#rush_order_price").addClass("hidden");
					var total = parseInt(document.getElementById('subtotal_amount').innerHTML);
					var rush_price = parseInt(<?= $vd->rush_item->price ?>)
					total -= rush_price;
					document.getElementById('subtotal_amount').innerHTML = total;
				}
			};

			is_delviery_radios.on("change", handle_delivery_radios);

		});

	</script>
	
</div>
</div>