<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/media_database_client.js');
	$loader->add('js/media_database.js');
	$loader->add('lib/bootbox.min.js');

	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 page-title">
				<h2>Media Database</h2>
			</div>
		</div>
	</header>

	<div class="panel panel-default md-max-width">
		<div class="panel-body" id="md-panel-body">

			<?= $ci->load->view('shared/media_database/header') ?>
			<?= $ci->load->view('manage/contact/media_database/list_container') ?>
			<?= $ci->load->view('shared/media_database/footer') ?>

			<script> window.create_list_modal_id = <?= 
				json_encode($vd->create_list_modal_id) ?>; </script>

		</div>
	</div>
</div>

<script>
	
$(function() {

	// user panel doesn't show duplicates by default
	$("#options-unique-only").prop("checked", true);
	var client = window.__media_database_ob.client;
	client.options.unique_only = true;

});

</script>