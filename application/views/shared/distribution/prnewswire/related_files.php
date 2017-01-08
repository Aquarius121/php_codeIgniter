<?php if ($result->stored_file_id_1 || $result->stored_file_id_2): ?>
	<p style="font-weight: bold;">Related Files</p>
	<?php if ($result->stored_file_id_1): ?>
		<?php $file = Stored_File::from_db($result->stored_file_id_1); ?>
		<?php $name = basename($result->stored_file_name_1); ?>
		<?php $url = $ci->website_url($file->url()); ?>
		<p><a target="_blank" href="<?= $vd->esc($url) ?>">
			<?= $vd->esc($name) ?></a></p>
	<?php endif; ?>
	<?php if ($result->stored_file_id_2): ?>
		<?php $file = Stored_File::from_db($result->stored_file_id_2); ?>
		<?php $name = basename($result->stored_file_name_2); ?>
		<?php $url = $ci->website_url($file->url()); ?>
		<p><a target="_blank" href="<?= $vd->esc($url) ?>">
			<?= $vd->esc($name) ?></a></p>
	<?php endif ?>
	<p></p>
<?php endif ?>