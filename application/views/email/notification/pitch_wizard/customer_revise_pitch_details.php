<strong style="font-weight:500;"><?= $customer_name ?></strong>,
<br /><br />
Our writers have found that the details you provided for your recent pitch 
wizard writing order seem to be insufficient. 
<br /><br />

<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr style="background-color:#616871; height:40px; border-bottom:2px solid #fff;">
		<td style=" width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
			alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>
		
		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; 
			line-height:26px; text-align:left;">Writer Comments</td>

		<td style=" width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" 
			height="1" width="30" style="border:0; display:block;" /></td>
	</tr>
	
	<tr style="background-color:#e0e3e7; height:40px; padding-top: 10px;">
		<td style="width:30px; border-top: 2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
			alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>

		<td style="color:#656565; border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
			font-size:16px; font-weight:200; line-height:26px; text-align:left;"><?= nl2br($comments) ?></td>

		<td style=" width:30px; border-top: 2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
			alt="" border="0" height="1" width="30" style="border:0; display:block;" /></td>
	</tr>
</table>

<br />

You can edit the details here:<br />
<a href="<?= $pitch_edit_link ?>" title="" target="_blank" style="color:#1357a8;"><?= $pitch_edit_link ?></a>

<br /><br />

Best Regards,<br /> 
<strong style="font-weight:500;">Newswire Team</strong>