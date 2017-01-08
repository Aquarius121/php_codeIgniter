<main class="main login-section form-page" role="main">
	<div class="container">
		
		<header class="main-header">
			<h1>Reset Password</h1>
		</header>
		
		<div class="separator"></div>

		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				
				<?php if (@$vd->success): ?>
				<div id="login-error" class="alert alert-success marbot-30">
					<strong>Success!</strong> Check the email account.
				</div>
				<?php endif ?>
				
				<?= $ci->load->view('website/partials/feedback') ?>
				
				<form class="login-form" method="post" action="login/forgot" role="form">
					<ul>
						<li class="form-group">
							<label for="email">Email Address</label>
							<input class="form-control" type="email" name="email" id="email" tabindex="1" />
							<script> $(function() { $("#email").focus(); }); </script>
						</li>
						<li>
							<button type="submit" class="signup-btn" tabindex="3">Submit</button>
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
		
	</div>
</main>