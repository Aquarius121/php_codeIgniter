<?php 

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/claim_nr.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid marbot-20">
	<div class="span12">
		<a href="<?= $ci->newsroom->url() ?>">Back to Newsroom</a>
	</div>
</div>

<form class="required-form row-fluid" action="browse/claim_nr/save" method="post">
	<div class="span12">
		<div class="content content-no-tabs row-fluid">
			<aside class="span3 aside">
				<div id="locked_aside">

					<div class="latest-news">
						<header class="ln-header marbot-20">
							<h2 class="marbot-10">About This Newsroom</h2>
						</header>
					</div>

					<div class="aside-properties padding-top marbot-20">
						<?= $ci->load->view('browse/claim_nr/partials/free_activation_sidebar') ?>
					</div>
				</div>
			</aside>

			<div class="span6 claim_center_area">
				<section class="latest-news">
					<header class="ln-header marbot-20">
						<h2 class="marbot-10">Verify the Company Newsroom of
							<i><?= @$ci->newsroom->company_name ?></i></h2>
					</header>

					<div class="row-fluid marbot-20">
						<div class="span12 relative">
							<input type="text" class="span12 in-text nomarbot has-placeholder" 
								name="company_name"
								readonly=readonly placeholder="Company Name"
								value="<?= $ci->newsroom->company_name ?>" />
							<strong class="placeholder">Company Name</strong>
						</div>
					</div>

					<div class="row-fluid marbot-20">
						<div class="span12 relative">
							<input type="text" class="span12 in-text nomarbot has-placeholder required" 
								name="rep_name" data-required-name="Company Representative"
								placeholder="Company Representative"  />
							<strong class="placeholder">Company Representative</strong>
						</div>
					</div>

					<div class="row-fluid marbot-20">
						<div class="span12 relative">
							<input type="email" class="span12 in-text nomarbot has-placeholder required"
								name="email" placeholder="Your Email" data-required-name="Email" />
							<strong class="placeholder">Your Email</strong>
						</div>
					</div>

					<div class="row-fluid marbot-20">
						<div class="span12 relative">
							<input type="text" class="span12 in-text nomarbot has-placeholder required" 
								name="phone" placeholder="Your Phone Number" 
								data-required-name="Phone Number" />
							<strong class="placeholder">Your Phone Number</strong>
						</div>
					</div>
					<div class="row-fluid marbot-30">
						<div class="span12">
							<div class="pull-right links-list">
								* Your info is SAFE with us. We promise to never rent, sell
							</div>
							<div class="pull-right links-list">
								or share your email and info with any other company.
							</div>
						</div>
					</div>

					<div class="row-fluid marbot-30">
						<div class="span6 offset6">
							<button class="span12 bt-orange" value="1" name="claim" type="submit">
								Submit Free Verification</button>
						</div>
					</div>
				</section>
			</div>



			<aside class="span3 aside claim_right_side">
				<div id="locked_aside">

					<div class="latest-news">
						<header class="ln-header marbot-20">
							<h2 class="marbot-10 text-center">Why Verify?</h2>
						</header>
					</div>

					<div class="padding-top marbot-20">
						<section class="ap-block">
							<div class="text-center marbot-20">
								<img src="<?= $vd->assets_base ?>im/verify_green.png" 
									alt="verify badge" />
							</div>
							
							<div class="row-fluid">
 								<div class="span1"></div>
  								<div class="span11">
		  							<ul class="rb-additional-resources rb-claim">
										<li class="marbot-15">Ensure all your company details 
											are correct</li>
										<li class="marbot-15">Easily stay in contact with your
											subscribers</li>
										<li class="marbot-15">Customize to match your company 
											branding</li>
										<li class="marbot-15">Absolutely <strong>FREE. </strong>
											Verify today</li>
									</ul>
								</div>
							</div>

						</section>
					</div>
				</div>
			</aside>
		</div>
	</div>
</form>

<script>
$(function(){
	$(".aside-left").css("display", "none");
});
</script>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>
