<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/media_database_client.js');
	$loader->add('js/media_database.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Media Database</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $ci->load->view('shared/media_database/header') ?>

<?php if ($vd->chunkination->total()): ?>
<?= $ci->load->view('admin/contact/media_database/list_container') ?>
<?php else: ?>
<?= $ci->load->view('manage/contact/media_database/list_container') ?>
<?php endif ?>

<?= $ci->load->view('shared/media_database/footer') ?>

<script> 

window.create_list_modal_id = <?= 
	json_encode($vd->create_list_modal_id) ?>; 
	
</script>