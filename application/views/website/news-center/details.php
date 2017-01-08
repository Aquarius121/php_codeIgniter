<div class="news-item-properties">
	<span class="ln-category"><?= Model_Content::full_type($content->type) ?></span> -
	<time class="ln-date" datetime="<?= $content->date_publish ?>">
		<?php $dt_date_publish = Date::out($content->date_publish); ?>
		<?= $dt_date_publish->format('M j, Y') ?>
	</time>
</div>