<main class="main signup-section form-page" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Sign up below, it’s easy.</h1>
				</header>
			</div>
		</div>
         
		<div class="separator"></div>
         
		<div class="row">
			<div class="col-sm-1"></div>
			<div class="col-sm-6">
				
				<?php if (!empty($vd->error_text)): ?>
				<div id="register-error" class="alert alert-error">
					<?= $vd->error_text ?>
				</div>
				<?php endif ?>
				
				<?php if (@$vd->success): ?>
				<div id="login-error" class="alert alert-success marbot-30">
					<strong>Success!</strong> Check the email account for verification.
				</div>
				<?php endif ?>
				
				<?= $ci->load->view('website/partials/feedback') ?>
				
				<form class="register-form required-form" method="post" action="register" role="form">
					<ul>
						<li class="form-group">
							<label for="email">Email Address</label>
							<input class="form-control required" type="email" name="email" 
								data-required-name="Email" id="email" value="<?= $this->input->post('email') ?>" />
						</li>
						<li class="form-group row">
							<div class="col-sm-6">
								<label for="first_name">First Name</label>
								<input class="form-control marbot-15 required" type="text" name="first_name" 
									data-required-name="First Name" id="first_name"
									value="<?= $this->input->post('first_name') ?>" />
							</div>
							<div class="col-sm-6">
								<label for="last_name">Last Name</label>
								<input class="form-control marbot-15 required" type="text" name="last_name" 
									data-required-name="Last Name" id="last_name"
									value="<?= $this->input->post('last_name') ?>" />
							</div>
						</li>
						<li class="form-group">
							<label for="company_name">Company Name</label>
							<input class="form-control" type="text" name="company_name" 
								id="company_name" value="<?= $this->input->post('company_name') ?>" />
						</li>
						<li class="form-group">
							<label for="real_password">Password</label>
							<input type="hidden" id="password" name="password" value="" />
							<input class="form-control required required-callback" type="password" 
								name="real_password" id="real_password" value="<?= $this->input->post('real_password') ?>" 
								data-required-name="Password" data-required-callback="password-length
									password-complex-number" />
							<script>
					
							$(function() {
								
								required_js.add_callback("password-length", function(value) {
									var response = { valid: false, text: "must have at least 8 characters" };
									if (value.length >= 8) response.valid = true;
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
						<li>
							<button name="register" type="submit" class="signup-btn">Sign Up</button>
						</li>
					</ul>
				</form>
			</div>

			<?php 

				$loader = new Assets\JS_Loader(
					$ci->conf('assets_base'), 
					$ci->conf('assets_base_dir'));
				$loader->add('lib/jquery.create.js');
				$loader->add('js/required.js');
				$render_basic = $ci->is_development();
				$ci->add_eob($loader->render($render_basic));

			?>
			

			<script>
			
			$(function() {
				
				$("form.register-form input").each(function() {
					var _this = $(this);
					if (!_this.val()) {
						_this.focus();
						return false;
					}					
				});
				
				// remove fake password to work 
				// better with auto fill 
				$("#password").remove();
				
			});
			
			</script>
			
			<aside class="col-sm-5 aside-form-page">
				<h3>Your free account includes:</h3>				
				<ul class="account-properties-list">
					<li><i class="fa fa-check"></i> Amplify Your Message Across the Web</li>
					<li><i class="fa fa-check"></i> Fast Editorial Approvals</li>
					<li><i class="fa fa-check"></i> Easy Press Management</li>
					<li><i class="fa fa-check"></i> Friendly Customer Support</li>
				</ul>				
				<a href="login" class="sign-in-link"><i class="fa fa-user"></i> 
					Have an account? <strong>Sign in &raquo;</strong></a>
			</aside>

		</div>
	</div>
</main>