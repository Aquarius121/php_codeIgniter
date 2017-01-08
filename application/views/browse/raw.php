<!doctype html>
<html lang="en">
	<head>
		
		<title>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> |
			<?php endif ?>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
			<?php if ($vd->nr_custom && $vd->nr_custom->headline): ?>
			<?= $vd->esc($vd->nr_custom->headline) ?>
			<?php else: ?>
			<?= $vd->esc($ci->newsroom->company_name) ?>
			<?php endif ?>
		</title>
		
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
		<base href="<?= $ci->env['base_url'] ?>" />
		
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />

		<?php

			$render_basic = $ci->is_development();

			$loader = new Assets\CSS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/bootstrap/css/bootstrap.min.css');
			$loader->add('css/base.css');
			$loader->add('css/browse.css');
			$loader->add('css/raw.css');
			echo $loader->render($render_basic);

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/jquery.js');
			$loader->add('lib/jquery.create.js');
			$loader->add('lib/imagesloaded.min.js');			
			echo $loader->render($render_basic);

		?>
		
		<link rel="canonical" href="<?= $ci->env['base_url'] ?><?= $vd->m_content->url() ?>" />
		
	</head>
	
	<body>
		
		<div id="cv-container" class="content-type-<?= $vd->m_content->type ?> wide-view">
			
			<article class="article">

				<header class="article-header">
					<?php $lo_im = Model_Image::find(@$vd->nr_custom->logo_image_id); ?>
					<div class="org-header-text clearfix marbot-20">						
						<?php if ($lo_im): ?>
						<?php $lo_variant = $lo_im->variant('header-sidebar'); ?>
						<?php $lo_url = Stored_File::url_from_filename($lo_variant->filename); ?>
						<a href="<?= $vd->esc(@$vd->nr_profile->website) ?>" class="raw-company-logo fl-left">
							<img src="<?= $lo_url ?>" alt="<?= $vd->esc($ci->newsroom->company_name) ?>" class="has-2x"
								data-url-2x="shared/resim/header-sidebar-2x/<?= $lo_im->id ?>"
								width="<?= $lo_variant->width ?>"
								height="<?= $lo_variant->height ?>" />
						</a>
						<?php endif ?>
						<span>
							<span class="prefix">
								an official press release
							</span>
							<br />
							<h1>								
								<a href="<?= $vd->esc(@$vd->nr_profile->website) ?>">
									<?= $vd->esc($ci->newsroom->company_name) ?>
								</a>
							</h1>
						</span>
					</div>
					<hr class="dashed" />
					<h2><?= $vd->esc($vd->m_content->title) ?></h2>
				</header>

				<section class="article-content">
					<?php $cover_image = Model_Image::find($vd->m_content->cover_image_id); ?>
					<?php if ($cover_image): ?>
						<?php $orig_variant = $cover_image->variant('original'); ?>
						<?php $ci_variant = $cover_image->variant('view-cover'); ?>
						<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
							class="use-lightbox floated-featured-image">
							<img src="<?= Stored_File::url_from_filename($ci_variant->filename) ?>" 
								alt="<?= $vd->esc($vd->m_content->title) ?>" class="add-border has-2x"									
								data-url-2x="shared/resim/view-cover-2x/<?= $cover_image->id ?>"
								width="<?= $ci_variant->width ?>"
								height="<?= $ci_variant->height ?>" />
						</a>
					<?php endif ?>
					<?= $ci->load->view('browse/view/partials/article_info') ?>
					<p class="article-summary"><?= $vd->esc($vd->m_content->summary) ?></p>
					<div class="marbot-15 html-content">
						<?php if ($vd->m_content->type == Model_Content::TYPE_PR): ?>
							<?= $ci->load->view('browse/view/html-content-pr', array('raw' => true)) ?>
						<?php else: ?>
							<?= $ci->load->view('browse/view/html-content') ?>
						<?php endif ?>
						<?php if ($vd->m_content->source): ?>
						<p>Source: <?= $vd->esc($vd->m_content->source) ?></p>
						<?php endif ?>
					</div>
				</section>
				
				<?php if ($vd->m_content->is_premium): ?>
				<div class="page-break-avoid">
					<?= $ci->load->view('browse/view/partials/related_resources') ?>
				</div>
				<?php endif ?>

				<div class="page-break-avoid">
					<?= $ci->load->view('browse/view/partials/additional_images') ?>
				</div>
				
				<div class="page-break-avoid">
					<?= $ci->load->view('browse/view/partials/tags_categories') ?>
				</div>
				
				<div class="view-original-source page-break-avoid">
					<hr />Original Source: <a href="<?= $ci->website_url($vd->m_content->url()) ?>">
						<?= $ci->conf('website_host') ?></a>
				</div> 

			</article>
			
		</div>		
		
		<script>
		
		$(function() {
			
			var base = $("base").attr("href");
			
			$("a").each(function() {
				var _this = $(this);
				var href = _this.attr("href")
				if (href.indexOf(":") < 0)
					_this.attr("href", base + href);
			});
			
			$(".has-2x").each(function() {
				var _this = $(this);
				var url = _this.data("url-2x");
				_this.attr("src", url);
			});
			
		});
		
		</script>
		
	</body>
</html>