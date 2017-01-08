<?php if (!$vd->nr_custom) return; ?>
<?php $back_image = Model_Image::find($vd->nr_custom->back_image_id); ?>

<style>

body {

	<?php if ($vd->nr_custom->back_image_repeat === 'repeat'): ?>
	background-position: top left !important;
	<?php else: ?>
	background-position: center center !important;
	<?php endif ?>

	<?php if ($vd->nr_custom->back_color): ?>
	background-color: <?= $vd->nr_custom->back_color ?> !important;
	<?php endif ?>

	<?php if ($back_image): ?>
	<?php $bi_variant = $back_image->variant('original'); ?>
	<?php $bi_url = Stored_File::url_from_filename($bi_variant->filename); ?>
	background-image: url("<?= $bi_url ?>") !important;
	<?php endif ?>

	background-repeat: <?= $vd->nr_custom->back_image_repeat ?> !important;
	background-attachment: fixed;

}

.bs3-container-back + .bs3-container {

	<?php if ($vd->nr_custom->back_color === 'transparent'): ?>
	background-color: transparent !important;
	<?php endif ?>

	<?php if ($vd->nr_custom->back_color !== 'transparent' || $back_image): ?>
	padding: 0 20px;
	<?php endif ?>

}

#content-container *:not(.no-custom) {

	<?php if ($vd->nr_custom->text_color): ?>
	color: <?= $vd->nr_custom->text_color ?> !important;
	<?php endif ?>

}

#content-container a:not(.no-custom) {

	<?php if ($vd->nr_custom->link_color): ?>
	color: <?= $vd->nr_custom->link_color ?> !important;
	<?php endif ?>

}

#content-container a:not(.no-custom):hover {

	<?php if ($vd->nr_custom->link_hover_color): ?>
	color: <?= $vd->nr_custom->link_hover_color ?> !important;
	<?php endif ?>

}

.ln-press-contact {

	<?php if ($vd->nr_custom->secondary_color): ?>
	border-color: <?= $vd->nr_custom->secondary_color ?> !important;
	<?php endif ?>

}

.ln-press-contact .ln-contact-details {

	<?php if ($vd->nr_custom->secondary_color): ?>
	background-color: <?= $vd->nr_custom->secondary_color ?> !important;
	color: #fff !important;
	<?php endif ?>

}

.ln-press-contact .ln-contact-details a {

	<?php if ($vd->nr_custom->secondary_color): ?>
	color: #fff !important;
	<?php endif ?>

}

.btn-flat-blue,
.btn-flat-blue:hover {

	<?php if ($vd->nr_custom->secondary_color): ?>
	background-color: <?= $vd->nr_custom->secondary_color ?> !important;
	<?php endif ?>
	
}

.btn-outline,
.btn-outline:active,
.btn-outline:hover {

	<?php if ($vd->nr_custom->secondary_color): ?>
	border-color: <?= $vd->nr_custom->secondary_color ?> !important;
	color: <?= $vd->nr_custom->secondary_color ?> !important;
	<?php endif ?>

}

.btn-outline:active,
.btn-outline:hover {

	<?php if ($vd->nr_custom->secondary_color): ?>
	color: #fff !important;
	<?php endif ?>

}

.links-list li.active a {

	<?php if ($vd->nr_custom->secondary_color): ?>
	background-color: <?= $vd->nr_custom->secondary_color ?> !important;
	<?php endif ?>

}

</style>
