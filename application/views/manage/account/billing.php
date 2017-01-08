<?= $ci->load->view('manage/account/menu') ?>
<div class="container-fluid relative">

	<div class="order-loader dnone">
		<img src="<?= $vd->assets_base ?>im/loader-circle-medium.gif" />
		<span>Please Wait</span>
	</div>

	<form role="form" action="manage/account/billing/submit" method="post" class="login-form required-form order-form" autocomplete="off">
	<div class="row form-col">
		
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel default">
				<div class="panel-body billing-info">

					<header>
						<div class="row">
							<div class="col-lg-6 page-title">
								<h2>Billing Information</h2>
							</div>
						</div>
					</header>
					
					<input type="hidden" id="order-url-prefix" value="<?= $vd->order_url_prefix ?>" />
					<input type="hidden" name="cc_suffix" value="<?= $vd->cc_suffix ?>" />
					
					<div class="row form-group">
						<div class="col-lg-6 col-sm-6">
							<label for="first_name">First Name</label>
							<input type="text" id="first_name" name="first_name" class="form-control in-text required col-lg-12"
								data-required-name="First Name" value="<?= @$vd->data->first_name ?>">
						</div>
						<div class="col-lg-6 col-sm-6">
							<label for="last_name">Last Name</label>
							<input type="text" id="last_name" name="last_name" class="form-control in-text required col-lg-12"
								data-required-name="Last Name" value="<?= @$vd->data->last_name ?>">
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-6 col-sm-6">
							<label for="company_name">Company Name</label>
							<input type="text" id="company_name" name="company_name" class="form-control in-text required col-lg-12"
								data-required-name="Company" value="<?= @$vd->data->company_name ?>">
						</div>
					
						<div class="col-lg-6 col-sm-6">
							<label for="phone">Phone (Optional)</label>
							<input type="text" id="phone" name="phone"
								class="form-control in-text col-lg-12" value="<?= @$vd->data->phone ?>">
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-12">

							<label for="street_address">Billing Address</label>
							<input type="text" id="street_address" name="street_address" 
								class="form-control in-text col-lg-12 required" data-required-name="Billing Address" 
								value="<?= @$vd->data->street_address ?>">
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-6 col-sm-6">
									
							<label for="locality">Town / City</label>
							<input type="text" id="locality" name="locality" 
								class="form-control in-text col-lg-12 required" data-required-name="Town or City" 
								value="<?= @$vd->data->locality ?>">
						</div>

						<div class="col-lg-6 col-sm-6">
							<label for="region">State / Region</label>
								<input type="text" id="region" name="region" class="form-control in-text col-lg-12"
									 value="<?= @$vd->data->region ?>">
							
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-7 col-sm-7">

							<label for="pass">Country</label>
							<select class="form-control selectpicker col-lg-12 show-menu-arrow marbot-20" id="country-id"
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
								select.on_load_select(function() {
									select.addClass("required");
								});
								
							});
							
							</script>
						</div>

						<div class="col-lg-5 col-sm-5">
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

					<fieldset>
						<div class="select-payment-method">
							<legend class="order-form-header marbot-10">
								<?php if ($vd->data->has_remote_card): ?>
								Update Card Details
								<?php else: ?>
								Enter Card Details
								<?php endif ?>
								<?php if (!@$vd->update_blocked): ?>
								<span id="or-paypal">or</span>
								<span id="paypal-button-image"></span>
								<span id="paypal-button-container"></span>
								<?php endif ?>
							</legend>
						</div>
						<?php if (@$vd->update_blocked): ?>
						<div class="marbot-20">
							As a security measure we require at
							least 24 hours between payment information updates. 
							Please contact us if you 
							would like to update the information sooner. 
						</div>
						<?php else: ?>
						<div class="payment-method-card credit-card-information">
							<div class="row form-group">
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
								<div class="col-lg-4 col-sm-6">
									<label for="cc_expires_month">Expiration Month</label>
									<select class="form-control selectpicker col-lg-12 show-menu-arrow marbot-20" name="cc_expires_month_<?= $vd->cc_suffix ?>"
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
								<div class="col-lg-4 col-sm-6">
									<label for="cc_expires_year">Expiration Year</label>
									<select class="form-control selectpicker col-lg-12 show-menu-arrow marbot-20" name="cc_expires_year_<?= $vd->cc_suffix ?>"
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
								<div class="col-lg-4 col-sm-12">
									<label for="cc_cvc">Card Verification Code</label>
									<input type="text" pattern="^[\d]*$" id="cc_cvc" name="cc_cvc_<?= $vd->cc_suffix ?>" 
										class="form-control in-text col-lg-12" value="<?= @$vd->data->cc_cvc ?>"
										autocomplete="off" />
								</div>
							</div>
							<p class="help-block">
								We will attempt to validate the card details without an authorization fee. 
								In some cases it will be nessecary to make a small charge of $1.00 in order to confirm the card details are correct. 
								The authorization fee will of course be refunded to you (but this can take a few days). 
							</p>
						</div>

						<div class="payment-method-paypal dnone
							 <?= value_if_test(!empty($vd->data->paypal_nonce), 'active') ?>">
							<input type="hidden" id="paypal-nonce-field"
								name="paypal_nonce" value="<?= @$vd->data->paypal_nonce ?>" />
							<input type="hidden" id="paypal-email-field" 
								name="paypal_email" value="<?= @$vd->data->paypal_email ?>" />
							<div class="paypal-frame marbot-20">
								<img class="paypal-logo" src="<?= $vd->assets_base ?>im/paypal-square.png" />
								<div class="paypal-account">
									<span class="paypal-email"><?= @$vd->data->paypal_email ?></span>
									<a id="paypal-button-cancel" href="#" class="paypal-cancel">Cancel</a>
								</div>
							</div>
						</div>
						<?php endif ?>
					</fieldset>

					<?php if ($vd->data->has_remote_card): ?>
					<fieldset>
						<div class="row form-group">
							<div class="col-lg-12">
								<?php if ($vd->data->raw_data->is_virtual_card): ?>
									<legend class="marbot-10">Existing Account Details</legend>
								<?php else: ?>
									<legend class="marbot-10">Existing Card Details</legend>
								<?php endif ?>
								<div class="existing-card-details">
									<img src="<?= $vd->data->raw_data->card_details->imageUrl ?>" alt="" />
									<?php if ($vd->data->raw_data->is_virtual_card): ?>
										<div class="virtual-card-account">
											<?= $vd->data->raw_data->card_details->email ?>
											(<a href="manage/account/billing/remove">remove</a>)
										</div>
										<div class="text-muted">
											<span class="virtual-card-auth">
												authorized account
											</span>
										</div>
									<?php else: ?>
										<div class="masked-cc-number">
											<?= $vd->data->raw_data->card_details->maskedNumber ?>
											(<a href="manage/account/billing/remove">remove</a>)
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
					</fieldset>
					<?php endif	?>

				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default" id="locked_aside">
				<div class="panel-body">
					<section class="ap-block ap-status"></section>
					<div class="alert alert-info billing-info-notice">
						This allows you to set the billing information used
						for all <strong>future transactions</strong>
						including <strong>active renewals</strong>.
					</div>
					<fieldset class="ap-block nomarbot">
						<div class="row">
							<div class="col-lg-12">
								<button type="submit" name="save" value="1" 
									class="col-lg-12 btn btn-primary">Save and Verify</button>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	</form>

</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/billing.js');
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