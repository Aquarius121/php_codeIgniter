<div class="ln-block <?= value_if_test($content->is_pinned, 'pinned') ?>">
		
	<?php $cover_image = Model_Image::find($content->image_id); ?>
	
	<?php if ($cover_image): ?>
	<?php $ci_variant = $cover_image->variant('cover-website'); ?>
	<?php if (!$ci_variant->filename) $ci_variant = $cover_image->variant('cover'); ?>
	<?php $ci_filename = $ci_variant->filename; ?>
	<a class="ln-cover" href="view/<?= $content->slug ?>">
		<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
			class="has-2x" data-url-2x="shared/resim/view-web-2x/<?= $cover_image->id ?>"
			alt="<?= $vd->esc($content->title) ?>" />
	</a>
	<?php endif ?>
	
	<?= $ci->load->view('browse/listing/partials/details') ?>

	<a href="view/<?= $content->slug ?>" class="content-link">
		<span class="ln-title">
			<?= $vd->esc($content->title) ?>
		</span>
		<?php if (@$content->summary): ?>
		<span class="ln-content">
			<p><?= nl2p($vd->esc($content->summary)) ?></p>
		</span>
		<?php endif ?>
	</a>
	
</div>