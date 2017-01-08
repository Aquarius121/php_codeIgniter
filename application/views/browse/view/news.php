<article class="article">

	<header class="article-header">
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<section class="article-content clearfix">
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
		<?= $ci->load->view('browse/view/partials/supporting_quote',
			array('m_content' => $vd->m_content)) ?>
		<div class="marbot-15 html-content">
			<?= $ci->load->view('browse/view/html-content') ?>
			<?php if ($vd->m_content->source_url): ?>
			<p>Source: <a href="<?= $vd->esc($vd->m_content->source_url) ?>"><?= 
				$vd->esc($vd->m_content->title) ?></a></p>
			<?php endif ?>
		</div>
		<?= $ci->load->view('browse/view/partials/share-bottom') ?>
	</section>

	<?= $ci->load->view('browse/view/partials/additional_images') ?>
	<?= $ci->load->view('browse/view/partials/additional_links') ?>
	<?= $ci->load->view('browse/view/partials/tags_categories') ?>

</article>