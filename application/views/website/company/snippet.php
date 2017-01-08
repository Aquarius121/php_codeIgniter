<div class="news-item news-item-company col-xs-3">
	<div class="ni-container">
		<div class="news-item-body pad-20 company-list-body">
				
			<div class="section-thumb">

				<?php $cover_image = Model_Image::find($company->logo_image_id); ?>

				<?php if ($cover_image): ?>
				<?php $ci_variant = $cover_image->variant('cover-website'); ?>
				<?php if (!$ci_variant->filename) $ci_variant = $cover_image->variant('header'); ?>
				<?php if (!$ci_variant->filename) $ci_variant = $cover_image->variant('original'); ?>
				<?php $ci_filename = $ci_variant->filename; ?>
				<a class="thumb" href="<?= $company->url() ?>">
					<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
						alt="<?= $vd->esc($company->title) ?>"  />
				</a>
				<?php endif ?>

			</div>

			<div class="company-name-h">
				<a class="company-link" href="<?= $company->url() ?>">
					<h3><?= $vd->esc($company->company_name) ?></h3>
				</a>
			</div>				
				
			<div class="ln-company clear">
				<p><?= $vd->esc($vd->cut(HTML2Text::plain(@$company->profile->summary), 120)) ?></p>
			</div>
			
			<?php if (@$company->beat_name): ?>
				<div class="marbot-5 clear"></div>
				<div class="ln-industry">
					<strong>Industry: </strong>
					<?= $company->beat_name ?>
				</div>
			<?php endif ?>
			<div class="marbot-5 clear"></div>

			<div id="social-icons">
				<?php if ($company->profile && $company->profile->is_twitter_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/twitter') ?>">
					<i class="fa fa-twitter-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile && $company->profile->is_facebook_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/facebook') ?>">
					<i class="fa fa-facebook-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile && $company->profile->is_gplus_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/google') ?>">
					<i class="fa fa-google-plus-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile && $company->profile->is_pinterest_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/pinterest') ?>">
					<i class="fa fa-pinterest-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile && $company->profile->is_youtube_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/youtube') ?>">
					<i class="fa fa-youtube-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile->is_vimeo_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/vimeo') ?>">
					<i class="fa fa-vimeo-square"></i></a>
				<?php endif ?>

				<?php if ($company->profile->is_instagram_feed_valid()): ?>
				<a href="<?= $company->url('browse/social/instagram') ?>">
					<i class="fa fa-instagram"></i></a>
				<?php endif ?>

				<?php if ($company->profile->soc_linkedin): ?>
				<a href="<?= $company->url('browse/social') ?>">
					<i class="fa fa-linkedin-square"></i></a>
				<?php endif ?>

			</div>
			
		</div>
	</div>
	
</div>