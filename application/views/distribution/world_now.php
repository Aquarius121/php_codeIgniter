<?= '<?xml version="1.0" encoding="utf-8" ?>' ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Latest Press Releases from Newswire.com</title>
		<link><?= $ci->website_url() ?></link>
		<atom:link href="<?= $ci->website_url($vd->feed_url) ?>" rel="self" type="application/rss+xml" />
		<description>Newswire.com RSS Feed</description>
		<copyright>Newswire.com All Rights Reserved.</copyright>
		<managingEditor>mike@newswire.com (Newswire.com Editor)</managingEditor>
		<image>
			<title>Latest Press Releases from Newswire.com</title>
			<url><?= $vd->assets_base ?>im/logo_275_35.png</url>
			<link><?= $ci->website_url() ?></link>
		</image>
		<?php foreach ($vd->results as $result): ?>
		<item>

			<?php $date_publish = Date::utc($result->date_publish); ?>
			
			<title><?= $vd->esc(to_utf8_fix_chars($result->title)) ?></title>
			<pubDate><?= $date_publish->format(Date::FORMAT_RFC2822) ?></pubDate>
			
			<description><![CDATA[

				<?php if ($result->summary): ?>
				<b><?= $vd->esc($result->summary) ?></b>
				<br /><br />
				<?php endif ?>
				
				<?php if ($result->c_cover_filename): ?>
				<a href="<?= $ci->website_url() ?>files/<?= $result->c_cover_full_filename ?>"
					target="_blank">
					<img src="<?= $ci->website_url() ?>files/<?= 
						$result->c_cover_filename ?>" style="max-width: 90%" />
				</a>
				<br /><br />
				<?php endif ?>

				<?= $vd->esc($result->location) ?>
				<?= $result->location ? '-' : null ?>
				<?= $date_publish->format('F j, Y') ?>
				<?php if ($result->virtual_source_id && $result->virtual_source_name): ?>
				- (<a href="<?= $vd->esc($result->virtual_source_website) ?>"><?= 
					$vd->esc($result->virtual_source_name) ?></a>)
				<?php else: ?>
				- (<a href="<?= $ci->website_url() ?>">Newswire.com</a>)
				<?php endif ?>
				<br /><?= $result->content ?><br />

				<?php if (($video_preview = Model_Video_Preview::find_or_create($result->web_video_provider, $result->web_video_id)) &&
								($video = $video_preview->video()) &&
								($video_preview_image = $video_preview->image())): ?>
				<?php if (($variant = $video_preview_image->variant('web-video-preview'))
						  && $variant->filename): ?>
				<a href="<?= $video->url() ?>" target="_blank" 
					style="max-width: 90%; display: block;">
					<img src="<?= $ci->website_url() ?><?= 
						Stored_Image::url_from_filename($variant->filename) 
						?>" style="border: none; width: 100%; display: block;" />
				</a>
				<br />
				<br />
				<?php endif ?>
				<?php endif ?>

				<?php if ($result->rel_res_pri_link || $result->rel_res_sec_link): ?>
					<strong>Related Links</strong><br />
					<?php if ($result->rel_res_pri_link) : ?>
					<?php if (!$result->rel_res_pri_title) $result->rel_res_pri_title = $result->rel_res_pri_link; ?>
						<a target="_blank" href="<?= $vd->esc($result->rel_res_pri_link) ?>"><?= 
							$vd->esc($result->rel_res_pri_title) ?></a><br />
					<?php endif; ?>
					<?php if ($result->rel_res_sec_link) : ?>
					<?php if (!$result->rel_res_sec_title) $result->rel_res_sec_title = $result->rel_res_sec_link; ?>	
						<a target="_blank" href="<?= $vd->esc($result->rel_res_sec_link) ?>"><?= 
							$vd->esc($result->rel_res_sec_title) ?></a><br />
					<?php endif ?>
					<br />
				<?php endif ?>

				<?= $ci->load->view('distribution/partials/related_files',
						array('result' => $result)); ?>
				<?= $ci->load->view('distribution/partials/related_images',
						array('result' => $result)); ?>
				
				<?php /* ?>
				<strong>Contact Info:</strong><br />
				<?php if ($result->c_logo_filename): ?>
				<img src="<?= $ci->website_url() ?>files/<?= 
					$result->c_logo_filename ?>"
					style="margin: 10px 0" /><br />
				<?php endif ?>
				<?php if ($c_website_url = URL::safe($result->c_website)): ?>
				<a href="<?= $vd->esc($c_website_url); ?>">
					<?= $vd->esc($result->c_name) ?>
				</a>
				<?php else: ?>
				<?= $vd->esc($result->c_name) ?>
				<?php endif ?>
				
				<?php if ($result->c_address_street): ?>
				<br /><?= $vd->esc($result->c_address_apt_suite) ?>
				<?= $vd->esc($result->c_address_street) ?>
				<?php endif ?>
				
				<?php if ($result->c_address_city): ?>
				<br /><?= $vd->esc($result->c_address_city) ?>
				<?php endif ?>
				
				<?php if ($result->c_address_state): ?>
				<br /><?= $vd->esc($result->c_address_state) ?>
				<?= $vd->esc($result->c_address_zip) ?>
				<?php endif ?>
				
				<?php if ($result->c_address_country): ?>
				<br /><?= $vd->esc($result->c_address_country) ?>
				<?php endif ?>
				
				<?php if ($result->c_contact_phone): ?>
				<br /><br />
				Press Contact: <br>
				<i><?= $vd->esc($result->c_contact_first_name) ?>
					<?= $vd->esc($result->c_contact_last_name) ?></i><br>
				<?= $vd->esc($result->c_contact_phone) ?>				
				<?php endif ?>
				*/ ?>
				
				<br /><br />
				Press Release Service
				by 
				<a href="<?= $ci->website_url() ?>">Newswire.com</a> <br /><br />Original Source: 
				<a href="<?= $ci->website_url($result->url()) ?>">
					<?= $vd->esc($result->title) ?>
				</a>

				<?php if ($result->tracking_uri): ?>
					<img src="<?= $vd->esc($result->tracking_uri) ?>" width="1" height="1" />
				<?php endif ?>
				
			]]></description>

			<link><?= $ci->website_url($result->url()) ?></link>
			<guid><?= $ci->website_url($result->url(false)) ?></guid>

		</item>
		<?php endforeach ?>
	</channel>
</rss>
