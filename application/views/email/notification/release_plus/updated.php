The following content has been updated and may require (re)submission to <b><?= $vd->esc($plus->name()) ?></b>. 
Please check the press release and submit to (or update) <b><?= $vd->esc($plus->name()) ?></b>.

<br><br>
<code>
	bundle: <?= $vd->esc($content->distribution_bundle()->name()) ?>
	<br>
	view: 
	<a href="<?= $ci->website_url($content->url()) ?>">
		<?= $ci->website_url($content->url()) ?>
	</a>
	<br>
	edit: 
	<a href="<?= $newsroom->url("manage/publish/{$content->type}/edit/{$content->id}") ?>">
		<?= $newsroom->url("manage/publish/{$content->type}/edit/{$content->id}") ?>
	</a>
	<br>
	admin: 
	<a href="<?= $ci->website_url("admin/publish/pr/all?filter_search={$content->id}") ?>">
		<?= $ci->website_url("admin/publish/pr/all?filter_search={$content->id}") ?>
	</a>
</code>

<?php if ($comment): ?>
<br><br>
The user made the following comment about the updates:
<br><br>
<code><?= nl2br($vd->esc($comment)) ?></code>
<?php endif ?>

<?= $this->load->view('email/notification/release_plus/partials/distribution',
	array('mContent' => $content)) ?>