<main class="main checkout-section form-page" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Place Your Order</h1>
				</header>
			</div>
		</div>
		<?php if (!empty($vd->inject_before_rule)): ?>
			<?php foreach ($vd->inject_before_rule as $inject): ?>
				<?= $ci->load->view($inject); ?>
			<?php endforeach ?>
		<?php endif ?>
		<div class="separator"></div>
		<?php if (!empty($vd->inject_after_rule)): ?>
			<?php foreach ($vd->inject_after_rule as $inject): ?>
				<?= $ci->load->view($inject); ?>
			<?php endforeach ?>
		<?php endif ?>
		<div class="relative">
			<div class="order-loader dnone">
				<img src="<?= $vd->assets_base ?>im/loader-circle-medium.gif" />
				<span>Please Wait</span>
			</div>
			<form role="form" action="order/submit" method="post" class="login-form required-form order-form" autocomplete="off">
				<input type="hidden" id="order-url-prefix" value="<?= $vd->order_url_prefix ?>" />
				<input type="hidden" name="cc_suffix" value="<?= $vd->cc_suffix ?>" />
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-6">
						<?= $ci->load->view('website/partials/feedback') ?>
						<ul>
							<div class="row">
								<li class="form-group col-md-6">
									<label for="first_name">First Name</label>
									<input type="text" id="first_name" name="first_name" class="form-control required"
										data-required-name="First Name" value="<?= @$vd->data->first_name ?>">
								</li>
								<li class="form-group col-md-6">
									<label for="last_name">Last Name</label>
									<input type="text" id="last_name" name="last_name" class="form-control required"
										data-required-name="Last Name" value="<?= @$vd->data->last_name ?>">
								</li>
							</div>
							<div class="row">
								<li class="form-group col-md-6">
									<label for="company_name">Company Name</label>
									<input type="text" id="company_name" name="company_name" class="form-control required"
										data-required-name="Company" value="<?= @$vd->data->company_name ?>">
								</li>
								<li class="form-group col-md-6">
									<label for="phone">Phone</label>
									<input type="text" id="phone" name="phone"
										class="form-control required" 
										data-required-name="Phone" 
										value="<?= @$vd->data->phone ?>">
								</li>
							</div>
							<li class="form-group">
								<label for="street_address">Billing Address</label>
								<input type="text" id="street_address" name="street_address" 
									class="form-control required" data-required-name="Billing Address" 
									value="<?= @$vd->data->street_address ?>">
							</li>
							<!-- 
							<li class="form-group">
								<label for="extended_address">Address Line 2 (Optional)</label>
								<input type="text" id="extended_address" name="extended_address" class="form-control"
									 value="<?= @$vd->data->extended_address ?>">
							</li>
							-->
							<div class="row">
								<li class="form-group col-md-6">
									<label for="locality">Town / City</label>
									<input type="text" id="locality" name="locality" 
										class="form-control required" data-required-name="Town or City" 
										value="<?= @$vd->data->locality ?>">
								</li>
								<li class="form-group col-md-6">
									<label for="region">State / Region</label>
									<input type="text" id="region" name="region" class="form-control"
										 value="<?= @$vd->data->region ?>">
								</li>
							</div>
							<div class="row">
								<li class="form-group col-md-7">
									<label for="pass">Country</label>
									<select class="form-control required" id="select-country"
										data-required-name="Country" name="country_id">
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
								</li>
								<li class="form-group col-md-5">
									<label for="zip">Postal Code</label>
									<input type="text" id="zip" name="zip"
										data-required-name="Postal Code"
										class="form-control required-callback"
										data-required-callback="postal-code-us"
										value="<?= @$vd->data->zip ?>">
									<script>

									$(function() {

										var zip = $("#zip");
										var country = $("#select-country");
										
										required_js.add_callback("postal-code-us", function(value) {
											var response = { valid: true, text: "is required" };
											// required only for US customers at the moment
											if (country.val() == <?= json_encode(Model_Country::ID_UNITED_STATES) ?>
												&& !zip.val()) response.valid = false;
											return response;
										});
										
									});
									
									</script>
								</li>
							</div>
							<h1 class="order-form-header account-details">
								Account Details
							</h1>
							<li class="form-group">
								<label for="email">Email Address</label>
								<input type="email" id="email" name="email" class="form-control required"
									data-required-name="Email" value="<?= @$vd->data->email ?>">
							</li>
							<li class="form-group">
								<label for="password">Password</label>
								<input type="password" id="password" name="password" data-required-name="Password"
									data-required-callback="password-length password-complex-number" 
									class="form-control required required-callback"
									value="<?= @$vd->data->password ?>" />
								<script>
			
								$(function() {
									
									required_js.add_callback("password-length", function(value) {
										var response = { valid: false, text: "must be at least 8 characters" };
										response.valid = value.length >= 8;
										return response;
									});
									
									required_js.add_callback("password-complex-number", function(value) {
										var response = { valid: false, text: "must have at least 1 number" };
										if (/[0-9]/.test(value)) response.valid = true;
										return response;
									});
									
								});
								
								</script>
							</li>
							<div class="select-payment-method">
								<h1 class="order-form-header">
									Credit Card
									<span id="or-paypal">or</span>
									<span id="paypal-button-image"></span>
									<span id="paypal-button-container"></span>
								</h1>
							</div>
							<div class="payment-method-card
								<?= value_if_test(!empty($vd->data->cc_number) && 
									!empty($vd->data->cc_nonce), 'active') ?>">
								<input type="hidden" name="cc_nonce" id="cc_nonce" value="" />
								<li class="form-group">
									<label for="cc_number">Credit Card Number</label>
									<input type="text" pattern="^[\d \-]*$" id="cc_number" data-required-name="Card Number"
										name="cc_number_<?= $vd->cc_suffix ?>" class="form-control required" 
										value="<?= @$vd->data->cc_number ?>" autocomplete="off" />
								</li>
								<div class="row">
									<li class="form-group col-md-6">
										<label for="cc_expires_month">Expiration Date</label>
										<select class="form-control" name="cc_expires_month_<?= $vd->cc_suffix ?>" id="cc_expires_month">
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
									</li>
									<li class="form-group col-md-6">
										<label for="cc_expires_year">&nbsp;</label>
										<select class="form-control" name="cc_expires_year_<?= $vd->cc_suffix ?>" id="cc_expires_year">
											<option value="" selected="selected">Please Select Year</option>
											<?php for ($i = 0; $i < 10; $i++): ?>
											<?php $date = Date::years($i)->format('Y'); ?>
											<option value="<?= $date ?>" <?= 
												value_if_test(@$vd->data->cc_expires_year == $date, 'selected') 
												?>><?= $date ?></option>
											<?php endfor ?>
										</select>
									</li>
								</div>
								<li class="form-group">
									<label for="cc_cvc">Card Verification Code</label>
									<input type="text" pattern="^[\d]*$" id="cc_cvc" name="cc_cvc_<?= $vd->cc_suffix ?>"
										class="form-control required" data-required-name="Card Verification Code"
										autocomplete="off" value="<?= @$vd->data->cc_cvc ?>">
								</li>
							</div>
							<div class="payment-method-paypal dnone marbot
								 <?= value_if_test(!empty($vd->data->paypal_nonce), 'active') ?>">
								<input type="hidden" id="paypal-nonce-field"
									name="paypal_nonce" value="<?= @$vd->data->paypal_nonce ?>" />
								<input type="hidden" id="paypal-email-field" 
									name="paypal_email" value="<?= @$vd->data->paypal_email ?>" />
								<div class="paypal-frame">
									<img class="paypal-logo" src="<?= $vd->assets_base ?>im/paypal-square.png" />
									<div class="paypal-account">
										<span class="paypal-amount">
											<span class="paypal-amount-value"><?= 
											$vd->cart->format($vd->cart->total_with_discount()) ?></span> USD
										</span>
										<span class="paypal-email"><?= @$vd->data->paypal_email ?></span>
										<a id="paypal-button-cancel" href="#" class="paypal-cancel">Cancel</a>
									</div>
								</div>
							</div>
							<li class="order-terms status-muted smaller">
								By submitting the order form you are confirming that you have read and that you agree to our 
								<a target="_blank" href="<?= $ci->website_url('terms-of-service') ?>">terms of service</a>. 
								<?php $items = $vd->cart->item_models_set(); ?>
								<?php foreach ($items as $item): ?>
									<?php if (($ird = $item->raw_data()) && !empty($ird->terms_html)): ?>
										<?= $ird->terms_html ?>
									<?php endif ?>
								<?php endforeach ?>
							</li>
							<li class="form-group submit-order-buttons">
								<?php if ($vd->cart->is_clear()): ?>
								<button class="signup-btn submit-order-button" type="submit" disabled>Submit Order</button>
								<?php else: ?>
								<button class="signup-btn submit-order-button" type="submit">Submit Order</button>
								<?php endif ?>
							</li>
						</ul>
					</div>
					<aside class="col-md-4 aside-form-page">
						<div class="aside_cart">
							<div class="your-cart">
								<div class="contentbox cart-data" id="cart-data">
									<div class="row">
										<?= $ci->load->view('shared/partials/cart') ?>
									</div>
								</div>
							</div>
						</div>
						<div id="cancel-at-any-time" class="<?= value_if_test(@$vd->has_renewal_distance, 'show') ?>">
							* Cancel at any time
						</div>
						<div class="aside_tips marbot-20 have-questions">
							<div class="tips">
								<div class="contentbox">
									<h3>Have questions?</h3>
									<p>Feel free to contact us if you have any questions or concerns.</p>
									<ul>
										<li><i class="fa fa-phone"></i>&nbsp; <a href="tel:8007137278">Call <em class="our-phone-number">800-713-7278</em></a></li>
										<li><i class="fa fa-comments"></i>&nbsp; <a href="#" id="chat-with-us-live">Chat with us live</a></li>
										<li><i class="fa fa-ticket"></i>&nbsp; <a href="helpdesk">Open a ticket</a></li>
										<script>
											
										$(function() {
											$("#chat-with-us-live").on("click", function() {
												$(".clickdesk .cd-bar").trigger("click");
											});
										});

										</script>
									</ul>
								</div>
							</div>
						</div>
						<div class="aside_tips marbot-20 already-have-account">
							<div class="tips">
								<div class="contentbox">
									<h3>Already have an account?</h3>
									<p>You should <a href="manage/order">login to your account</a> to 
										continue the order process.</p>
								</div>
							</div>
						</div>
						<?= $ci->load->view('shared/partials/secure-seals.php') ?>
					</aside>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</div>	
</main>

<?= $ci->load->view('shared/partials/order-js') ?>
<?= $ci->load->view('partials/track-hotjar') ?>

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