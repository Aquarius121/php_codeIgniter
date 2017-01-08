<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-facebook">
	<div class="inner feed-facebook">

		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-facebook-square"></i></span>
			<a href="http://www.facebook.com/<?= $vd->esc($vd->nr_profile->soc_facebook) ?>/posts/<?= 
				$content->post_id ?>" target="_blank" class="no-custom intro-posted">
				Posted</a>
			<span>
				<? $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<?php if ($raw_data->name): ?>
			<span class="section-title">
				<a class="rss_header_title" target="_blank"
					href="<?= $vd->esc(Social_Facebook_Post::url($content->post_id)) ?>">
					<?= $vd->esc($vd->cut(stripslashes($raw_data->name), 84)) ?>
				</a>
			</span>
		<?php endif ?>

		<?php if ($raw_data->picture): ?>
			<span class="section-thumb">
				<a rel="nofollow" title="" target="_blank"
					href="<?= $vd->esc(Social_Facebook_Post::url($content->post_id)) ?>">
					<img alt="" src="<?= $vd->esc($raw_data->picture) ?>" />
				</a>
			</span>
		<?php endif ?>

		<span class="section-text">
			<span class="rss_text">
				<?php if ($raw_data->description): ?>
					<?= $vd->esc($raw_data->description) ?>
				<?php elseif ($raw_data->post_message): ?>
					<?= $vd->esc($raw_data->post_message) ?>
				<?php endif ?>
			</span>
		</span>

	</div>
</div>