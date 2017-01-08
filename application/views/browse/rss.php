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
		
	<atom:link href="<?= $ci->env['base_url'] ?><?= 
		$vd->esc(gstring($ci->uri->uri_string)) ?>" 
		rel="self" type="application/rss+xml" />
		
	<?php foreach ($vd->results as $result): ?>
	<item>
		<title><?= $vd->esc(@$result->title) ?></title>
		<?php if ($result->type == Model_Content::TYPE_SOCIAL): ?>
			<?php $raw_data = $result->m_pb_social->raw_data() ?>
			<?php if ($result->m_pb_social->media_type == Model_PB_SOCIAL::TYPE_FACEBOOK): ?>
				<link>http://facebook.com/<?= $result->soc_facebook ?>/posts/<?= $result->m_pb_social->post_id ?></link>
				<guid>http://facebook.com/<?= $result->soc_facebook ?>/posts/<?= $result->m_pb_social->post_id ?></guid>
			<?php elseif ($result->m_pb_social->media_type == Model_PB_SOCIAL::TYPE_TWITTER): ?>
				<link>https://twitter.com/<?= @$raw_data->user->screen_name ?>/status/<?= @$raw_data->id ?></link>
				<guid>https://twitter.com/<?= @$raw_data->user->screen_name ?>/status/<?= @$raw_data->id ?></guid>
			<?php elseif ($result->m_pb_social->media_type == Model_PB_SOCIAL::TYPE_GPLUS): ?>
				<?php if (!empty($raw_data->url)): ?>
					<link><?= $raw_data->url ?></link>
					<guid><?= $raw_data->url ?></guid>
				<?php endif ?>
			<?php elseif ($result->m_pb_social->media_type == Model_PB_SOCIAL::TYPE_PINTEREST): ?>
				<?php if (!empty($raw_data->link)): ?>
					<link><?= $raw_data->link ?></link>
					<guid><?= $raw_data->link ?></guid>
				<?php endif ?>
			<?php elseif ($result->m_pb_social->media_type == Model_PB_SOCIAL::TYPE_YOUTUBE): ?>
				<?php if (!empty($raw_data->link)): ?>
					<link><?= $raw_data->link ?></link>
					<guid><?= $raw_data->link ?></guid>
				<?php endif ?>
			<?php endif ?>
		<?php elseif ($result->type == Model_Content::TYPE_BLOG): ?>
			<link><?= $result->m_pb_blog->source_url ?></link>
			<guid><?= $result->m_pb_blog->source_url ?></guid>
		<?php else: ?>
			<link><?= $ci->website_url($result->url()) ?></link>
			<guid><?= $ci->website_url($result->url()) ?></guid>
		<?php endif ?>
		<description><?= $vd->esc(@$result->summary) ?></description>
	</item>
	<?php endforeach ?>
	
</channel>
</rss>