	</div> <!-- wrapper -->

	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" />
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:700" />
	
	<div id="eob-loader"></div>

	<?php

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/json.js');
		$render_basic = $ci->is_development();

	?>

	<!--[if lt IE 9]>
	<?= $loader->render($render_basic) ?>
	<![endif]-->

	<?= $ci->load->view('partials/defer-jquery') ?>

	<?php 

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap/js/bootstrap.min.js');
		$loader->add('lib/jquery.browser.mobile.js');
		$loader->add('lib/bootstrap2-select.js');
		$loader->add('lib/jquery.lockfixed.js');
		$loader->add('lib/bootstrap-datetimepicker.js');
		$loader->add('lib/bootbox.min.js');
		$loader->add('js/base.js');
		$loader->add('js/manage.js');
		$loader->add('js/admin.js');		
		echo $loader->render($render_basic);

	?>

	<?= $ci->load->view('partials/defer-after') ?>

	<script>
	
	$(function() { 
		
		$(".selectpicker").on_load_select();
		
	});
	
	</script>

	<?php if ($ci->eob): ?>
	<div id="eob">
		<?php foreach ($ci->eob as $eob) 
			echo $eob; ?>
	</div>
	<?php endif ?>
	
</body>
</html>