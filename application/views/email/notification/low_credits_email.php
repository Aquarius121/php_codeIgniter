<h1 style="color:#f79432; font-size:20px; font-weight:500; margin:0; padding:0; text-align:center;
	font-family:Helvetica, Arial, sans-serif;">You are running low on email credits.</h1>
<br />
<?php if ($stat->available > 0): ?>
	You currently have just <span style="color:#f79432; font-weight:500;"><?= $stat->available ?></span> 
	credits available.
<?php else: ?>
	You have <span style="color:#c8483f; font-weight:500;">0</span> credits available.
<?php endif ?>

<br /><br />

You can purchase <a href="<?= $ci->website_url('manage/upgrade') ?>" title="" target="_blank" 
	style="color:#1357a8;">additional credits</a> from within your account control panel.
