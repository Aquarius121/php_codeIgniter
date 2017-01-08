<?= $ci->load->view('manage/partials/cart-menu') ?>

<div class="container-fluid relative">

	<script>
		
	$(function() {

		// move order errors into the order form
		var feedback = $("#feedback").children().detach();
		$("#order-feedback").prepend(feedback);

	});

	</script>

	<div class="order-loader dnone">
		<img src="<?= $vd->assets_base ?>im/loader-circle-medium.gif" />
		<span>Please Wait</span>
	</div>

	<form role="form" action="manage/order/submit" method="post" class="login-form required-form order-form" autocomplete="off">
	<div class="row">
		<div class="col-lg-8 col-md-6 order-form-col-1" id="order-feedback">
			<div class="panel panel-default">
				<div class="panel-body">
			
					<div class="marbot-15"></div>
					<div class="relative">

						<input type="hidden" id="order-url-prefix" value="<?= $vd->order_url_prefix ?>" />
						<input type="hidden" name="cc_suffix" value="<?= $vd->cc_suffix ?>" />
						<div class="row form-group">
							<div class="col-lg-12">
								<?php if (isset($vd->message)): ?>
								<?= $vd->message ?>
								<?php endif ?>
								
								<?php if (Auth::user()->has_clients()): ?>
									<div  class="form-group">
										<label for="client_name">Client Name (Optional)</label>
										<input type="text" id="client_name" name="client_name"
											class="form-control in-text nomarbot col-lg-12"
											value="<?= @$vd->data->client_name ?>">
									</div>
									<hr class="marbot-30" />
								<?php endif ?>

								<div class="row form-group">
									<div class="col-lg-6">
										<label for="first_name">First Name</label>
										<input type="text" id="first_name" name="first_name" class="form-control in-text required col-lg-12"
											data-required-name="First Name" value="<?= @$vd->data->first_name ?>">
									</div>
									<div class="col-lg-6">
										<label for="last_name">Last Name</label>
										<input type="text" id="last_name" name="last_name" class="form-control in-text required col-lg-12"
											data-required-name="Last Name" value="<?= @$vd->data->last_name ?>">
									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-6">
										<label for="company_name">Company Name</label>
										<input type="text" id="company_name" name="company_name" class="form-control in-text required col-lg-12"
											data-required-name="Company" value="<?= @$vd->data->company_name ?>">
									</div>
									<div  class="col-lg-6">
										<label for="phone">Phone</label>
										<input type="text" id="phone" name="phone"
											class="form-control in-text col-lg-12 marbot-5 <?= 
												value_if_test(!Auth::is_admin_online(), 'required') ?>"
											value="<?= @$vd->data->phone ?>"
											data-required-name="Phone" />
									</div>
								</div>
								<div  class="row form-group">
									<div  class="col-lg-12">
										<label for="street_address">Billing Address</label>
										<input type="text" id="street_address" name="street_address" 
											class="form-control in-text col-lg-12 required" data-required-name="Billing Address" 
											value="<?= @$vd->data->street_address ?>">
									</div>
								</div>
								<!--
								<div  class="form-group">
									<label for="extended_address">Address Line 2 (Optional)</label>
									<input type="text" id="extended_address" name="extended_address" class="form-control in-text col-lg-12"
										 value="<?= @$vd->data->extended_address ?>">
								</div>
								-->
								<div class="row form-group">
									<div  class="col-lg-6">
										<label for="locality">Town / City</label>
										<input type="text" id="locality" name="locality" 
											class="form-control in-text col-lg-12 required" data-required-name="Town or City" 
											value="<?= @$vd->data->locality ?>">
									</div>
									<div  class="col-lg-6">
										<label for="region">State / Region</label>
										<input type="text" id="region" name="region" class="form-control in-text col-lg-12"
											 value="<?= @$vd->data->region ?>">
									</div>
								</div>
								<div class="row">
									<div  class="form-group col-lg-7">
										<label for="pass">Country</label>
										<select class="form-control selectpicker col-lg-12 marbot show-menu-arrow" id="country-id"
											data-required-name="Country" name="country_id" data-container="body">
											<option value="">Select Country</option>
											<?php foreach ($vd->common_countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->data->country_id == $country->id), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
											<?php foreach ($vd->countries as $country): ?>
											<option value="<?= $country->id ?>"
												<?= value_if_test((@$vd->data->country_id == $country->id && 
													!$country->is_common), 'selected') ?>>
												<?= $vd->esc($country->name) ?>
											</option>
											<?php endforeach ?>
										</select>
										<script>
										
										$(function() {
											
											var select = $("#country-id");
											select.on_load_select();
											
											$(window).on("load", function() {
												select.addClass("required");
											});
											
										});
										
										</script>
									</div>
									<div  class="form-group col-lg-5">
										<label for="zip">Postal Code</label>
										<input type="text" id="zip" name="zip"
											data-required-name="Postal Code"
											class="form-control in-text col-lg-12 required-callback nomarbot"
											data-required-callback="postal-code-us"
											value="<?= @$vd->data->zip ?>">
										<script>

										$(function() {

											var zip = $("#zip");
											var country = $("#country-id");
											
											required_js.add_callback("postal-code-us", function(value) {
												var response = { valid: true, text: "is required" };
												// required only for US customers at the moment
												if (country.val() == <?= json_encode(Model_Country::ID_UNITED_STATES) ?>
													&& !zip.val()) response.valid = false;
												return response;
											});
											
										});
										
										</script>
									</div>
								</div>

								<hr />
								
								<div id="payment-section">
									<?php if ($vd->data->has_remote_card): ?>
									<div class="payment-radios">
										<div class="row">
											<div class="col-lg-12">
												<label class="radio-container">
													<input type="radio" id="use_remote_card" 
														name="use_remote_card" value="1" <?= 
														value_if_test($vd->data->use_remote_card, 'checked') ?> />
													<span class="radio"></span>
													Use existing 
													<?php if ($vd->data->raw_data->is_virtual_card): ?>
													account
													<?php else: ?>
													card
													<?php endif ?>
													details
												</label>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">									
												<div class="existing-card-details">
													<img src="<?= $vd->data->raw_data->card_details->imageUrl ?>" alt="" />
													<?php if ($vd->data->raw_data->is_virtual_card): ?>
													<div class="virtual-card-account">
														<?= $vd->data->raw_data->card_details->email ?>
													</div>
													<div class="text-muted">
														<span class="virtual-card-auth">
															authorized account
														</span>
													</div>
													<?php else: ?>
													<div class="masked-cc-number">
														<?= $vd->data->raw_data->card_details->maskedNumber ?>
													</div>
													<div class="text-muted">
														<span class="card-type"><?= $vd->data->raw_data->card_details->cardType ?></span>
														<span>-</span> 
														<span class="card-expires">
															<?= $vd->data->raw_data->card_details->expirationDate ?>
														</span>				
													</div>
													<?php endif ?>
												</div>
											</div>
										</div>
										<div class="row form-group">
											<div class="col-lg-12">
												<label class="radio-container">
													<input type="radio" name="use_remote_card" value="0" <?= 
														value_if_test(!$vd->data->use_remote_card, 'checked') ?> />
													<span class="radio"></span>
													Use a different card or account
												</label>
											</div>
										</div>
									</div>
									<?php endif ?>
									<?php if (@$vd->update_blocked): ?>
									<div class="marbot-30 dnone payment-methods alert alert-warning">
										As a security measure we require at
										least 24 hours between payment information updates. Please contact us if you 
										would like to update the information sooner. 
									</div>
									<?php else: ?>
									<div class="payment-methods <?= value_if_test($vd->data->has_remote_card, 'dnone') ?>">
										<div class="select-payment-method">
											<div class="row">
												<div class="col-lg-12">
													<legend>
														Credit Card 
														<span id="or-paypal">or</span>
														<span id="paypal-button-image"></span>
														<span id="paypal-button-container"></span>
													</legend>
												</div>
											</div>
											<div class="payment-method-card">
												<div class="row form-group credit-card-information">
													<div class="col-lg-12">
														<input type="hidden" name="cc_nonce" id="cc_nonce" value="" />
														<label for="cc_number">Credit Card Number</label>
														<input type="text" pattern="^[\d \-]*$" id="cc_number" 
															name="cc_number_<?= $vd->cc_suffix ?>" class="form-control in-text col-lg-12" 
															value="<?= @$vd->data->cc_number ?>"
															autocomplete="off" />
													</div>
												</div>
												<div class="row form-group">
													<div class="col-lg-6">
														<label for="cc_expires_month">Expiration Date</label>
														<select class="form-control selectpicker col-lg-12 show-menu-arrow" name="cc_expires_month_<?= $vd->cc_suffix ?>"
															id="cc_expires_month" data-container="body">
															<option value="" selected="selected">Please Select Month</option>
															<option value="01" <?= value_if_test(@$vd->data->cc_expires_month == '01', 'selected') ?>>01 - January</option>
															<option value="02" <?= value_if_test(@$vd->data->cc_expires_month == '02', 'selected') ?>>02 - February</option>
															<option value="03" <?= value_if_test(@$vd->data->cc_expires_month == '03', 'selected') ?>>03 - March</option>
															<option value="04" <?= value_if_test(@$vd->data->cc_expires_month == '04', 'selected') ?>>04 - April</option>
															<option value="05" <?= value_if_test(@$vd->data->cc_expires_month == '05', 'selected') ?>>05 - May</option>
															<option value="06" <?= value_if_test(@$vd->data->cc_expires_month == '06', 'selected') ?>>06 - June</option>
															<option value="07" <?= value_if_test(@$vd->data->cc_expires_month == '07', 'selected') ?>>07 - July</option>
															<option value="08" <?= value_if_test(@$vd->data->cc_expires_month == '08', 'selected') ?>>08 - August</option>
															<option value="09" <?= value_if_test(@$vd->data->cc_expires_month == '09', 'selected') ?>>09 - September</option>
															<option value="10" <?= value_if_test(@$vd->data->cc_expires_month == '10', 'selected') ?>>10 - October</option>
															<option value="11" <?= value_if_test(@$vd->data->cc_expires_month == '11', 'selected') ?>>11 - November</option>
															<option value="12" <?= value_if_test(@$vd->data->cc_expires_month == '12', 'selected') ?>>12 - December</option>
														</select>
													</div>
													<div class="col-lg-6">
														<label for="cc_expires_year">&nbsp;</label>
														<select class="selectpicker form-control col-lg-12 show-menu-arrow" name="cc_expires_year_<?= $vd->cc_suffix ?>"
															id="cc_expires_year" data-container="body">
															<option value="" selected="selected">Please Select Year</option>
															<?php for ($i = 0; $i < 10; $i++): ?>
															<?php $date = Date::years($i)->format('Y'); ?>
															<option value="<?= $date ?>" <?= 
																value_if_test(@$vd->data->cc_expires_year == $date, 'selected') 
																?>><?= $date ?></option>
															<?php endfor ?>
														</select>
													</div>
												</div>
												<div class="row form-group">
													<div class="col-lg-12">
														<label for="cc_cvc">Card Verification Code</label>
														<input type="text" pattern="^[\d]*$" id="cc_cvc" name="cc_cvc_<?= $vd->cc_suffix ?>" 
															class="form-control in-text col-lg-12" value="<?= @$vd->data->cc_cvc ?>"
															autocomplete="off" />
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="payment-method-paypal dnone
										 <?= value_if_test(!empty($vd->data->paypal_nonce), 'active') ?>">
										<input type="hidden" id="paypal-nonce-field"
											name="paypal_nonce" value="<?= @$vd->data->paypal_nonce ?>" />
										<input type="hidden" id="paypal-email-field" 
											name="paypal_email" value="<?= @$vd->data->paypal_email ?>" />
										<div class="paypal-frame marbot-30">
											<img class="paypal-logo" src="<?= $vd->assets_base ?>im/paypal-square.png" />
											<div class="paypal-account">
												<span class="paypal-email"><?= @$vd->data->paypal_email ?></span>
												<a id="paypal-button-cancel" href="#" class="paypal-cancel">Cancel</a>
											</div>
										</div>
									</div>
									<?php endif ?>
								</div>
							</div>
						</div>

						<hr />
							
						<div class="row form-group">
							<div class="col-lg-12">
								<div class="order-terms status-muted smaller">
									By submitting the order form you are confirming that you have read and that you agree to our 
									<a target="_blank" href="<?= $ci->website_url('terms-of-service') ?>">terms of service</a>. 
									<?php $items = $vd->cart->item_models_set(); ?>
									<?php foreach ($items as $item): ?>
										<?php if (($ird = $item->raw_data()) && !empty($ird->terms_html)): ?>
											<?= $ird->terms_html ?>
										<?php endif ?>
									<?php endforeach ?>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-12">
								<div class="submit-order-buttons">
									<?php if ($vd->cart->is_clear()): ?>
									<button class="btn btn-success submit-order-button" type="submit" disabled>Submit Order</button>
									<?php else: ?>
									<button class="btn btn-success submit-order-button" type="submit">Submit Order</button>
									<?php endif ?>
								</div>
							</div>
						</div>
					
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-6 aside-form-page order-form-col-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Order Summary</h3>
				</div>
				<div class="panel-body">
					<div class="aside_cart">
						<div class="tips your-cart manage-25-cart">
							<div class="cart-data" id="cart-data">	
								<?= $ci->load->view('shared/partials/cart') ?>		
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="cancel-at-any-time" class="<?= value_if_test(@$vd->has_renewal_distance, 'show') ?>">
				* Cancel at any time
			</div>

			<div class="panel panel-default">
				<div class="pad-30v pad-20h">
					<?= $ci->load->view('shared/partials/secure-seals.php') ?>
				</div>
			</div>

		</div>
	</div>
	</form>
</div>

<script>

$(function() {

	var use_remote_card = $("#use_remote_card");	
	var payment_methods = $(".payment-methods");
	var payment_radios = $(".payment-radios input");
	var existing_payment_method = $(".existing-card-details");
	var card_payment_method = $(".payment-method-card");
	var paypal_payment_method = $(".payment-method-paypal");

	if (use_remote_card.size()) {

		var switch_card_mode = function() {
			if (use_remote_card.is(":checked")) {
				card_payment_method.find("input").prop("disabled", true);
				paypal_payment_method.find("input").prop("disabled", true);
				existing_payment_method.slideDown();
				payment_methods.slideUp();
			} else {
				card_payment_method.find("input").prop("disabled", false);
				paypal_payment_method.find("input").prop("disabled", false);
				payment_methods.slideDown();
				existing_payment_method.slideUp();
			}
		};
		
		payment_radios.on("change", switch_card_mode);
		switch_card_mode();

	}	

});	
	
</script>

<?= $ci->load->view('shared/partials/order-js') ?>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/billing.js');
	$loader->add('lib/jquery.create.js');
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	$ci->add_eob($loader->render($render_basic));

?>

