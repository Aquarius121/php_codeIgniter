<?php 

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/claim_nr.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<div class="marbot-20"></div>
		<a href="<?= $ci->newsroom->url() ?>">Back to preview</a>
	</div>
</div>

<br>

<div class="row-fluid">
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
						<?php if (@$vd->is_from_private_link): ?>
							<?= $ci->load->view('browse/claim_nr/partials/about_nr_sidebar_pvt_url') ?>
						<?php else: ?>
							<?= $ci->load->view('browse/claim_nr/partials/about_nr_sidebar_direct_url') ?>
						<?php endif ?>
					</div>
				</div>
			</aside>

			<div class="span6 claim_center_area">
				<section class="latest-news">
					
					<header class="ln-header marbot-20">
						<h2 class="marbot-10">Activate the Company 	Newsroom of
							<i><?= @$ci->newsroom->company_name ?></i></h2>
					</header>

					<div class="marbot-20">
						<img src="/assets/im/activate.png">
					</div>

					<p></p><p></p>

					<div class="row-fluid marbot-30">
						<div class="span9 offset2">
						<a href="browse/claim_nr/checkout">
							<button class="span12 bt-orange" value="1" name="claim"
								type="submit">Continue to Activate This Newsroom</button></a>
						</div>
					</div>

				</section>
			</div>

			<aside class="span3 aside claim_right_side">
				<div id="locked_aside">

					<div class="latest-news">
						<header class="ln-header marbot-20">
							<h2 class="marbot-10 text-center">Why Activate?</h2>
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
										<li class="marbot-15">Verify and Ensure all your company details
											are correct</li>
										<li class="marbot-15">Easily stay in contact with your
											subscribers</li>
										<li class="marbot-15">Customize to match your company
											branding</li>
									</ul>
								</div>
							</div>

						</section>
					</div>
				</div>
			</aside>
		</div>
	</div>

<script>

$(function() {
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