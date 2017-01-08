<?= $ci->load->view('email/container/header') ?>

Hello <strong style="font-weight:500;"><?= $vd->esc($vd->user->first_name) ?></strong>,

<br /><br />
Thank you for registering with Newswire.

<br /><br />
Please <a href="<?= $ci->website_url("login/verify/{$vd->user->id}/{$vd->secret}") ?>" target="_blank"
	style="color:#1357a8;">click here to activate your account</a>.

<?php if ($vd->password): ?>
	<br /><br />
	Your password is:
	<strong><?= $vd->esc($vd->password) ?></strong>
<?php endif ?>

<br /><br />
If you did not request membership into Newswire.com, or have changed your mind, no further action is required.

<br /><br />
If you have any questions, feel free to contact us via phone on 800-713-7278 or send
	us an email to support@newswire.com.

<?= $ci->load->view('email/container/footer') ?>
