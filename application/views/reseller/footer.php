	</div> <!-- wrapper -->
	
	<footer class="footer">
		<div class="container">
			<div class="row-fluid">
				<div class="span6">
					<ul>
						<li><a href="<?= $ci->website_url() ?>">Return to Website</a></li>
					</ul>
				</div>
				<div class="span6">
					<ul class="pull-right">
						<?php if ($ci->is_reseller_editor()): ?>
						<li><a href="reseller/dashboard">Dashboard</a></li>
						<?php endif ?>
						<li><a href="reseller/publish">iPublish</a></li>
						<li><a href="reseller/account/branding">Branding</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>

	<div id="eob" class="no-print">
	<?php foreach ($ci->eob as $eob) echo $eob; ?>
	<?= $ci->load->view('partials/track-google-analytics') ?>
	</div>
	
	<?php

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/bootstrap/js/bootstrap.min.js');
		$loader->add('lib/jquery.browser.mobile.js');
		$loader->add('lib/bootstrap-select.js');
		$loader->add('lib/jquery.lockfixed.js');
		
		$render_basic = $ci->is_development();
		echo $loader->render($render_basic);

	?>

	<?php

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/json.js');

	?>
	<!--[if lt IE 9]><?= $loader->render($render_basic) ?><![endif]-->
	
	<script>
	
	$(function() { 
		
		$(".selectpicker").on_load_select();
		
	});
	
	</script>
	
</body>
</html>
