<!DOCTYPE html>
<html lang="en" class="newswire">
<head>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<base href="<?= $ci->env['base_url'] ?>" />
	<title>
		Newswire.com
	</title>

	
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,300italic,400italic,600italic"
		rel="stylesheet" type="text/css" />

	<?php

		$loader = new Assets\CSS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap3/css/bootstrap.min.css');
		$loader->add('css/base.css');
		$loader->add('css/manage.css');
		$render_basic = $ci->is_development();
		echo $loader->render($render_basic);

	?>

	<?php if (isset($vd->canonical_url)): ?>
	<link rel="canonical" href="<?= $ci->website_url() ?><?= $vd->esc($vd->canonical_url) ?>" />
	<?php endif ?>
	
</head>
<body class="relative auth-complete">

	<div id="fb-root"></div>

	<header class="header">

		<nav class="navbar">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand navbar-social-auth" href="">
						<img src="<?= $vd->assets_base ?>im/website/logo-inewswire.svg" alt="Newswire">
					</a>
				</div>
			</div>
		</nav>
	</header>