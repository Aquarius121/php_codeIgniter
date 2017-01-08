
			</div>
		</div>

		<div id="eob-loader" class="no-print"></div>

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />

		<?= $ci->load->view('partials/defer-jquery') ?>

		<?php

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap3-select.css');
			$loader->add('lib/jquery.mmenu.min.css');
			$loader->add('lib/bootstrap-datetimepicker.css');		
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/json.js');

		?>
		
		<!--[if lt IE 9]>
		<?= $loader->render($render_basic) ?>
		<![endif]-->

		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap3/js/bootstrap.min.js');
			$loader->add('lib/jquery.mmenu.min.js');
			$loader->add('lib/bootstrap3/js/bootstrap-tabcollapse.js');
			$loader->add('lib/bootstrap3-select.js');		
			$loader->add('js/base.js');
			$loader->add('js/manage.js');
			$loader->add('js/required.js');
			$loader->add('lib/jquery.lockfixed.js');
			$loader->add('lib/bootbox.min.js');
			$loader->add('lib/bootstrap-datetimepicker.js');
			echo $loader->render($render_basic);

		?>

		<?= $ci->load->view('partials/defer-after') ?>

		<div id="eob" class="no-print">
			<?= $ci->load->view_html('manage/partials/menu.js') ?>
			<?= $ci->load->view('partials/track-google-analytics') ?>
			<?= $ci->load->view('partials/track-tout') ?>
			<?= $ci->load->view('partials/track-adroll') ?>
			<?= $ci->load->view('partials/track-linkedin') ?>
			<?= $ci->load->view('partials/clickdesk') ?>
			<?php foreach ($ci->eob as $eob) 
				echo $eob; ?>
		</div>

	</body>

</html>