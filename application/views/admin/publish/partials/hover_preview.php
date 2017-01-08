<div id="hover-content-data">
	<strong><?= $vd->esc($result->title) ?></strong><hr />
	<strong><?= $vd->esc($result->summary) ?></strong>
	<?php /* remove all html tags but preserve spacing between tight html */ ?>
	<?php $content = strip_tags(str_replace('</p>', '</p> ', $result->content)) ?>
	<?= $vd->cut($content, 1500) ?>
</div>