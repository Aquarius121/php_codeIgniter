<div class="ln-block">

	<?php $cover_image = Model_Image::find($content->cover_image_id); ?>
	
	<?php if ($cover_image): ?>
	<?php $ci_variant = $cover_image->variant('cover-website'); ?>
	<?php if (!$ci_variant->filename) $ci_variant = $cover_image->variant('cover'); ?>
	<?php $ci_filename = $ci_variant->filename; ?>
	<a class="ln-cover" href="<?= $content->url() ?>" 
		rel="noindex, nofollow" target="_blank">
		<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
			class="has-2x" data-url-2x="shared/resim/view-web-2x/<?= $cover_image->id ?>"
			alt="<?= $vd->esc($content->title) ?>"  />
	</a>
	<?php endif ?>
	
	<?= $ci->load->view('browse/listing/partials/details') ?>

	<a href="<?= $content->url() ?>" class="content-link" 
		target="_blank" rel="noindex, nofollow">
		<span class="ln-title">
			<?= $vd->esc(stripslashes($content->title)) ?>
		</span>
		<?php if (@$content->summary): ?>
		<span class="ln-content">
			<p><?= $vd->esc(stripslashes($content->summary)) ?></p>
		</span>
		<?php endif ?>
	</a>
	
</div>