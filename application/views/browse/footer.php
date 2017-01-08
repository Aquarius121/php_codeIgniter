					</div>
				</div>
			</div>

		</div> <!-- bs3 container -->

		<?php if (!$ci->is_own_domain): ?>
		<footer class="footer">
			<div class="bs3-container">
				<div class="row-fluid">
					<div class="span3">
						<a href="<?= $ci->conf('website_url') ?>" class="footer-brand footer-brand-logo">News<b>wire</b></a>
					</div>
					<div class="span9">
						<nav class="footer-menu">
							<ul>
								<li>
									<i class="fa fa-info-circle"></i>
									<a href="<?= $ci->conf('website_url') ?>about-us">About Newswire</a>
								</li>
								<?php if (Auth::is_user_online()): ?>				
								<li>
									<i class="fa fa-sign-in"></i>
									<a href="manage">Control Panel</a></li>
								<?php else: ?>				
								<li>
									<i class="fa fa-arrow-circle-right"></i>
									<a href="<?= $ci->website_url('features/newsrooms') ?>">
										Get a Newsroom</a></li>	
								<li>
									<i class="fa fa-sign-in"></i> 
									<a href="manage">
										Login</a></li>
								<?php endif ?>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</footer>
		<?php endif ?>

		<div id="ln-container-loader"></div>

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />
		
		<?= $ci->load->view('partials/defer-jquery') ?>

		<div id="eob">

			<?= $ci->load->view('partials/track-google-analytics') ?>
			<?= $ci->load->view('partials/track-google-remarketing') ?>
			<?= $ci->load->view('partials/track-tout') ?>
			<?= $ci->load->view('partials/track-adroll') ?>
			<?= $ci->load->view('partials/track-linkedin') ?>
			
			<?php if (!$ci->is_detached_host): ?>
			<?= $ci->load->view('partials/record-activate', null, true) ?>
			<?= $ci->load->view('partials/record-stats', null, true) ?>
			<?= $ci->load->view('partials/ganal', null, true) ?>
			<?php endif ?>
			
			<?php foreach ($ci->eob as $eob) 
				echo $eob; ?>

		</div>

		<?php

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/imagesloaded.min.js');
			$loader->add('lib/masonry.min.js');
			$loader->add('lib/bootstrap/js/bootstrap.min.js');
			$loader->add('js/base.js');
			$loader->add('js/browse.js');
			$loader->add('js/columnize.js');
			$render_basic = $ci->is_development();
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/json.js');

		?>

		<!--[if lt IE 9]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

		<?= $ci->load->view('partials/defer-after') ?>
			
	</body>
</html>


