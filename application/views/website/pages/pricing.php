<?php $ci->add_eoh($ci->load->view('website/partials/track-vwo')); ?>

<main class="main" role="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<header class="main-header">
					<h1>Flexible Pricing Made Simple.</h1>
					<p>
						<span>Select the best plan that suits your exact needs. We can help in all areas of </span>
						<span>your content marketing campaigns. Our team is here to help you.</span>
					</p>
				</header>
				<div class="row">
					<div class="col-sm-12">
						<div class="main-body">

							<?= $ci->load->view('website/partials/packages-table') ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<section class="features">
	<div class="container">
		<div class="row">
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<div class="row">
					<div class="col-sm-12">
						<header class="features-header">
							<h2>Need to Submit a Single Press Release?</h2>
							<p>
								Ready to Submit? Order a single Premium Press Release and have your press release published to over 250+ news outlets online.
								Our distribution is fast and affordable for all budgets. Find out why we are the leading provider for online press release distribution.
							</p>
						</header>
					</div>
				</div>
				<div class="row features-content marbot-30">
					<div class="col-sm-12 text-center">
						<a href="pricing/single" class="btn btn-lg btn-success">Order Single Press Release</a>						
					</div>
				</div>
			</div>
			<div class="col-sm-2">
			</div>
		</div>
	</div>
</section>

<div class="separator"></div>

<?= $ci->load->view('website/partials/pricing-custom-distribution') ?>

<div class="separator"></div>

<?= $ci->load->view('website/partials/faq') ?>
<?= $ci->load->view('website/partials/planner-footer') ?>