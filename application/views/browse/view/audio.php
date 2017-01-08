<article class="article">

	<header class="article-header">
		<h2><?= $vd->esc($vd->m_content->title) ?></h2>
	</header>

	<?php

		$render_basic = $ci->is_development();

		$loader = new Assets\CSS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/mediaelement/mediaelementplayer.css');		
		echo $loader->render($render_basic);

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/mediaelement/mediaelement-and-player.min.js');
		echo $loader->render($render_basic);

	?>

	<section class="article-details">
		
		<div class="row-fluid">
			<div class="span12">
				<?= $ci->load->view('browse/view/partials/article_info') ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php if ($vd->m_content->stored_file_id): ?>
				<div id="audio-player">
					<?php $audio = Stored_file::load_data_from_db(
					$vd->m_content->stored_file_id); ?>
					<audio src="<?php echo Stored_file::url_from_filename($audio->filename) ?>" />
				</div>
				<script>					
					$(function() {

						var audio = $("#audio-player audio");
						audio.mediaelementplayer({
							audioWidth: 540
						});
						
					});				
				</script>
				<?php endif ?>
			</div>
		</div>
		
	</section>

	<section class="article-content clearfix">
		
		<?php if ($vd->m_content->license || $vd->m_content->source): ?>
		<p class="article-details-license">
			<?php if ($vd->m_content->license): ?>
				License: <?php echo $vd->esc($vd->m_content->license) ?>
			<?php endif ?>
			<?php if ($vd->m_content->license && $vd->m_content->source): ?>
			<span>-</span>
			<?php endif	?>
			<?php if ($vd->m_content->source): ?>
				Source: <?php echo $vd->esc($vd->m_content->source) ?>
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

	<?php echo $ci->load->view('browse/view/partials/links_tags') ?>

</article>