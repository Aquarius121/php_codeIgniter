<article class="article">

	<header class="article-header">
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<section class="article-content clearfix">
		<?php $image = Model_Image::find($vd->m_content->image_id); ?>
		<?php if (!$image) $image = new Model_Image(); ?>
		<?php $meta = $image->raw_data_object('meta_data'); ?>
		<?php $orig_variant = $image->variant('original'); ?>
		<?php $full_variant = $image->variant('view-full'); ?>
		<?php if ($full_variant->filename): ?>
			<?php if ((int) $vd->m_content->date_publish != 0): ?>
				<?= $ci->load->view('browse/view/partials/article_info') ?>
			<?php endif ?>
			<div class="featured-image marbot">
				<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
					class="use-lightbox" data-caption="<?= $vd->esc($meta->caption) ?>">
					<img src="<?= Stored_File::url_from_filename($full_variant->filename) ?>" 
						alt="<?= $vd->esc($vd->m_content->title) ?>" />
				</a>
			</div>
		<?php elseif (($wc_variant = $image->variant('view-cover'))
					  && $wc_variant->filename): ?>
			<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
				class="use-lightbox floated-featured-image marbot"
				data-caption="<?= $vd->esc(@$meta->caption) ?>">
				<img src="<?= Stored_File::url_from_filename($wc_variant->filename) ?>" 
					alt="<?= $vd->esc($vd->m_content->title) ?>" />
			</a>
			<?= $ci->load->view('browse/view/partials/article_info') ?>
		<?php else: ?>
			<?= $ci->load->view('browse/view/partials/article_info') ?>
		<?php endif ?>

		<?php if ($vd->m_content->license || $vd->m_content->source): ?>
		<p class="article-details-license">
			<?php if ($vd->m_content->license): ?>
				License: <?= $vd->esc($vd->m_content->license) ?>
			<?php endif ?>
			<?php if ($vd->m_content->license && $vd->m_content->source): ?>
			<span>-</span>
			<?php endif	?>
			<?php if ($vd->m_content->source): ?>
				Source: <?= $vd->esc($vd->m_content->source) ?>
			<?php endif ?>
		</p>
		<?php endif ?>
	
		<?= $ci->load->view('browse/view/partials/supporting_quote',
			array('m_content' => $vd->m_content)) ?>
		
		<?php if ($vd->m_content->summary): ?>
		<p class="article-summary"><?= $vd->esc($vd->m_content->summary) ?></p>
		<?php endif ?>
		
		<?= $ci->load->view('browse/view/partials/share-bottom') ?>
		
	</section>

	<?= $ci->load->view('browse/view/partials/links_tags') ?>

</article>