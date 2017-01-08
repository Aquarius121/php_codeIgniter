<?= $this->view('email/container/header'); ?>
<div style="padding:0 20px;">

	<p>Hello <?= $vd->user->first_name ?>,</p>
	<p>This is an update from Newswire Insights (<b style="font-weight:bold;color:#9D3D3D">BETA</b>).
		<br>We've found the following new content matching your search:</p>

	<?php 

	$cssin = new CSS_Inliner();
	$cssin->set_html($this->view('cli/insights/alert-results'));
	$cssin->set_css(file_get_contents('assets/css/insights.css'));
	echo $cssin->convert();

	?>

</div>
<div style="font-size:12px;color:#999;text-align:center;margin-top:40px;line-height:normal;">
	To unsubscribe from these notifications please click the link below:<br>
	<a href="<?= $vd->esc($vd->unsubscribe_url) ?>" style="color:#78A5BC">
		<?= $vd->esc($vd->cut(URL::nice($vd->unsubscribe_url), 65)) ?>
	</a>
</div>
<?= $this->view('email/container/footer'); ?>