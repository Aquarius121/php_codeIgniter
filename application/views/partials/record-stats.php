<?php

// prevent stats on dev environment
if (!$ci->conf('stats_enabled')) 
	return;

$builder = new Stats_URI_Builder();
$builder->add_newsroom_view($ci->newsroom);

if (isset($vd->m_content) && $vd->m_content->is_published)
{
	$builder->add_content_view($ci->newsroom, $vd->m_content);
	$builder->add_network_content_view($vd->m_content);
}

?>

<img src="<?= $vd->esc($builder->build(Stats_URI_Builder::MEDIA_IMAGE)) ?>"
	width="1" height="1" class="stat-pixel" />
