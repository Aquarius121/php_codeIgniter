<script> window.$ = undefined; </script>

<?php 

$loader = new Assets\JS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/jquery.js');
$loader->add('lib/jquery.create.js');
$render_basic = $ci->is_development();
echo $loader->render($render_basic);

?>