<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
	<sitemap><loc><?= $ci->website_url('sitemap/pages') ?></loc></sitemap>
	<sitemap><loc><?= $ci->website_url('sitemap/news_center') ?></loc></sitemap>
	<?php for ($block = 0; $block * $vd->block_size < $vd->content_count; $block++): ?>
	<sitemap><loc><?= $ci->website_url(sprintf('sitemap/content_block/%d', $block + 1)) ?></loc></sitemap>
	<?php endfor ?>
	<?php for ($block = 0; $block * $vd->block_size < $vd->newsroom_count; $block++): ?>
	<sitemap><loc><?= $ci->website_url(sprintf('sitemap/newsroom_block/%d', $block + 1)) ?></loc></sitemap>
	<?php endfor ?>
</sitemapindex>