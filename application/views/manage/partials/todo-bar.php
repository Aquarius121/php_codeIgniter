<?php foreach ($bar->stages() as $stage): ?>
<?php if ($stage->is_done) continue; ?>
<li>
	<?php if (empty($stage->info_link)): ?>
	<?= $stage->display_name ?>
	<?php else: ?>
	<a href="<?= $bar->newsroom()->url($stage->info_link) ?>">
		<?= $stage->display_name ?>
	</a>
	<?php endif ?>
</li>
<?php endforeach ?>