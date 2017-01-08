The following content has been submitted and might require submission to <b><?= $vd->esc($plus->name()) ?></b>.
Please review the press release and then approve or reject it. <br>
<br>
Once approved, the content will need to be distributed at <b><?= $vd->esc($plus->name()) ?></b>.
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
</code>

<?= $this->load->view('email/notification/release_plus/partials/distribution',
	array('mContent' => $content)) ?>