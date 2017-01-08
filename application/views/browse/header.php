<!doctype html>
<html lang="en" class="newswire 
	<?= value_if_test($ci->is_common_host,   'is-common-host') ?>
	<?= value_if_test($ci->is_detached_host, 'is-detached-host') ?>
	<?= value_if_test(Auth::is_admin_mode(), 'is-admin-mode') ?>">
	<head>
		<?php if ($vd->is_auto_built_unclaimed_nr): ?>
			<meta name="robots" content="noindex" />
		<?php endif ?>
		<title>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> | 
			<?php endif ?>			
			<?php if ($vd->nr_custom && $vd->nr_custom->headline): ?>
				<?= $vd->esc($vd->nr_custom->headline) ?>
			<?php else: ?>
				Company Newsroom of
			 	<?= $vd->esc($ci->newsroom->company_name) ?>
			<?php endif ?>
		</title>

		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<base href="<?= $ci->env['base_url'] ?>" />

		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap/css/bootstrap.min.css');
			$loader->add('css/base.css');
			$loader->add('css/browse.css');
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/html5shiv.js');

		?>

		<!--[if lt IE 9]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/formdata.js');

		?>

		<!--[if lt IE 10]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

		<?= $ci->load->view('partials/defer-before') ?>

		<?php if (!$ci->is_common_host): ?>
		<?= $ci->load->view('browse/partials/custom-css.php') ?>
		<?php endif ?>

		<?php if (isset($vd->m_content)): ?>

			<link rel="canonical" href="<?= $ci->env['base_url'] ?><?= $vd->m_content->url() ?>" />
			<meta property="og:url" content="<?= $ci->env['base_url'] ?><?= $vd->m_content->url() ?>" />
			<meta property="og:title" content="<?= $vd->esc($vd->m_content->title) ?>" />
			<meta property="og:description" content="<?= $vd->esc($vd->m_content->summary) ?>" />
			<meta property="og:keywords" content="<?= $vd->esc($vd->m_content->get_tags_string()) ?>" />
			<meta name="description" content="<?= $vd->esc($vd->m_content->summary) ?>" />
			<meta name="keywords" content="<?= $vd->esc($vd->m_content->get_tags_string()) ?>" />

			<?php $cover_image = Model_Image::find($vd->m_content->cover_image_id); ?>
			<?php if ($cover_image): ?>
				<?php $orig_variant = $cover_image->variant('original'); ?>
				<?php $orig_url = Stored_File::url_from_filename($orig_variant->filename); ?>
				<meta property="og:image" content="<?= $ci->common()->url($orig_url) ?>" />
			<?php endif ?>

		<?php endif ?>

		<link href="<?= $vd->assets_base ?>im/favicon.ico?<?= $vd->version ?>" type="image/x-icon" rel="shortcut icon" />
		<link href="<?= $vd->assets_base ?>im/favicon.ico?<?= $vd->version ?>" type="image/x-icon" rel="icon" />

	</head>

	<body class="<?= value_if_test($ci->is_own_domain, 'is-own-domain') ?>">

		<!--[if lt IE 7]><p class="chromeframe">You are using an <strong>outdated</strong> browser.
		Please <a href="http://browsehappy.com/">upgrade your browser</a> or
		<a href="http://www.google.com/chromeframe/?redirect=true">activate Google
		Chrome Frame</a> to improve your experience.</p><![endif]-->

		<?php if ($vd->nr_custom && 
			$vd->nr_custom->raw_data() &&
			$vd->nr_custom->raw_data()->inject_pre_header): ?>
		<?= $vd->nr_custom->raw_data()->inject_pre_header ?>
		<?php endif ?>

		<?= $ci->load->view('shared/partials/header-admo', array('is_browse_area' => 1)) ?>
		<?= $ci->load->view('browse/partials/top-panel') ?>

		<div class="bs3-container-back"></div>
		<div class="bs3-container" id="root-bs3-container">

			<?php if ( /* 0 ==> default */ $ci->newsroom->company_id): ?>
			<?php $lo_height = 0; ?>
			<?php $lo_im = $vd->nr_custom ? Model_Image::find($vd->nr_custom->logo_image_id) : null; ?>	
			<?php if ($lo_im) $lo_variant = $lo_im->variant('header'); ?>
			<?php if ($lo_im) $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
			<?php if ($lo_im) $lo_height = $lo_variant->height; ?>
			<?php $vd->company_logo_m_image = $lo_im; ?>
			<header class="org-header
				<?= value_if_test($lo_height < 50, 'slim') ?>">
				<div class="row-fluid">

					<div class="span9 org-header-company">
						<?php if ($lo_im): ?>
						<div class="org-header-logo">
							<a href="<?= $ci->newsroom->url(null, true) ?>">
								<img src="<?= $lo_url ?>" alt="<?= $vd->esc($ci->newsroom->company_name) ?>" />
							</a>
						</div>
						<?php endif ?>
						<div class="org-header-text">
							<?php if (!$ci->is_common_host): ?>
							<span>
								<?php if ($vd->nr_custom && 
									$vd->nr_custom->headline_prefix): ?>
								<span class="prefix">
									<?= $vd->esc($vd->nr_custom->headline_prefix) ?>
								</span>
								<?php else: ?>
								<span class="prefix">
									the company newsroom of
								</span>
								<?php endif ?>
								<br />
								<h1>
									<?php if ($ci->is_common_host): ?>
									<a href="<?= $vd->esc($vd->nr_profile->website) ?>">
										<?= $vd->esc($ci->newsroom->company_name) ?>
									</a>
									<?php else: ?>
									<a href="<?= $ci->newsroom->url(null, true) ?>">
										<?php if (@$vd->nr_custom->headline_h1): ?>
										<?= $vd->esc($vd->nr_custom->headline_h1) ?>
										<?php else: ?>
										<?= $vd->esc($ci->newsroom->company_name) ?>
										<?php endif ?>
									</a>
									<?php endif ?>
								</h1>
							</span>
							<?php else: ?>
								<span>
									<span class="prefix">an official press release from</span>
									<br /><h1><?= $vd->esc($ci->newsroom->company_name) ?></h1>
								</span>
							<?php endif ?>
						</div>
					</div>

					<?php if (!$vd->is_claim_nr && !$ci->is_common_host): ?>
					<div class="span3 org-header-search">
						<form action="browse/search" method="get">
							<input type="text" name="terms" placeholder=""
								value="<?= $vd->esc($this->input->get('terms')) ?>" />
							<button type="submit"><i class="fa fa-search"></i></button>
						</form>
					</div>
					<?php endif ?>

				</div>
			</header>
			<?php endif ?>

			<div id="content-container" class="
				<?= value_if_test($vd->switched_cols, 'switched-cols') ?>
				<?= value_if_test($vd->full_width,    'full-width') ?>">

				<div class="main-cols clearfix">

					<?php if (!$vd->full_width): ?>
					<aside class="aside aside-sidebar accordian-section">
						<?= $ci->load->view('browse/partials/aside') ?>
					</aside>
					<?php endif ?>

					<div class="main-content-container">

						<div id="feedback">
						<?php $ci->process_feedback(); ?>
						<?php foreach ($ci->feedback as $feedback): ?>
						<div class="feedback"><?= $feedback ?></div>
						<?php endforeach ?>
						</div>