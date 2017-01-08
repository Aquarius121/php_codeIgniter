<?php if ($content->is_pinned): ?>
	<div class="ln-featured">FEATURED</div>
<?php endif ?>

<header class="ln-block-header <?= value_if_test($content->is_pinned, 'pinned-content') ?>">
	<span class="ln-category">
		<?php if ($this->is_common_host): ?>
		<?= !empty($vd->content_type_labels->{$content->type}->singular)
				? $vd->esc($vd->content_type_labels->{$content->type}->singular)
				: Model_Content::full_type($content->type) ?>
		<?php else: ?>
			<a href="browse/<?= $content->type ?>">
				<?= !empty($vd->content_type_labels->{$content->type}->singular)
					? $vd->esc($vd->content_type_labels->{$content->type}->singular)
					: Model_Content::full_type($content->type) ?>
			</a>			
		<?php endif ?>
	</span>
	<span class="ln-date">
		<?php $dt_date_publish = Date::out($content->date_publish); ?>
		<?= $dt_date_publish->format('M j, Y') ?>
	</span>
</header>