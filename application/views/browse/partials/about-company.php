<?php if (@$vd->nr_profile->summary || @$vd->nr_profile->description
	|| @$vd->nr_profile->website): ?>

<section class="al-block aside-about-company accordian">
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		About <?= $vd->esc($ci->newsroom->company_name) ?>
	</h3>
	<div class="accordian-content aside-content">

		<?php if ($ci->is_common_host): ?>
		<?php $lo_im = Model_Image::find(@$vd->nr_custom->logo_image_id); ?>
		<?php if ($lo_im): ?>
		<?php $lo_variant = $lo_im->variant('header-sidebar'); ?>
		<?php $orig_variant = $lo_im->variant('original'); ?>
		<?php if (!$lo_variant->filename) $lo_variant = $lo_im->variant('header'); ?>
		<?php $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
		<?php $orig_url = Stored_File::url_from_filename($orig_variant->filename); ?>
		<div class="marbot-15">
			<a target="_blank" class="use-lightbox"
				href="<?= $orig_url ?>">
				<img alt="<?= $vd->esc($ci->newsroom->company_name) ?>"
					src="<?= $lo_url ?>" />
			</a>
		</div>
		<?php endif ?>
		<?php endif ?>

		<?php if (@$vd->nr_profile->website) : ?>
		<div class="aside-website marbot">
			<a href="<?= $vd->esc($vd->nr_profile->website) ?>">
				<i class="fa fa-globe"></i> Visit Website</a></div>
		<?php endif; ?>

		<?php if (@$vd->nr_profile->summary): ?>

			<p><?= nl2p($vd->esc($vd->nr_profile->summary)) ?></p>

			<?php if (!$ci->is_common_host): ?>
			<p><a href="browse/about">Learn More &#187;</a></p>
			<?php endif ?>

			<?php elseif (@$vd->nr_profile->description): ?>

			<?php $desc = strip_tags($vd->nr_profile->description); ?>
				<?php if (strlen($desc) > 1000): ?>
				<p><?= nl2p($vd->esc($vd->cut($desc, 1000))) ?> ...</p>
				<?php else: ?>
					<p><?= nl2p($vd->esc($desc)) ?></p>
				<?php endif ?>

			<?php if (!$ci->is_common_host): ?>
				<p><a href="browse/about">Learn More &#187;</a></p>
			<?php endif ?>

		<?php endif ?>
		
	</div>
</section>

<?php endif ?>