<section class="main">
	<div class="container">
		
		<?php $ci->process_feedback(); ?>

		<?php if ($ci->feedback): ?>
		<div id="feedback">
			<?php foreach ($ci->feedback as $feedback): ?>
			<div class="feedback"><?= $feedback ?></div>
			<?php endforeach ?>
		</div>
		<?php endif ?>