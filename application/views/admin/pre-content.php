<?= $ci->load->view('admin/partials/search') ?>
<section class="main">
	<div class="container">
		<div id="feedback">
		<?php $ci->process_feedback(); ?>
		<?php foreach ($ci->feedback as $feedback): ?>
		<div class="feedback"><?= $feedback ?></div>
		<?php endforeach ?>
		</div>