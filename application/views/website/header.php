<!DOCTYPE html>
<html lang="en" class="newswire">
<head>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<base href="<?= $ci->env['base_url'] ?>" />

	<?php if ($vd->meta_content && $meta_data = $vd->meta_content->raw_data()): ?>
		<?php foreach ($meta_data as $k => $v): ?>
		<meta name="<?= $vd->esc($k) ?>" content="<?= $vd->esc($v) ?>" />
		<?php endforeach ?>
	<?php endif ?>

	<title>
		<?php if (!empty($vd->meta_content->title)): ?>
			<?= $vd->esc($vd->meta_content->title) ?>
		<?php else: ?>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> |
			<?php endif ?>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
			Newswire
		<?php endif ?>
	</title>

	<?php if (!empty($vd->meta_content->keywords)): ?>
		<meta name="keywords" content="<?= $vd->esc($vd->meta_content->keywords) ?>" />
	<?php endif ?>
	
	<?php if (!empty($vd->meta_content->description)): ?>
		<meta name="description" content="<?= $vd->esc($vd->meta_content->description) ?>" />
	<?php endif ?>

	<?php 

		$render_basic = $ci->is_development();

		$loader = new Assets\CSS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap3/css/bootstrap.min.css');
		$loader->add('css/base.css');
		$loader->add('css/website.css');
		echo $loader->render($render_basic);

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/html5shiv.js');

	?>

	<!--[if lt IE 9]><?= $loader->render($render_basic); ?><![endif]-->
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->

	<?= $ci->load->view('partials/defer-before') ?>

	<link href="<?= $vd->assets_base ?>im/favicon.ico?<?= $vd->version ?>" type="image/x-icon" rel="shortcut icon" />
	<link href="<?= $vd->assets_base ?>im/favicon.ico?<?= $vd->version ?>" type="image/x-icon" rel="icon" />

	<?= $ci->load->view('partials/track-kiss-metrics') ?>

	<meta name="google-site-verification" content="q8ryY6fz2fhB9rFoyyhK0b6aq7qYS99CRThypWPaZOA" />

	<?php if (isset($vd->canonical_url)): ?>
	<link rel="canonical" href="<?= $ci->website_url() ?><?= $vd->esc($vd->canonical_url) ?>" />
	<?php endif ?>

	<?php if ($ci->eoh): ?>
	<?php foreach ($ci->eoh as $eoh)
		echo $eoh; ?>
	<?php endif ?>

</head>
<body class="relative">

	<!--[if lt IE 8]><p class="chromeframe">You are using an <strong>outdated</strong> browser.
	Please <a href="http://browsehappy.com/">upgrade your browser</a> or
	<a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a>
	to improve your experience.</p><![endif]-->

	<div id="fb-root"></div>

	<header class="header">

		<nav class="navbar">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="">
						<img src="<?= $vd->assets_base ?>im/website/logo-inewswire.svg" alt="Newswire">
					</a>
				</div>
				<div class="navbar-collapse collapse">
					<div class="header-phone header-phone-inside our-phone-number">(800) 713-7278</div>
					<menu class="nav navbar-nav navbar-right nav-activate" data-nav-selector=">li">
						<li data-on="^features">
							<a href="features">Features</a>
							<menu class="navbar-submenu nav-activate" data-nav-selector=">li">
								<li data-on="^features/distribution"><a href="features/distribution">Distribution</a></li>
								<li data-on="^features/outreach"><a href="features/outreach">Media Outreach</a></li>
								<li data-on="^features/writing"><a href="features/writing">Writing Service</a></li>
								<!-- <li data-on="^features/pitching"><a href="features/pitching">Pitching</a></li> -->
								<li data-on="^features/newsrooms"><a href="features/newsrooms">Company Newsrooms</a></li>
								<li data-on="^features/analytics"><a href="features/analytics">Analytics</a></li>
								<li data-on="^features/social"><a href="features/social">Social</a></li>
							</menu>
						</li>
						<li data-on="^pricing(/?|(/m\d+)|/single)$">
							<a href="pricing">Pricing</a>
							<menu class="navbar-submenu nav-activate" data-nav-selector=">li">
								<li data-on="^pricing/single$"><a href="pricing/single">Single Distribution</a></li>
								<li data-on="^pricing/?$"><a href="pricing">Month to Month</a></li>
								<li data-on="^pricing/m12"><a href="pricing/m12">One Year (10% Off)</a></li>
								<!--li data-on="^pricing/m24"><a href="pricing/m24">Two Years (25% Off)</a></li-->
							</menu>
						</li>
						<li data-on="^newsroom">
							<a href="newsroom">Newsroom</a>
							<menu class="navbar-submenu nav-activate" data-nav-selector=">li" id="news-center-menu">
								<li data-on="^newsroom(/?|(/page/.+)?)$">
									<a href="newsroom">All Categories</a></li>
								<li data-on="^newsroom/business(/?|(/page/.+)?)$">
									<a href="newsroom/business">Business</a></li>
								<li data-on="^newsroom/arts-and-entertainments(/?|(/page/.+)?)$">
									<a href="newsroom/arts-and-entertainments">Entertainment</a></li>
								<li data-on="^newsroom/medicine-and-healthcare(/?|(/page/.+)?)$">
									<a href="newsroom/medicine-and-healthcare">Health</a></li>
								<li data-on="^newsroom/business-finance(/?|(/page/.+)?)$">
									<a href="newsroom/business-finance">Finance</a></li>
								<li data-on="^newsroom/beats/?$">
									<a href="newsroom/beats">Other</a></li>
								<li>
									<div class="btn-group btn-group-default">
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span id="news-center-filter-label">Filter Content</span> <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li><a href="newsroom/all">View All</a></li>
											<li class="divider"></li>
											<li><a href="newsroom/pr">Press Releases</a></li>
											<li class="divider"></li>
											<li><a href="newsroom/audio">Audio</a></li>
											<li><a href="newsroom/event">Events</a></li>
											<li><a href="newsroom/image">Images</a></li>
											<li><a href="newsroom/news">News</a></li>
											<li><a href="newsroom/video">Videos</a></li>
										</ul>
									</div>
								</li>
								<li>
									<div class="news-center-search-box">
										<form class="input-group"
											method="get" action="newsroom/search">
											<input type="text" name="terms" class="form-control" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button">
													<i class="fa fa-search"></i></button>
											</span>
										</form>
									</div>
								</li>
								<li>
									<span class="news-center-rss-button">
										<?php if (isset($vd->news_center_params)): ?>
											<?php $params = gstring(implode('/', $vd->news_center_params)); ?>
											<a href="newsroom/rss<?= value_if_test($params, "/{$params}") ?>"><i class="fa fa-rss"></i></a>
										<?php endif ?>
									</span>
								</li>
							</menu>
						</li>

						<li data-on="^how-it-works">
							<a href="how-it-works" class="btn btn-default take-tour">
								How It Works
							</a>
						</li>

						<li data-on="^login">
							<?php if (isset($vd->auth_to_redirect)): ?>
							<a href="<?= $vd->auth_to_redirect ?>">Login</a>
							<?php else: ?>
							<a href="login">Login</a>
							<?php endif ?>
						</li>

						<li data-on="^register">
							<a href="register" class="btn btn-success">
								Sign up
							</a>
						</li>

					</menu>
				</div>
				<div class="header-phone header-phone-outside our-phone-number">(800) 713-7278</div>
			</div>
		</nav>
	</header>