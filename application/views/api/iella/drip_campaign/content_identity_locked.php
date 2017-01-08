<?= $ci->load->view('email/container/header') ?>

Hello <strong style="font-weight:500;"><?= $vd->esc($vd->user->first_name) ?></strong>,
<br /><br />

My name is Erik Rohrmann, Head of Business Development at Newswire.
<br /><br />
Would you like to boost the distribution of your recent press release: <strong style="font-weight:500;"><?= 
	$vd->esc($vd->content->title) ?></strong>

<br /><br />
We can offer you Premium Distribution to 100+ major websites including Google News, Digital Journal & The 
Herald for $49 (17% off the retail price), as well as the ability to include links, images, video and targeted 
distribution to media in your industry with your Premium Release.
<br /><br />
To take advantage of this offer please email me at, <a href="mailto:erik@newswire.com" title="" target="_blank" 
	style="color:#1357a8;">erik@newswire.com</a>, and I will provide you with the discount coupon code.
<br /><br />
Thank you for your business and we look forward to helping you achieve your PR goals.

<br /><br />

Best Regards,<br /> 
<strong style="font-weight:500;">Erik Rohrmann</strong>

<?= $ci->load->view('email/container/footer') ?>