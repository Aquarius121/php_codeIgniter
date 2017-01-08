<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" />

<div id="report-header">
	<?php if ($vd->logo_image_id): ?>
	<?php $lo_im = Model_Image::find($vd->logo_image_id); ?>
	<?php $lo_variant = $lo_im->variant('header-thumb'); ?>
	<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
	<img id="logo-image-thumb" src="<?= $lo_url ?>" />    
	<?php endif ?>
	<div></div>
</div>