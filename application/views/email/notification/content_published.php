<table align="center" border="0" cellpadding="0" cellspacing="0" style="">
	<tr>
		<th style="">
			<img class="" src="<?= $vd->assets_base ?>im/icon-content-published.png" alt="" border="0" 
				style="border:0; display:block;" />
		</th>
		<th class="spacer-horizontal" style="width:20px;">
			<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="20" 
				style="border:0; display:block;" /></th>
		<th style="color:#656565; font-family:Helvetica, Arial, sans-serif; font-size:20px; font-weight:200; 
			line-height:28px; text-align:left;">
			Your <?= strtolower(Model_Content::full_type($content->type)) ?> submission has been published!
		</th>
	</tr>
</table>

<br />

<center>
<a href="<?= $ci->website_url($content->url()) ?>" title="" style="color:#1357a8;"><?= 
	$vd->esc($content->title) ?></a>
</center>