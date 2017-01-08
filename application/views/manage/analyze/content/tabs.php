<?php if ($vd->is_search): ?>
<div class="marbot-20"></div>
<?php else: ?>
<div class="panel-heading">

	<ul class="nav nav-tabs nav-activate tab-links ax-loadable" 
		data-ax-elements="#ax-chunkination, #ax-tab-content" id="tabs">
		<li>
			<a data-on="^manage/analyze/content/pr/published" data-toggle="link"
				href="<?= gstring('manage/analyze/content/pr/published') ?>">
				Press Releases
			</a>
		</li>
		<li>
			<a data-on="^manage/analyze/content/news/published" data-toggle="link"
				href="<?= gstring('manage/analyze/content/news/published') ?>">
				News
			</a>
		</li>
		<li>
			<a data-on="^manage/analyze/content/event/published" data-toggle="link" 
				href="<?= gstring('manage/analyze/content/event/published') ?>">
				Events
			</a>
		</li>
		<li>
			<a data-on="^manage/analyze/content/image/published" data-toggle="link" 
				href="<?= gstring('manage/analyze/content/image/published') ?>">
				Images
			</a>
		</li>
		<li>
			<a data-on="^manage/analyze/content/audio/published" data-toggle="link"
				href="<?= gstring('manage/analyze/content/audio/published') ?>">
				Audio
			</a>
		</li>
		<li>
			<a data-on="^manage/analyze/content/video/published" data-toggle="link" 
				href="<?= gstring('manage/analyze/content/video/published') ?>">
				Video
			</a>
		</li>
	</ul>
</div>
<?php endif ?>