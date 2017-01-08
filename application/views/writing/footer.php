	</div> <!-- wrapper -->
	
	<footer class="footer"></footer>

	<script>

	$(window).load(function() { 
		$(".selectpicker").on_load_select();
	});

	</script>

	<?php

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/jquery.browser.mobile.js');
		$loader->add('lib/bootstrap/js/bootstrap.min.js');
		$loader->add('lib/bootstrap-select.js');
		$loader->add('lib/jquery.lockfixed.js');
		$render_basic = $ci->is_development();
		echo $loader->render($render_basic);

	?>
	
</body>
</html>


