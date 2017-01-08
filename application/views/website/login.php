<main class="main login-section form-page" role="main">
	<div class="container">
		
		<header class="main-header">
			<h1>Login</h1>
		</header>
		
		<div class="separator"></div>

		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				
				<?php if (!empty($vd->error_text)): ?>
				<div id="login-error" class="alert alert-error marbot-30">
					<?= $vd->error_text ?>
				</div>
				<?php endif ?>
				
				<?= $ci->load->view('website/partials/feedback') ?>
				
				<form class="login-form" method="post" action="<?= gstring('login') ?>" role="form">
					<ul>
						<li class="form-group">
							<label for="email">Email Address</label>
							<input class="form-control" type="email" name="email" id="email" tabindex="1"
								value="<?= $vd->esc(@$this->vd->email) ?>" />
						</li>
						<li class="form-group">
							<div class="row">
								<div class="col-xs-6">
									<label for="pass">Password</label>
								</div>
								<div class="col-xs-6">
									<a href="login/forgot" class="help-form-link">Forgot Password?</a>
								</div>
							</div>
							<input class="form-control" type="password" name="password" id="password" tabindex="2" />
						</li>
						<li>
							<button type="submit" class="signup-btn" tabindex="3">Login</button>
						</li>
					</ul>
					
					<footer class="form-footer">
						<menu class="form-footer-menu">
							<li><i class="fa fa-user"></i> Need an account? <a href="register">Sign up today!</a></li>
							<li><i class="fa fa-envelope"></i> Didn't get your confirmation email? <a href="login/resend">Resend confirmation email.</a></li>
						</menu>
					</footer>
				</form>
			</div>
			<div class="col-sm-3"></div>			
		</div>
		
		<script>
		
		$(function() {
			
			var email = $("#email");
			if (email.val())
				  $("#password").focus();
			else email.focus();
	
		});
		
		</script>
		
	</div>
</main>