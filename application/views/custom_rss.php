<?= '<?xml version="1.0" encoding="utf-8" ?>' ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>

	<title>
		<?php if (isset($ci->title) && $ci->title): ?>
			<?= $vd->esc($ci->title) ?> |
		<?php endif ?>
		<?php foreach(array_reverse($vd->title) as $title): ?>
			<?= $vd->esc($title) ?> |
		<?php endforeach ?>
		<?php if ($ci->is_common_host): ?>
		Newswire
		<?php else: ?>
		<?= $vd->esc($ci->newsroom->company_name) ?>
		<?php endif ?>
	</title>
	<link><?= $ci->newsroom->url(null, true) ?></link>
	<description>Latest news from <?= 
		$vd->esc($ci->newsroom->company_name) ?></description>
	<pubDate><?= date('D, d M Y') ?></pubDate>
	<?php if ($vd->rss_feed->is_show_inews_logo): ?>
	<image>
		<title>Latest Press Releases and News From Newswire.com</title>
		<url><?= $vd->assets_base ?>im/logo.png</url>
		<link><?= $ci->website_url() ?></link>
	</image>
	<?php endif ?>

	<atom:link href="<?= $ci->config->item('base_url') ?><?= 
		$vd->esc(gstring($ci->uri->uri_string)) ?>" 
		rel="self" type="application/rss+xml" />

	<?php foreach ($vd->results as $result): ?>
	<item>
		<title><?= $vd->esc(@$result->title) ?></title>
        <pubDate>
			<?php $dt = Date::out(@$result->date_publish); ?>
			<?= $dt->format('D, M j, Y H:i') ?>
        </pubDate>
		<link><?= $ci->website_url($result->url()) ?></link>
		<guid><?= $ci->website_url($result->url()) ?></guid>
		<description><![CDATA[

			<?php if ($vd->rss_feed->is_show_publish_date): ?>
				<?php $dt = Date::out($result->date_publish); ?>
				<?= $dt->format('M j, Y') ?>
				<span>-</span>
			<?php endif ?>
			(<a href="<?= $ci->website_url() ?>"><?= $vd->rss_feed->inews_link_text
				? $vd->rss_feed->inews_link_text
				: 'Newswire' ?></a>)

			<?php if ($vd->rss_feed->is_show_logo && ! empty($result->newsroom_custom->logo_image_id)): ?>
				<?php $lo_im = Model_Image::find($result->newsroom_custom->logo_image_id); ?>
				<?php $lo_variant = $lo_im->variant('header-thumb'); ?>
				<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
				<img align="left" style="float: left; width: auto; margin: 0 8px 5px 0;" 
					src="<?= $ci->website_url($lo_url) ?>" />
			<?php endif ?>

			<?= $result->content ?>
			<?= $result->footer_text ?>

			<?php if ($vd->rss_feed->is_show_related_images): ?>
				<?php $images = $result->get_images() ?>
				<?php if (count($images)): ?>
					<div><strong>Related Images:</strong></div>
					<?php foreach ($images as $image): ?>
						<?php $im_variant = $image->variant('web') ?>
						<?php $im_variant_file = $im_variant->filename; ?>
						<?php $im_variant_url = Stored_Image::url_from_filename($im_variant_file) ?>                        
						<img src="<?= $ci->website_url($im_variant_url) ?>" />
					<?php endforeach ?>
				<?php endif ?>
			<?php endif ?>

			<?php if ($vd->rss_feed->is_include_contact_info): ?>

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
				<i><?= $vd->esc($result->c_contact_name) ?></i><br>
				<?= $vd->esc($result->c_contact_phone) ?>				
				<?php endif ?>
				
			<?php endif ?>

			<?php if ($result->tracking_url): ?>
				<img src="<?= $vd->esc($result->tracking_url) ?>"
					width="1" height="1" />
			<?php endif ?>

		]]></description>
	</item>
	<?php endforeach ?>
	
</channel>
</rss>