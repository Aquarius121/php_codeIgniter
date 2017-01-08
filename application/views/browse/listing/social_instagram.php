<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-instagram">
	<div class="inner feed-instagram">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-instagram"></i></span>
			<a href="<?= $vd->esc($raw_data->link) ?>" target="_blank"  class="no-custom intro-posted">
			Shared</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if (!empty($raw_data->images->low_resolution->url)): ?>
			<div class="section-thumb <?= value_if_test($raw_data->type == 'video', 'video') ?>">
				<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>">
					<img src="<?= $vd->esc($raw_data->images->low_resolution->url) ?>">
				</a>
			</div>
		<?php endif ?>
		
		<?php if ($content->title): ?>
			<span class="section-text">
				<?= $vd->esc($vd->cut($content->title, 125)) ?>
			</span>
		<?php endif ?>

	</div>
</div>