<?php if ($result->web_video_provider && $result->web_video_id): ?>
	<?php $video = Video::get_instance($result->web_video_provider, $result->web_video_id); ?>
	<p style="font-weight: bold;">Related Video</p>
	<p>
		<a href="<?= $vd->esc($video->url()) ?>" target="_blank">
			<?= $vd->esc($video->url()) ?>
		</a>
	</p>
	<p></p>
<?php endif ?>