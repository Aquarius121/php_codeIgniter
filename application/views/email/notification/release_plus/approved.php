The following content has been approved and requires submission to <b><?= $vd->esc($plus->name()) ?></b>.
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
</code>

<?= $this->load->view('email/notification/release_plus/partials/distribution',
	array('mContent' => $content)) ?>