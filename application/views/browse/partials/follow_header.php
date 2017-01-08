<div class="follow-modal-header">
	<?php $lo_im = $vd->nr_custom ? Model_Image::find($vd->nr_custom->logo_image_id) : null; ?>	
	<?php if ($lo_im) $lo_variant = $lo_im->variant('header'); ?>
	<?php if ($lo_im) $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
	<?php if ($lo_im): ?>
		<div class="ta-center">
			<img src="<?= $vd->esc($lo_url) ?>" alt="<?= $vd->esc($ci->newsroom->company_name) ?>" />
		</div>
	<?php else: ?>
		<div class="ta-left">
			<?= $vd->esc($ci->newsroom->company_name) ?>
		</div>
	<?php endif ?>

	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
		<i class="fa fa-remove"></i>
	</button>
</div>