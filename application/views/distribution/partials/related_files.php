<?php if ($result->stored_file_id_1 || $result->stored_file_id_2): ?>
	<strong>Related Files</strong><br />
	<?php if ($result->stored_file_id_1) : ?>
		<?php $file = Stored_File::from_db($result->stored_file_id_1); ?>
		<?php $name = basename($result->stored_file_name_1); ?>
		<?php $url = $ci->website_url($file->url()); ?>
		<a target="_blank" href="<?= $vd->esc($url) ?>"><?= 
			$vd->esc($name) ?></a><br />
	<?php endif; ?>
	<?php if ($result->stored_file_id_2) : ?>
		<?php $file = Stored_File::from_db($result->stored_file_id_2); ?>
		<?php $name = basename($result->stored_file_name_2); ?>
		<?php $url = $ci->website_url($file->url()); ?>
		<a target="_blank" href="<?= $vd->esc($url) ?>"><?= 
			$vd->esc($name) ?></a><br />
	<?php endif ?>
	<br />
<?php endif ?>