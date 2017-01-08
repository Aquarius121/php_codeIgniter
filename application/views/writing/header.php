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
		<base href="<?= $base = $ci->config->item('base_url') ?>" />
		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />
		
		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap/css/bootstrap.min.css');
			$loader->add('lib/bootstrap-select.css');
			$loader->add('css/base.css');
			$loader->add('css/manage.css');
			$loader->add('css/writing.css');			
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/jquery.js');
			$loader->add('lib/jquery.create.js');
			$loader->add('js/base.js');
			$loader->add('js/manage.js');
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
		
		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/formdata.js');

		?>

		<!--[if lt IE 10]>
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
				
			</div>
		</header>

		<div class="wrapper container">
			
			
