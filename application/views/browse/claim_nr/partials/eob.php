<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>