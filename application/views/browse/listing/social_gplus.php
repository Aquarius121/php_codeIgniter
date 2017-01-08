<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-google">
	<div class="inner feed-google">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-google-plus-square"></i></span>
			<a href="<?= $vd->esc($raw_data->url) ?>" target="_blank" class="no-custom intro-posted" >
			Posted</a> 
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if (!empty($raw_data->object->attachments[0]->image->url)): ?>
			<span class="section-thumb">
				<a target="_blank" href="<?= $raw_data->url ?>">
					<img src="<?= $vd->esc($raw_data->object->attachments[0]->image->url) ?>">
				</a>
			</span>
		<?php endif ?>
		
		<?php if ($raw_data->post_title): ?>
			<span class="section-text">
				<a class="rss_text" target="_blank"
					href="<?= $raw_data->url ?>">
					<?= $vd->esc($vd->cut($raw_data->post_title, 104)) ?>
				</a>
			</span>
		<?php endif ?>

	</div>
</div>