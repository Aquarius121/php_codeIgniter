Hi <?= $vd->esc($vd->claimant_name) ?>, <br><br>

Thanks for taking the time to verify the <?= $vd->esc($vd->company_name) ?> Company Newsroom. <br><br>

We'll continue to publish and curate your social content as it becomes available. <br><br>

However, if you wish to improve your online visibility and submit other content such as 
press releases, news, videos, etc. you may do so by logging in here: <br><br>

<a href="<?= $ci->website_url('login') ?>"><?= $ci->website_url('login') ?></a>
<br>
username: <?= $vd->email ?> <br>
password: <?= $vd->password ?> <br><br><br>


If you have any questions about your newsroom please give us a call or 
contact us using the helpdesk link below: <br><br>

Warm Regards, <br>
The Newswire Team <br>
(800) 713-7278 <br>
<a href="<?= $ci->website_url('helpdesk') ?>"><?= $ci->website_url('helpdesk') ?></a>