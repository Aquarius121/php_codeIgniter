<div style="color:#111111;padding:10px;font-size:16px;font-family:sans-serif;">
	<img src="<?= $vd->assets_base ?>im/logo.png" alt="Newswire" style="margin:10px 0">
	<br><br>Hello <?= $vd->esc($vd->contact->name()) ?>
	<br><br>Thank you for registering with Newswire.
	<br>Please click the link below to activate the account.
	<br><br>
	<a href="<?= $ci->website_url("journalists/activate/{$vd->subscriber->id}/{$vd->secret}") ?>">
		<?= $ci->website_url("journalists/activate/{$vd->subscriber->id}/{$vd->secret}") ?></a>
	<br><br>The link can also be used to edit your subscription in the future.
	<div style="color:#999999;font-size:12px">
		<br><br>This is an automated email. 
			Please visit our website to contact us.
	</div>
</div>