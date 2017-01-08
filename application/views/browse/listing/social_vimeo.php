<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-vimeo">
	<div class="inner feed-vimeo">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-vimeo-square"></i></span>
			<a href="<?= $vd->esc($raw_data->link) ?>" target="_blank" class="no-custom intro-posted">
			Uploaded</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if ($raw_data->post_id): ?>
			<span class="section-thumb">
				<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>" title="<?= $vd->esc(@$content->title) ?>">
					<img src="<?= $raw_data->pictures->sizes[2]->link ?>">
				</a>
			</span>
		<?php endif ?>

		<div class="section-title">
			<a target="_blank" href="<?= $vd->esc($raw_data->link) ?>" title="<?= $vd->esc(@$content->title) ?>" 
				class="rss_header_title">
				<?= $vd->esc(stripslashes(@$content->title)) ?>
			</a>
		</div>
		
		<?php if ($raw_data->description && !is_object($raw_data->description)): ?>
			<span class="section-text">
				<?= $vd->esc($vd->cut($raw_data->description, 125)) ?>
			</span>
		<?php endif ?>

	</div>
</div>