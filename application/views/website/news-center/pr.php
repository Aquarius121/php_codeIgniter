<div class="news-item col-xs-3">
	
	<div class="ni-container">
		<?php $cover_image = Model_Image::find($content->cover_image_id); ?>
		
		<?php if ($cover_image): ?>
		<?php $ci_variant = $cover_image->variant('cover-website'); ?>
		<?php if (!$ci_variant->filename) $ci_variant = $cover_image->variant('cover'); ?>
		<?php $ci_filename = $ci_variant->filename; ?>
		<a class="ni-cover" href="<?= $content->url() ?>">
			<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
				alt="<?= $vd->esc($content->title) ?>" 
				width="<?= $ci_variant->width ?>" 
				height="<?= $ci_variant->height ?>" />
		</a>
		<?php endif ?>
		
		<div class="news-item-body">
			<?= $ci->load->view('website/news-center/details') ?>		
			<a class="content-link" href="<?= $content->url() ?>"><h3><?= $vd->esc($content->title) ?></h3></a>
			<?php if (@$content->summary): ?>
			<div class="ln-content">
				<p><?= nl2p($vd->esc($content->summary)) ?></p>
			</div>
			<?php endif ?>
		</div>
	</div>
	
</div>