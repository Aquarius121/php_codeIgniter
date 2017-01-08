<article class="article">

	<header class="article-header">
		<?php if ($vd->m_content->is_advert_supported()): ?>
			<?= $ci->load->view_html('partials/google-adverts/728-15') ?>
		<?php endif ?>
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<section class="article-content">
		<?php $cover_image = Model_Image::find($vd->m_content->cover_image_id); ?>
		<?php if ($cover_image): ?>
			<?php $orig_variant = $cover_image->variant('original'); ?>
			<?php $ci_variant = $cover_image->variant('view-cover'); ?>
			<?php $ci_filename = $ci_variant->filename; ?>
			<?php $meta = $cover_image->raw_data_object('meta_data'); ?>
			<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
				class="use-lightbox floated-featured-image"
				data-caption="<?= $vd->esc($meta->caption) ?>"
				title="<?= $vd->esc($meta->alt) ?>">
				<img src="<?= Stored_File::url_from_filename($ci_filename) ?>" 
					alt="<?= $vd->esc($meta->alt) ?>"
					class="add-border" />
			</a>
		<?php endif ?>
		<?= $ci->load->view('browse/view/partials/article_info') ?>
		<?php if ($vd->m_content->summary): ?>
		<p class="article-summary"><?= $vd->esc($vd->m_content->summary) ?></p>
		<?php endif ?>
		<?php if ($vd->m_content->is_advert_supported()): ?>
			<div class="advert-row-300-250">
				<?= $ci->load->view_html('partials/google-adverts/300-250') ?>
				<?= $ci->load->view_html('partials/google-adverts/300-250') ?>
			</div>
		<?php endif ?>		
		<div class="marbot-15 html-content">
			<?= $ci->load->view('browse/view/html-content-pr') ?>			
			<?php if ($vd->m_content->is_scraped_content && !empty($vd->scraped_content_url)): ?>
				<p>Source URL: <a href="<?= $vd->esc($vd->scraped_content_url) ?>" 
					target="_blank" rel="noindex, nofollow"><?= $vd->esc($vd->scraped_content_url) ?></a></p>
			<?php endif ?>
			<?php if ($vd->m_content->source): ?>
				<p>Source: <?= $vd->esc($vd->m_content->source) ?></p>
			<?php endif ?>
		</div>
		<?php if ($vd->m_content->is_advert_supported()): ?>
			<?= $ci->load->view_html('partials/google-adverts/728-90') ?>
		<?php endif ?>
		<?= $ci->load->view('browse/view/partials/share-bottom') ?>
		<?php if ($vd->m_content->is_premium && $vd->m_content->web_video_id) : ?>
			<?php $video = Video::get_instance($vd->m_content->web_video_provider, $vd->m_content->web_video_id); ?>
			<span class="media-block pull-left clearfix">
				<?= $video->render(700,394) ?>
			</span>
		<?php endif ?>
	</section>

	<?php if ($vd->m_content->is_premium): ?>
	<?= $ci->load->view('browse/view/partials/related_resources') ?>
	<?php endif ?>
	
	<?= $ci->load->view('browse/view/partials/additional_images') ?>
	
	<?php if ($vd->m_content->is_advert_supported()): ?>
		<?= $ci->load->view_html('partials/google-adverts/728-15') ?>
	<?php endif ?>
	
	<?= $ci->load->view('browse/view/partials/tags_categories') ?>

</article>
