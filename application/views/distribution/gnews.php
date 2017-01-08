<?= '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
				xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

	<?php foreach ($vd->results as $result): ?>
	<url>
		<loc><?= $ci->website_url($result->url()) ?></loc>
		<news:news>
			<news:publication>
				<news:name>Newswire</news:name>
				<news:language>en</news:language>
			</news:publication>
			<news:genres>PressRelease</news:genres>
			<news:publication_date><?= Date::utc($result->date_publish)->format('c') ?></news:publication_date>
			<news:title><?= $vd->esc($result->title) ?></news:title>
		</news:news>
	</url>
	<?php endforeach ?>
	
</urlset>