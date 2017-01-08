<!doctype html>
<html>
	<head>
		<title>Newsroom</title>
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

		<!--[if lt IE 9]><?= $loader->render($render_basic); ?><![endif]-->
	
		<style>
		
		body { 
			margin: 50px auto;
			width: 600px;
		}		
		
		.form-horizontal .control-label {
			width: 150px;
		}
		
		.form-horizontal .controls {
			margin-left: 170px;
		}

		.unsubscribe-form label{
			display: inline;
		}

		.unsubscribe-form input[type="radio"]{
			margin: 0 5px;
		}
			
		</style>
	</head>
	<body>
	