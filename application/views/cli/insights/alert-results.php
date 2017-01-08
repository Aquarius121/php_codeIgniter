<?php $mustache = new Mustache_Engine(); ?>
<?php $template = $this->load->view_raw('cli/insights/result.mustache'); ?>

<div class="results">
	<?php foreach ($vd->results as $result): ?>
		<?= $mustache->render($template, $result) ?>
	<?php endforeach ?>
</div>