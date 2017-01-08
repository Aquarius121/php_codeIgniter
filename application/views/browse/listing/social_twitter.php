<?php $raw_data = $content->m_pb_social->raw_data_object() ?>
<?php if (!$raw_data) return; ?>

<div class="ln-block social-stream ln-twitter pre-loaded">
	<div class="inner feed-twitter">
		
		<span class="section-intro">
			<span class="social-type-icon"><i class="fa fa-twitter-square"></i></span>
			<a href="https://twitter.com/<?= $vd->esc($raw_data->user->screen_name) ?>/status/<?= $raw_data->id ?>"
				class="no-custom intro-posted" target="_blank">Tweeted</a> 
			<span>
				<?php $publish = Date::out($content->date_publish); ?>
				<?= Date::difference_in_words($publish); ?>
			</span>
		</span>

		<span class="section-thumb">
			<a class="thumb" href="https://www.twitter.com/<?= $vd->esc($raw_data->user->screen_name) ?>" target="_blank">
				<img alt="" src="<?= $raw_data->user->profile_image_url_https ?>">
			</a>
		</span>

		<span class="section-text">
			<span class="rss_text">
				<span class="twitter-user">
					<a href="https://www.twitter.com/<?= $vd->esc($raw_data->user->screen_name) ?>" target="_blank">
						<strong><?= $vd->esc($raw_data->user->name) ?> </strong> <br>
						@<?= $vd->esc($raw_data->user->screen_name) ?>
					</a>
				</span>
				<?= Social_Twitter_Post::parse($vd->esc(stripslashes($raw_data->text))) ?>
			</span>
		</span>

	</div>
</div>

