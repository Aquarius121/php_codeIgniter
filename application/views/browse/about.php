<div class="main-content">
	<section class="content-view">
		
		<header class="cv-header"></header>

		<div id="cv-container">
			
			<div class="inner-content">

				<?php $lo_im = Model_Image::find(@$vd->nr_custom->logo_image_id); ?>
				<?php if ($lo_im): ?>
				<?php $orig_variant = $lo_im->variant('original'); ?>
				<?php $lo_variant = $lo_im->variant('header'); ?>
				<?php $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
				<?php $orig_url = Stored_File::url_from_filename($orig_variant->filename); ?>
				<div class="marbot-20">
					<h2 class="company-about-h2">
						<a target="_blank" class="use-lightbox"
							href="<?= $orig_url ?>">
							<img alt="<?= $vd->esc($ci->newsroom->company_name) ?>"
								src="<?= $lo_url ?>" />
						</a>
						<div class="company-inline-block">
							<?= $vd->esc($ci->newsroom->company_name) ?><br />
							<span class="company-top"> 
								<?php if (@$vd->nr_profile->website): ?>
									<span class="company-top-item"><i class="fa fa-globe"></i> <a href="<?= 
										$vd->esc($vd->nr_profile->website) ?>">Visit Website</a></span>
								<?php endif; ?>
								<?php if (@$vd->nr_profile->email): ?>
									<span class="company-top-item"><i class="fa fa-envelope-alt"></i> <a class="email-obfuscated"
										href="mailto:<?= $vd->esc(strrev($vd->nr_profile->email)) ?>">Email</a></span>
								<?php endif ?>
							</span>
						</div>
					</h2>
				</div>
				<?php endif ?>
				
				<?php if (@$vd->nr_profile->beat_id): ?>
				<p class="company-details-info marbot">
					<?php $beat = Model_Beat::find($vd->nr_profile->beat_id); ?>
					Industry: <strong><?= $vd->esc($beat->name) ?></strong>
				</p>
				<?php endif ?>
				
				<?php if (@$vd->nr_profile->year): ?>
				<p class="company-details-info marbot">
					Founded: <strong><?= $vd->nr_profile->year ?></strong>
				</p>
				<?php endif ?>

				<?php if (@$vd->nr_profile->description) : ?>
				<div class="html-content">
					<?= $vd->nr_profile->description ?>
				</div>
				<?php endif ?>

			</div>
			
		</div>
		
	</section>	
</div>
