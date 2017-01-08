<div class="article-info">
	<?php if ($ci->is_common_host): ?>
	<span class="ai-category"><?= 
		!empty($vd->content_type_labels->{$vd->m_content->type}->singular)
			? $vd->esc($vd->content_type_labels->{$vd->m_content->type}->singular)
			: Model_Content::full_type($vd->m_content->type) ?></span>
	<span class="dash">-</span>
	<?php else: ?>
	<a href="browse/<?= $vd->m_content->type ?>">
		<span class="ai-category"><?= 
			!empty($vd->content_type_labels->{$vd->m_content->type}->singular)
				? $vd->esc($vd->content_type_labels->{$vd->m_content->type}->singular)
				: Model_Content::full_type($vd->m_content->type) ?></span>
	</a>
	<span class="dash">-</span>
	<?php endif ?>	
	<span class="ai-date">
		<?php $dt_date_publish = Date::out($vd->m_content->date_publish); ?>
		<?php $dt_date_updated = Date::out($vd->m_content->date_updated); ?>
		<?php if ($dt_date_updated > $dt_date_publish && $vd->m_content->is_published): ?>
			<span class="status-true">
				<?php if ($dt_date_updated > Date::days(-2) && 
					$dt_date_updated->format('H:i') != '00:00'): ?>
					updated: <?= $dt_date_updated->format('M j, Y H:i T') ?>
				<?php else: ?>
					updated: <?= $dt_date_updated->format('M j, Y') ?>
				<?php endif ?>
			</span>
		<?php else: ?>			
			<?php if ($dt_date_publish > Date::days(-2) && 
				$dt_date_publish->format('H:i') != '00:00'): ?>
			<?= $dt_date_publish->format('M j, Y H:i T') ?>
			<?php else: ?>
			<?= $dt_date_publish->format('M j, Y') ?>
			<?php endif ?>
		<?php endif ?>		
	</span>
</div>