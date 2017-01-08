<strong class="date-line">
	<?php if ($m_content->location): ?>
		<?= $vd->esc($m_content->location) ?>, 
	<?php endif ?>
	<?php $dt_date_publish = Date::out($m_content->date_publish); ?>
	<?= $dt_date_publish->format('F j, Y') ?>
	<?php if ($m_content->owner() && 
		$m_content->owner()->is_virtual()): ?>
		<?php $source = $m_content->owner()->virtual_source(); ?>
		(<?= $vd->esc($source->name) ?>) -
	<?php elseif ($m_content->is_scraped_content && 
			$m_content->type == Model_Content::TYPE_PR): ?>
		(Press Release) -
	<?php else: ?>
		(Newswire.com) -
	<?php endif ?>
</strong>