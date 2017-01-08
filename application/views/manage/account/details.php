<?= $ci->load->view('manage/account/menu') ?>

<form action="<?= $ci->uri->uri_string ?>" method="post" class="required-form">
	
	<div class="container-fluid">
		<header>
			<div class="row">
				<div class="col-lg-6">
					<h2>Account Details</h2>
				</div>
			</div>
		</header>
		
		<div class="row">
			<div class="col-lg-12 form-col-1">
				<div class="panel panel-default">
					<div class="panel-body">

						<fieldset class="basic-information">
							<legend>User Information</legend>
							<div class="row form-group">
								<div class="col-lg-10 form-inline">
									<div class="required-callback"
										data-required-name="Name"
										data-required-callback="name-combined">
										<input type="text" placeholder="First Name" class="form-control"
											name="first_name" id="first-name"
											value="<?= $vd->esc($vd->user->first_name) ?>" />
										<input type="text" placeholder="Last Name" class="form-control" 
											name="last_name" id="last-name"
											value="<?= $vd->esc($vd->user->last_name) ?>" />
									</div>
								</div>
								<script>

								$(function() {

									var first_name = $("#first-name");
									var last_name = $("#last-name");

									required_js.add_callback("name-combined", function(value) {
										var response = { valid: false, text: "must have a value" };
										response.valid = first_name.val() && last_name.val();
										return response;
									});

								});

								</script>
							</div>
							<div class="row form-group">
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
									<input type="email" placeholder="Email Address" class="form-control required" 
										name="email" value="<?= $vd->esc($vd->user->email) ?>" 
										data-required-name="Email Address" />
								</div>
							</div>
						</fieldset>

						<fieldset class="change-password">
							<legend>Change Password</legend>
							<div class="row">
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
									<div class="required-callback" data-required-name="Password"
										data-required-callback="password-strength password-match"></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
									<input type="password" placeholder="New Password" id="new-password" 
										name="new_password" class="form-control marbot-15" />
									<div class="progress dnone" id="password-strength">
										<div class="progress-bar" role="progressbar" id="password-strength-bar"
											aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
									<input type="password" placeholder="Confirm New Password" 
										id="new-password-confirm" name="new_password_confirm" 
										class="form-control" />
								</div>
								<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
									<div class="dnone" id="secure-hint">
										<strong>Be Secure!</strong> We recommend a password at least 8 characters in length. 
										You should include capital letters, numbers and symbols for added security. 
									</div>

									<script>
									
									$(function() {
										
										var hidden = true;
										var pw_box = $("#new-password");
										var pw_confirm_box = $("#new-password-confirm");
										var pw_strength = $("#password-strength");
										var pw_strength_bar = $("#password-strength-bar");
										var secure_hint = $("#secure-hint");
										var pw_is_valid = true;

										var pw_options = {
											minimumChars: 8,
											strengthScaleFactor: 0.75,
											minValidComplexity: 25,
										};

										pw_box.on("focus", function() {
											if (!hidden) return;
											secure_hint.fadeIn();
											pw_strength.fadeIn();
											hidden = false;
										});

										pw_box.complexify(pw_options, function(valid, complexity, password) {

											pw_is_valid = valid || !password.length;
											pw_strength_bar.removeClass("progress-bar-success");
											pw_strength_bar.removeClass("progress-bar-warning");
											pw_strength_bar.removeClass("progress-bar-danger");
											pw_strength_bar.width(complexity + "%");
											pw_strength_bar.attr("aria-valuenow", complexity);

											if (valid && complexity > 50) {
												pw_strength_bar.addClass("progress-bar-success");
												return;
											}

											if (valid && complexity > 25) {
												pw_strength_bar.addClass("progress-bar-warning");
												return;
											}

											pw_strength_bar.addClass("progress-bar-danger");
											return;

										});

										required_js.add_callback("password-strength", function(value) {
											var response = { valid: false, text: "must have a secure value" };
											response.valid = pw_is_valid;
											return response;
										});

										required_js.add_callback("password-match", function(value) {
											var response = { valid: false, text: "does not match" };
											response.valid = pw_box.val() == pw_confirm_box.val();
											return response;
										});
										
									});
									
									</script>
								</div>
							</div>
						</fieldset>

						<fieldset class="form-section change-password">
							<legend>Notification Preferences</legend>
							<div class="text-muted marbot-15" style="max-width:70%">Select all of the emails you wish to receive. 
								Please add <code>notification@newswire.com</code> to your address book
								to ensure you receive all notifications from us.</div>		
							<?php foreach (Model_User_Mail_Blocks::collection() as $block): ?>
							<div class="row form-group">
								<div class="col-lg-11">
									<label class="checkbox-container inline">
										<input type="checkbox" value="1" name="<?= $vd->esc($block) ?>"
											<?= value_if_test(!$vd->mail_blocks || !$vd->mail_blocks->has($block), 'checked') ?>>
										<span class="checkbox"></span>
										<?= $vd->esc(Model_User_Mail_Blocks::describe($block)) ?>
									</label>
								</div>
							</div>
							<?php endforeach ?>
						</fieldset>

						<?php if (!$this->session->get('assume_account_owner') && !Auth::is_admin_online()): ?>
						<fieldset class="confirm-password">
							<legend>Current Password</legend>
							<div class="row form-group">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">								
									<input type="password" class="form-control required in-text col-lg-12" 
										data-required-name="Current Password"
										placeholder="Current Password" name="password" />
									<p class="help-block">
										Enter your current password to confirm the change.
										<br />This is to maintain account security.
									</p>
								</div>
							</div>
						</fieldset>	
						<?php endif ?>
						<div class="row form-group">
							<div class="col-lg-12">
							<button type="submit" name="save" value="1"
								class="col-lg-2 btn btn-primary">Save</button>
						</div>	
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
		$loader->add('lib/complexify/jquery.complexify.min.js');
		$render_basic = $ci->is_development();
		$ci->add_eob($loader->render($render_basic));

	?>
	
</form>
