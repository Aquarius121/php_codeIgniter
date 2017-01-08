<!doctype html>
<html lang="en">
	<head>
		<title>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> |
			<?php endif ?>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
			<?php if ($ci->is_common_host): ?>
			Newswire
			<?php else: ?>
			<?= $vd->esc($ci->newsroom->company_name) ?>
			<?php endif ?>		
		</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
		<base href="<?= $base = $ci->env['base_url'] ?>" />
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic,300,600,300italic,400italic,600italic" />

		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap/css/bootstrap.min.css');
			$loader->add('lib/bootstrap-select.css');
			$loader->add('css/base.css');
			$loader->add('css/manage-bs2.css');
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/jquery.js');
			$loader->add('js/base.js');
			$loader->add('js/manage.js');
			$loader->add('js/reseller.js');
			echo $loader->render($render_basic);

		?>

		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/html5shiv.js');

		?>
		
		<!--[if lt IE 9]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

		<script>
		
		CKEDITOR_BASEPATH = <?= json_encode("{$vd->assets_base}lib/ckeditor/") ?>;
		
		</script>
	</head>
	<body>
		
		<?= $ci->load->view('shared/partials/header-admo') ?>
		
		<header class="header">
			<div class="container">
			
				<h1 class="logo"><a href="manage" accesskey="1">Newswire</a></h1>
				<?= $ci->load->view('shared/partials/header-login-panel') ?>
					
			</div>
		</header>

		<div class="wrapper container">
			<section class="main-menu-bar">
				<div class="container">
					<div class="row-fluid">
						<div class="span8">
							<nav class="main-menu">
								<ul id="nav-main" class="nav-activate">
									<?php if ($ci->is_reseller_editor()): ?>
									<li>
										<a href="reseller/dashboard" data-on="^reseller/(dashboard|$)" 
											class="menu-icons menu-icons-dashboard">
											<i></i><span>Dashboard</span>
										</a>
									</li>									
									<?php endif ?>
									<li>
										<a href="reseller/publish" data-on="^reseller/publish"
											class="menu-icons menu-icons-ipublish">
											<i></i><span>iPublish</span>
										</a>
									</li>
									<li>
										<a href="reseller/account/branding" data-on="^reseller/account/branding" 
											class="menu-icons menu-icons-inewsroom">
											<i></i><span>Branding</span>
										</a>
									</li>
								</ul>
							</nav>
						</div>
						<?= $this->load->view('reseller/partials/search', null, true) ?>
					</div>
				</div>
			</section>
