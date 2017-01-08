<section class="sign-up-panel register-footer">
	<div class="container">
		<div class="row">
			<div class="signup-today">
				<h2>Get started today. <strong>Sign up for free!</strong></h2>
				<form class="form-inline" role="form" method="post" action="<?= 
					$ci->ssl_url($ci->website_url('register/quick')) ?>">
					<div class="form-group">
						<label class="sr-only">Email Address</label>
						<input type="email" class="form-control" name="email" placeholder="Email Address" />
					</div>
					<div class="form-group">
						<button type="submit" class="signup-btn">Sign up</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>