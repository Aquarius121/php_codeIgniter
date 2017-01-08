	<?php if (!$vd->hide_footer): ?>
	<footer class="footer">
		<div class="container">
			<div class="row">

				<div class="col-sm-2 col-xs-3">
					<nav class="footer-nav">
						<h3>Our Company</h3>
						<menu>
							<li><a href="about">About Us</a></li>
							<li><a href="team">Our Team</a></li>
							<li><a href="why-us">Why Newswire</a></li>
							<li><a href="features">Features</a></li>
							<li><a href="pricing">Pricing</a></li>
							<li><a href="helpdesk">Helpdesk</a></li>
							<li><a href="blog">Blog</a></li>
							<li><a href="partners">Partner With Us</a></li>
						</menu>
					</nav>
				</div>

				<div class="col-sm-2 col-xs-3">
					<nav class="footer-nav">
						<h3>Solutions for</h3>
						<menu>
							<li><a href="/industry/business">Business</a></li>
							<li><a href="/industry/health">Health</a></li>
							<li><a href="/industry/technology">Technology</a></li>
							<li><a href="/industry/healthcare">Healthcare</a></li>
							<li><a href="/industry/travel">Travel</a></li>
						</menu>
					</nav>
				</div>

				<div class="col-sm-2 col-xs-3">
					<nav class="footer-nav">
						<h3>Resources</h3>
						<menu>
							<li><a href="planner">PR Planner</a></li>
							<li><a href="journalists">For Journalists</a></li>
							<li><a href="content-guidelines">Content Guidelines</a></li>
							<li><a href="editorial-process">Editorial Process</a></li>
							<li><a href="company">Company Directory</a></li>
							<li><a href="newsroom">Newsroom</a></li>
							<li><a href="feeds">RSS Feeds</a></li>
						</menu>
					</nav>
				</div>

				<div class="col-sm-2 col-xs-3">
					<nav class="footer-nav marbot-20">
						<h3>How to Guides</h3>
						<menu>
							<li><a href="http://guides.newswire.com/guide/how-to-write-a-press-release">Write a Press Release</a></li>
							<li><a href="http://guides.newswire.com/guide/how-to-create-a-newsroom">Create a Newsroom</a></li>
							<li><a href="http://guides.newswire.com/guide/how-to-send-press-releases-via-email">Send PR via Email</a></li>
						</menu>
					</nav>

				</div>

				<div class="col-sm-4">
					<section class="copy">

						<span class="tel">
							<i class="fa fa-phone"></i> <em class="our-phone-number">(800) 713-7278</em>
						</span>

						<address class="adr">
							<span><strong>Newswire</strong> LLC 5 Penn Plaza, 23rd Floor</span>
							<span>New York, NY 10001 | <em class="our-phone-number">(800) 713-7278</em></span>
						</address>

						<div class="business-hours marbot-20 ta-center">
							Our business hours are Monday to Friday <br />
							between 8AM and 7PM (EST).
						</div>

						<img class="cards" src="<?= $vd->assets_base ?>im/website/cards.png" alt="" />

						<div class="footer-social-buttons marbot-15">
							<a href="https://www.facebook.com/inewswire"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
							<a href="https://twitter.com/inewswire"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
							<a href="https://www.linkedin.com/company/newswire-com"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
							<a href="https://plus.google.com/u/0/101924799425201218432/posts"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
						</div>

						<div class="footer-uptime">
							<a href="//uptime.com" target="_blank"><img src="<?= $vd->assets_base ?>im/uptime.png" style="border: 1px solid #ccc" /></a>
						</div>

					</section>
				</div>

			</div>
		</div>

		<div class="copyright">
			<div class="container">
				&copy; Newswire.com LLC All Rights Reserved 2004 - <?= date('Y') ?>
				<span><a href="terms-of-service">Terms of Service</a></span>
				<span><a href="privacy-policy">Privacy Policy</a></span>
			</div>
		</div>

	</footer>
	<?php endif ?>

	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css" />
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />
	<link href="//fonts.googleapis.com/css?family=Work+Sans:400,300,700,800" rel="stylesheet" type="text/css">

	<?= $ci->load->view('partials/defer-jquery') ?>

	<?php

		$render_basic = $ci->is_development();

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap3/js/bootstrap.min.js');
		$loader->add('lib/retina.js');
		$loader->add('lib/imagesloaded.min.js');
		$loader->add('lib/masonry.min.js');
		$loader->add('lib/enquire.min.js');
		$loader->add('js/base.js');
		$loader->add('js/website.js');
		$loader->add('js/matchmedia.js');
		echo $loader->render($render_basic);

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/respond.min.js');

	?>
	
	<!--[if lt IE 9]>
	<?= $loader->render($render_basic) ?>
	<![endif]-->

	<?= $ci->load->view('partials/defer-after') ?>

	<div id="eob">
		<?= $ci->load->view('partials/track-google-analytics') ?>
		<?= $ci->load->view('partials/track-google-remarketing') ?>
		<?= $ci->load->view('partials/track-tout') ?>
		<?= $ci->load->view('partials/track-adroll') ?>
		<?= $ci->load->view('partials/track-linkedin') ?>
		<?= $ci->load->view('partials/clickdesk') ?>
		<?php foreach ($ci->eob as $eob)
			echo $eob; ?>
	</div>

</body>
</html>
