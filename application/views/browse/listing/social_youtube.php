<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-youtube">
	<div class="inner feed-youtube">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-youtube-square"></i></span>
			<a href="<?= $vd->esc($raw_data->link) ?>" target="_blank" class="no-custom intro-posted">
			Uploaded</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if ($raw_data->post_id): ?>
			<span class="section-thumb">
				<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>" title="<?= $vd->esc($raw_data->title) ?>">
					<img src="https://img.youtube.com/vi/<?= $raw_data->post_id ?>/mqdefault.jpg">
				</a>
			</span>
		<?php endif ?>

		<span class="section-title">
			<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>" title="<?= $vd->esc($raw_data->post_title) ?>" 
				class="rss_header_title">
				<?= $vd->esc(stripslashes($raw_data->post_title)) ?>
			</a>
		</span>

		<?php if ($raw_data->post_description_text && !is_object($raw_data->post_description_text)): ?>
			<span class="section-text">
				<?= $vd->esc($vd->cut($raw_data->post_description_text, 125)) ?>
			</span>
		<?php endif ?>

	</div>
</div>