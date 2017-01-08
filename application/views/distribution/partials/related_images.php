<?php $images = $result->get_images(); ?>
<?php if (count($images) > 1 || (count($images) && $images[0]->id != $result->cover_image_id)): ?>
	<strong>Related Images</strong><br />
	<?php foreach ($images as $image): ?>
		<?php $web_image = Model_Image::find($image->id); ?>
		<?php $web_variant = $web_image->variant('view-web'); ?>
		<?php $orig_variant = $web_image->variant('original'); ?>
		<a href="<?= $ci->website_url() ?><?= Stored_File::url_from_filename($orig_variant->filename) ?>"
			target="_blank" style="margin:0 10px 10px 0">
			<img src="<?= $ci->website_url() ?><?= Stored_File::url_from_filename($web_variant->filename) ?>" 
				width="<?= $web_variant->width ?>" height="<?= $web_variant->height ?>" /></a>
	<?php endforeach; ?>
	<br /><br />
<?php endif ?>