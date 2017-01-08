<div class="feedback-alert feedback-<?= $feedback->status ?>">
	<?php if ($feedback->title): ?>
		<h3><?= $vd->esc($feedback->title) ?></h3> 
	<?php endif ?>
	<?= $feedback->html ?>
</div>