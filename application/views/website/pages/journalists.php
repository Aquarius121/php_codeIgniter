<?php $ci->title = 'Get News You Care About, Straight to Your Inbox'; ?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>News Updates Right to Your Inbox</h1>
					<p>
						<span>The simple and effective way for Journalists to find relevant news stories.</span>
					</p>

				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<section class="features features-smaller">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 marbot-20">
				<div class="row">
					<div class="col-sm-1"></div>

					<div class="col-sm-1"></div>
				</div>
				<div class="row">
					<div class="col-sm-1"></div>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-4">
								<h3><i class="fa fa-envelope"></i>News Straight To Your Inbox</h3>
								<p>
									Receive news and content just how you want it. Set your updates to receive news in real-time or a daily update. Choose from hundreds of beats and topics.
								</p>
							</div>
							<div class="col-sm-4">
								<h3><i class="fa fa-globe"></i>Find and Connect with Experts in Your Industry</h3>
								<p>
									We make it easy to find the right expert in your industry. Easily search through our database of company newsrooms to find the right source for your story quote.
								</p>
							</div>
							<div class="col-sm-4">
								<h3><i class="fa fa-certificate"></i>Access to Thousands of Media Content</h3>
								<p>
									Search and find the right images or videos for your story. Our database of company newsrooms allow journalists to drill down and find that specific image or media piece required for their story.
								</p>
							</div>
						</div>
					</div>
					<div class="col-sm-1"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="sign-up-panel register-footer">
	<div class="container">
		<div class="row">
			<div class="signup-today">
				<h2><strong>Sign up to get news on topics you care about.</strong></h2>
				<form class="form-inline" role="form" method="post" action="<?=
					$ci->ssl_url($ci->website_url('journalists/register')) ?>">
					<div class="form-group">
						<a href="http://www.newswire.com/journalists/register"><button type="submit" class="signup-btn">Get Started Now</button></a>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
