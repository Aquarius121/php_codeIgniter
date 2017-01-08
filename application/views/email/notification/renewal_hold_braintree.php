<?= $ci->load->view('email/container/header') ?>

Hi <strong style="font-weight:500;"><?= $user->first_name ?></strong>,
<br /><br />
We have received notification that your recent attempt at payment for the following items was unable to process. 
<br /><br />
This may have occurred for a variety reasons relating to card information or 
potential changes by the card issuer. We want to make sure that we help in 
resolving this as quickly as possible so that your service is not interrupted.
<br /><br />



<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr style="background-color:#616871; height:40px;">

		<td style=" width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" 
			height="1" width="30" style="border:0; display:block;" /></td>

		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; 
			line-height:26px; text-align:left; width:50%;">
			Item
		</td>

		<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
			width="2" style="border:0; display:block;" /></td>
		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; 
		line-height:26px; text-align:center;">
			Quantity
		</td>
		<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
			width="2" style="border:0; display:block;" /></td>
		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; 
		line-height:26px; text-align:center;">
			Price
		</td>
		<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
			width="2" style="border:0; display:block;" /></td>
		<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; 
		line-height:26px; text-align:center;">
			Total
		</td>
	</tr>

	<tbody>
		<?php foreach ($v_cart->items() as $cart_item): ?>
		<tr style="background-color:#e0e3e7; height:40px;">
			
			<td style="width:30px; border-top:2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
				border="0" height="1" width="30" style="border:0; display:block;" /></td>
			
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:left; width:50%;"><?= $vd->esc($cart_item->item()->name) ?></td>

			<td style="background-color:#ffffff; border-top:2px solid #fff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>
			
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; 
				font-weight:200; line-height:26px; text-align:center;"><?= $vd->esc($cart_item->quantity) ?></td>


			<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>
			
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:center;"><?= $v_cart->format($cart_item->price, true) ?></td>

			<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>
			
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; padding-right:15px; text-align:right;">
				<?= $v_cart->format($cart_item->price * 
					$cart_item->quantity) ?>
			</td>
		</tr>

						
		<?php endforeach ?>
		<?php if ($v_cart->coupon()): ?>
		<tr style="background-color:#e0e3e7; height:40px;">
			<td style="width:30px; border-top:2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" 
				height="1" width="30" style="border:0; display:block;" /></td>
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:left; width:50%;">

			<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:center;">
			</td>

			<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>
			<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:center;">Discount</td>
			
			<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
				alt="" border="0" height="1" width="2" style="border:0; display:block;" /></td>

			<td style="color: #900; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
				line-height:26px; text-align:center;"><strong style="font-weight:500;"><?= $v_cart->format($v_cart->discount()) ?></strong>
			</td>
		</tr>
		
		<?php endif ?>

		<tr style="background-color:#ffffff; height:40px;">
			<td colspan="3" style="border-top:2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
				width="1" style="border:0; display:block;" /></td>
			<td colspan="5" style="border-top:2px solid #fff; background-color:#edeff2; color:#656565; 
				font-family:Helvetica, Arial, sans-serif;
				font-size:16px; font-weight:200; line-height:26px; padding-right:15px; text-align:right;">
				Total Amount Paid: <strong style="font-weight:500;">
				<?= $v_cart->format($v_cart->total_with_discount()) ?></strong>
			</td>
		</tr>
	</tbody>
</table>

<br /> <br />
If you believe this is an error please let us know and we will work with our service providers to correct the issue. 
<br /><br />
We appreciate your business and want to help in resolving this matter in a timely manner. 
<br /><br />
Should you have any questions please feel free to contact us. 
<br /><br />
Warmest Regards,
<br /><br />
<strong style="font-weight:500;">Patrick Santiago</strong><br />
Business Development<br />
Newswire.com, LLC<br />
Office: <span class="apple-link"><a href="tel:8007137278" title="" target="_blank" 
	style="color:#1357a8;">(800) 713-7278</a></span><br />
Website: <a href="http://newswire.com/" title="" target="_blank" 
	style="color:#1357a8;">www.newswire.com</a><br />
Email: <a href="mailto:patrick@newswire.com" title="" target="_blank" 
	style="color:#1357a8;">patrick@newswire.com</a><br />
Facebook: <a href="https://www.facebook.com/inewswire" title="" target="_blank" 
	style="color:#1357a8;">https://www.facebook.com/inewswire</a>

<?= $ci->load->view('email/container/footer') ?>