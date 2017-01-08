<?php $images = $result->get_images(); ?>
<?php if (count($images) > 1 || (count($images) && $images[0]->id != $result->cover_image_id)): ?>
	<?php $img_cnt = 1; ?>
	<p style="font-weight: bold;">Related Images</p>
	<?php foreach ($images as $image): ?>
		<?php $web_image = Model_Image::find($image->id); ?>
		<?php $orig_variant = $web_image->variant('original'); ?>
		<p><a href="<?= $ci->website_url(Stored_File::url_from_filename($orig_variant->filename)) ?>" target="_blank">
			<?= sprintf('image%d.%s', $img_cnt++, Stored_File::parse_extension($orig_variant->filename)); ?> 
		</a></p>
	<?php endforeach ?>
<?php endif ?>