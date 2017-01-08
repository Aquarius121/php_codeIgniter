<article class="article">

	<header class="article-header">
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<section class="article-details">
		<div class="row-fluid">
			<div class="span12">
				<?= $ci->load->view('browse/view/partials/article_info') ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">				
				<?php if ($vd->m_content->external_video_id): ?>
					<?php $provider = Video::get_instance(
						$vd->m_content->external_provider, 
						$vd->m_content->external_video_id); ?>
					<span class="media-block">
						<?= $provider->render(540, 304) ?>
					</span>
				<?php endif ?>
			</div>
		</div>
	</section>

	<section class="article-content clearfix">
		
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