<?php $ci->title = 'Press Release Writing'; ?>
<?php $single_pr_writing = Model_Item::find_slug('writing-credit'); ?>
<?php $single_pr_distribution = Model_Item::find_slug('premium-pr-credit'); ?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Press Release Writing Services</h1>
					<p>
						<span>Get your news noticed with a compelling press release</span>
						<span>written by our seasoned staff.</span>
					</p>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<div class="ta-center marbot-20">
	<?php $video = new Video_Youtube('ObMShYiKbfw'); ?>
	<?= $video->render(854, 480, array('autoplay' => 1)) ?>
</div>


<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<section class="buy-box get-started-writing">
				<div class="marbot-25">
					<i class="fa fa-pencil"></i> Press Release Writing + Distribution only 
					<strong>$<?= number_format($single_pr_writing->price + $single_pr_distribution->price, 0) ?></strong>
				</div>
				<div>
					<a href="#" class="signup-btn order-writing-distribution">
						Get Started <i class="fa fa-caret-right"></i></a>
				</div>
			</section>
		</div>
	</div>
</div>

<section class="features">
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="row features-content">
					<div class="col-sm-6 marbot-30">
						<h3><i class="fa fa-check"></i> No Guesswork</h3>
						<p>
							We take the guesswork out of writing press releases. Our experienced staff will create a professionally written press release to engage your audience and express your message. We take care of everything so you can focus on your business.
						</p>
					</div>
					<div class="col-sm-6 marbot-30">
						<h3><i class="fa fa-files-o"></i> AP Writing Style</h3>
						<p>
							Our writing team complies with all AP style rules to properly format your press release.
							They have each been specifically trained in AP style in order to provide professionally written press releases for your story.
						</p>
					</div>
				</div>
				<div class="row marbot">
					<div class="col-sm-6 marbot-30">
						<h3><i class="fa fa-refresh"></i> Transparency</h3>
						<p>
							We will send you a draft of your press release before sending it for publishing. This allows you to make any edits or revisions to the press release. This ensures your complete satisfaction before the press release is distributed.
						</p>
					</div>
					<div class="col-sm-6 marbot-30">
						<h3><i class="fa fa-clock-o"></i> Quick Turnaround</h3>
						<p>
							After placing your order, we'll start the writing process in minutes. Fill out a simple company details form and let us do the rest.
							We assign a professional writer to write a press release for you within 24-48 hours after receiving your order.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 marbot-30">
						<h3><i class="fa fa-users"></i> Our Press Release Writing Team</h3>
						<p>Our press release writers were carefully selected to help you write your press release. Whether you don't have the time or don't know how to write a press relase our writers can help. </p><br>
						<p>The team is comprised of University educated and experienced writers from a wide spectrum of backgrounds which allows us to confidently cover all industries and niches. We are able to match the tone and messaging of your story with a writer experienced in your company's industry.</p><br>
						<p>We know how important it is to choose a professional press release writing service and our team will guide you through the process. With your PR writing services comes a dedicated writer experienced in your niche to meet industry's highest standards.</p>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</div>
</section>

<section class="sign-up-panel register-footer register-footer-writing">
	<div class="container">
		<div class="row">
			<div class="signup-today signup-today-writing">
				<h2 class="nomarbot">
					<i class="fa fa-pencil"></i>
					Order press release content writing today, 
					<strong>$<?= number_format($single_pr_writing->price +
						$single_pr_distribution->price, 0) ?></strong>
				</h2>
				<div class="marbot-30">
					<a href="#" class="signup-btn writing-get-started order-writing-distribution">
						Get Started <i class="fa fa-caret-right"></i></a>
				</div>
				<p>($<?= number_format($single_pr_distribution->price, 0) ?>
					Premium Press Release Distribution included with order)</p>
			</div>
		</div>
	</div>
</section>

<script>
	
	$(function() {

		var buttons = $(".order-writing-distribution");
		buttons.on("click", function() {
			var url = <?= json_encode("features/distribution/order?item_id={$single_pr_distribution->id}&add_writing=1") ?>;
			window.location = url;
		});

	});

</script>