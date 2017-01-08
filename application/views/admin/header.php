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
			Newswire
		</title>
		<meta charset="utf-8" />		
		<meta name="viewport" content="width=device-width" />
		<base href="<?= $base = $ci->env['base_url'] ?>" />
		
		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap/css/bootstrap.min.css');
			$loader->add('lib/bootstrap2-select.css');
			$loader->add('css/base.css');
			$loader->add('css/manage-bs2.css');
			$loader->add('css/admin.css');
			$loader->add('lib/bootstrap-datetimepicker.css');
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

		<?= $ci->load->view('partials/defer-before') ?>

		<script> 

			CKEDITOR_BASEPATH = <?= json_encode("{$vd->assets_base}lib/ckeditor/") ?>;
			NR_COMPANY_ID = <?= json_encode($ci->newsroom->company_id) ?>;
			RELATIVE_URI = <?= json_encode($ci->uri->uri_string) ?>;
			ASSETS_VERSION = <?= json_encode($vd->version) ?>;

		</script>

		<?php if ($ci->eoh): ?>
		<?php foreach ($ci->eoh as $eoh) 
			echo $eoh; ?>
		<?php endif ?>
	</head>
	<body>
		
		<?= $ci->load->view('shared/partials/header-admo') ?>
		
		<header class="header">
			<div class="container">
				
				<h1 class="logo"><a href="manage" accesskey="1">Newswire</a></h1>
				<div class="newsroom-panel">
				</div>
				
				<?= $ci->load->view('shared/partials/header-login-panel') ?>
						
			</div>
		</header>

		<div class="wrapper container">
			<section class="main-menu-bar">
				<div class="container">
					<div class="row-fluid">
						<div class="span12">
							<nav class="main-menu">
								<ul id="nav-main" class="nav-activate nav-main-compact">
									<li>
										<a href="admin/publish<?= $vd->esc(gstring()) ?>" data-on="^admin/publish">
											Distribution
											<?php if ($vd->menu_count_publish): ?>
												<span class="menu-count"><?= (int) $vd->menu_count_publish ?></span>
											<?php endif ?>
										</a>
									</li>
									<li>
										<a href="admin/contact<?= $vd->esc(gstring()) ?>" data-on="^admin/contact">
											Media Outreach
										</a>
									</li>
									<li>
										<a href="admin/companies<?= $vd->esc(gstring()) ?>" 
											data-on="^admin/(companies|nr_builder)">
											Companies
										</a>
									</li>
									<li>
										<a href="admin/users<?= $vd->esc(gstring()) ?>" data-on="^admin/users">
											Users
										</a>
									</li>	
									<li>
										<a href="admin/store<?= $vd->esc(gstring()) ?>" data-on="^admin/(virtual/)?store">
											Store
										</a>
									</li>
									<li>
										<a href="admin/writing<?= $vd->esc(gstring()) ?>" data-on="^admin/writing">
											Writing
											<?php if ($vd->menu_count_writing): ?>
												<span class="menu-count"><?= (int) $vd->menu_count_writing ?></span>
											<?php endif ?>
										</a>
									</li>	
									<li>
										<a href="admin/analytics<?= $vd->esc(gstring()) ?>" data-on="^admin/analytics">
											Analytics
										</a>
									</li>
									<li>
										<a href="admin/other<?= $vd->esc(gstring()) ?>" data-on="^admin/other">Other</a>
									</li>
									<li>
										<a href="admin/logs<?= $vd->esc(gstring()) ?>" data-on="^admin/logs">Logs</a>
									</li>
									<li>
										<a href="admin/settings<?= $vd->esc(gstring()) ?>" data-on="^admin/settings">
											Settings
										</a>
									</li>
								</ul>
							</nav>
						</div>
					</div>
				</div>
			</section>