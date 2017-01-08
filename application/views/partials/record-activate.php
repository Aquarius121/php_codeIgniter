<?php

// prevent stats on dev environment
if (!$ci->conf('stats_enabled')) 
	return;

$rec_enc = $this->input->get('rec');
$set_enc = $this->input->get('set');

if (!$rec_enc) return;
if (!$set_enc) return;

?>

<script src="//<?= $ci->conf('stats_host') ?>/activate/js?rec=<?= $vd->esc($rec_enc) ?>&amp;set=<?= $vd->esc($set_enc) ?>"></script>
<noscript>
	<img src="//<?= $ci->conf('stats_host') ?>/activate/im?rec=<?= $vd->esc($rec_enc) ?>&amp;set=<?= $vd->esc($set_enc) ?>"
		width="1" height="1" class="stat-pixel" />
</noscript>