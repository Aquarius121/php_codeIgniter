<?= '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
	<?php foreach ($this->vd->urls as $url): ?>
	<url>
		<loc><?= $vd->esc($url[0]) ?></loc>
		<?php if (isset($url[1]) && $url[1]): ?>
		<changefreq><?= $vd->esc($url[1]) ?></changefreq>
		<?php endif ?>
		<?php if (isset($url[2]) && $url[2] instanceof DateTime): ?>
		<lastmod><?= Date::utc($url[2])->format('Y-m-d\TH:iP') ?></lastmod>
		<?php endif ?>		
	</url>
	<?php endforeach ?>
</urlset>