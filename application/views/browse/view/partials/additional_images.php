<?php 

$images = $vd->m_content->get_images();
if (count($images) == 1 && $images[0]->id == $vd->m_content->cover_image_id)
	return;
	
?>

<?php if ($images): ?>
<section class="resources-block view-web-images-section">
	<h3>
		Additional <strong>Images</strong>
	</h3>
	<div class="view-web-images clearfix">
		<?php foreach ($images as $image): ?>
			<?php $web_variant = $image->variant('view-web'); ?>
			<?php $orig_variant = $image->variant('original'); ?>
			<?php $meta = $image->raw_data_object('meta_data'); ?>
			<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>" 
				target="_blank" class="use-lightbox content-media" data-caption="<?= $vd->esc($meta->caption) ?>"
				title="<?= $vd->esc($meta->alt) ?>">		
				<img src="<?= Stored_File::url_from_filename($web_variant->filename) ?>" 
					alt="<?= $vd->esc($meta->alt) ?>" class="has-2x"
					data-url-2x="shared/resim/view-web-2x/<?= $image->id ?>"
					width="<?= $web_variant->width ?>"
					height="<?= $web_variant->height ?>" />
			</a>
		<?php endforeach ?>
	</div>
</section>
<?php endif; ?>