<div style="color:#111111;padding:10px;font-size:16px;font-family:sans-serif;">
	<img src="<?= $vd->assets_base ?>im/logo.png" alt="Newswire" style="margin:10px 0">
	<br><br>Hello <?= $vd->esc($vd->contact->name()) ?>
	<br><br>This is Newswire daily summary for journalists. <br>The following content has been 
		published in an industry you have expressed interest in. 
	<?php foreach ($vd->content_results as $content): ?>
	<br><br>
	<a href="<?= $ci->website_url($content->url()) ?>"><strong><?= 
		$vd->esc($content->title) ?></strong></a>
	<br><?= $vd->esc($content->summary) ?>
	<?php endforeach ?>
	<div style="color:#999999;font-size:12px">
		<br><br>This is an automated email. 
			Please visit our website to contact us.
		<br>Click <a href="<?= $ci->website_url() ?>journalists/activate/<?= 
			$vd->subscriber->id ?>/<?= $vd->subscriber->raw_data()->secret ?>">here</a>
			to update your email preferences.
	</div>
</div>

<?php if ($vd->impressions_uri): ?>
<img src="<?= $vd->esc($vd->impressions_uri) ?>"
	width="1" height="1" class="stat-pixel" />
<?php endif ?>