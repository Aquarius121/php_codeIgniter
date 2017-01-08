<?php 

$render_basic = $ci->is_development();

$loader = new Assets\CSS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/bootstrap-datepicker.css');
$loader->add('css/insights.css');
echo $loader->render($render_basic);

$loader = new Assets\JS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/moment.min.js');
$loader->add('lib/bootstrap-datepicker.js');
$loader->add('lib/mustache.js');
$loader->add('js/columnize.js');
$loader->add('js/insights.js');
$ci->add_eob($loader->render($render_basic));
