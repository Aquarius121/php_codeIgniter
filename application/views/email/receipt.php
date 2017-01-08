<?= $ci->load->view('email/container/header') ?>

					Hello <strong style="font-weight:500;"><?= $vd->esc($vd->user->name()) ?></strong>,
					<br /><br />
					This is your payment receipt from Newswire. Thank you for your purchase!
				</td>
				<td class="margins" style="width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
					alt="" border="0" height="1" width="15" style="border:0; display:block;" /></td>
			</tr>
		</table>
	</td>
</tr>
						
<tr style="background:#fff">
	<td style="height:60px; background:#fff"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
		border="0" height="60" width="1" style="border:0; display:block;" /></td>
</tr>

<tr>
<td id="section-table" class="section" style="">
	
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%; background-color: #fff;">
		<tr>
			<td class="margins" style="width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
				border="0" height="1" width="15" style="border:0; display:block;" /></td>
			
			<td style="">				
				<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
					<tr style="background-color:#616871; height:40px;">
						<td style=" width:30px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="30" style="border:0; display:block;" /></td>

						<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; 
							font-weight:500; line-height:26px; text-align:left; width:50%;">Item</td>

						<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="2" style="border:0; display:block;" /></td>

						<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; 
							font-weight:500; line-height:26px; text-align:center;">Quantity</td>

						<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="2" style="border:0; display:block;" /></td>

						<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; 
							font-weight:500; line-height:26px; text-align:center;">Price</td>

						<td style="width:2px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="2" style="border:0; display:block;" /></td>

						<td style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; 
							font-weight:500; line-height:26px; text-align:center;">Total</td>
					</tr>

					<!--<tr>
						<td colspan="8" style="background-color:#ffffff; height:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="2" 
								width="1" style="border:0; display:block;" /></td>
					</tr>-->
					
					<?php foreach ($vd->cart->items() as $cart_item): ?>
					<tr style="background-color:#e0e3e7; height:40px;">
						<td style="width:30px; border-top: 2px solid #fff;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="30" style="border:0; display:block;" /></td>

						<td style="color:#656565; border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:left; width:50%;">
							<?= $vd->esc($cart_item->name) ?>
							<?php foreach ($cart_item->attached as $atd): ?>
								<?php if ($atd->hidden) continue; ?>
								<br>&nbsp;&nbsp;+ 
									<?= value_if_test($atd->quantity > 1, (int) $atd->quantity) ?> 
									<?= $vd->esc($atd->name) ?>
							<?php endforeach ?>
						</td>

						<td style="background-color:#ffffff; border-top: 2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
							<?= $vd->esc($cart_item->quantity) ?>
						</td>
						<td style="background-color:#ffffff; border-top: 2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565;border-top:2px solid #fff;font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
							<?= $vd->cart->format($cart_item->price, true) ?>
						</td>

						<td style="background-color:#ffffff; width:2px; border-top: 2px solid #fff;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565;  border-top: 2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; padding-right:15px; text-align:right;">
							<?= $vd->cart->format($cart_item->price * 
								$cart_item->quantity) ?>
						</td>
					</tr>
					
					<?php endforeach ?>

					<?php if ($vd->cart->coupon()): ?>
					<tr style="background-color:#e0e3e7; height:40px;">
						<td style="width:30px; border-top:2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
							alt="" 	border="0" height="1" width="30" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:left; width:50%;">

						<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
						</td>

						<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">Coupon</td>

						<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>

						<td style="color: #090; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
							<strong style="font-weight:500;"><?= $vd->esc($vd->cart->coupon()->code) ?></strong>
						</td>
					</tr>

					<tr style="background-color:#e0e3e7; height:40px;">
						<td style="width:30px; border-top:2px solid #fff;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" 
							alt="" 	border="0" height="1" width="30" style="border:0; display:block;" /></td>

						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:left; width:50%;">

						<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
						</td>

						<td style="background-color:#ffffff; border-top:2px solid #fff; width:2px; border-top:2px solid #fff;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>
						<td style="color:#656565; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">Discount</td>
						
						<td style="background-color:#ffffff; width:2px; border-top:2px solid #fff;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
								width="2" style="border:0; display:block;" /></td>

						<td style="color: #900; border-top:2px solid #fff; font-family:Helvetica, Arial, sans-serif; 
							font-size:16px; font-weight:200; line-height:26px; text-align:center;">
							<strong style="font-weight:500;"><?= $vd->cart->format($vd->cart->discount()) ?></strong>
						</td>
					</tr>
					
					<?php endif ?>

					<tr style="background-color:#ffffff; height:40px;">
						<td colspan="3" style="border-top:2px solid #fff;">
							<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" 
							border="0" height="1" width="1" style="border:0; display:block;" /></td>
						<td colspan="5" style="background-color:#edeff2; border-top:2px solid #fff; color:#656565; 
							font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:200; 
							line-height:26px; padding-right:15px; text-align:right;">
							Total Amount Paid: <strong style="font-weight:500;">
							<?= $vd->cart->format($vd->cart->total_with_discount()) ?></strong>
						</td>
					</tr>
				</table>
				
			</td>
			<td class="margins" style="width:30px;">
				<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="15" 
				style="border:0; display:block;" /></td>
		</tr>
	</table>

</td>
</tr>

<tr style="background:#fff">
	<td style="height:25px;"><img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="25"
		width="1" style="display:block;" style="border:0; display:block;" /></td>
</tr>

<tr style="background:#fff">
	<td id="section-customer" class="section" style="">
		
		<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
			<tr>
				<td class="margins" style="width:30px;">
					<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" width="30" 
						style="border:0; display:block;" /></td>
				<td style="">
					
					<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
						<tr>
							<th class="stack-center" style="color:#656565; font-family:Helvetica, Arial, sans-serif; 
								font-size:14px; font-weight:200; line-height:20px; text-align:left;" valign="top">
								
								<?php if (!empty($vd->order_data->client_name)): ?>
									<strong style="font-weight:500;">
										<?= $vd->esc($vd->order_data->client_name) ?>
									</strong>
									<br />
								<?php endif ?>

								<?php if ($vd->data->first_name): ?>
									<?= $vd->esc($vd->data->first_name) ?> 
									<?= $vd->esc($vd->data->last_name) ?>
									<br />
								<?php endif ?>

								<?php if ($vd->data->company_name): ?>
									<?= $vd->esc($vd->data->company_name) ?>
									<br />
								<?php endif ?>
								
								<?php if ($vd->data->street_address): ?>
									<?= $vd->esc($vd->data->street_address) ?>
									<br />
								<?php endif ?>

								<?php if ($vd->data->locality && $vd->data->region): ?>
									<?= $vd->esc($vd->data->locality) ?>, 
									<?= $vd->esc($vd->data->region) ?>
									<br />
								<?php else: ?>
									<?= $vd->esc($vd->data->locality) ?>
									<br />
								<?php endif ?>

								<?php if ($vd->data->country_id): ?>
									<?php $country = Model_Country::find($vd->data->country_id); ?>
									<?= $vd->esc($country->name) ?>
									<br />
								<?php endif ?>

								<?php if ($vd->user): ?>
									<a style="color:#617C89;margin-top:5px;display:block;">
										<?= $vd->esc($vd->user->email) ?></a>
									<br />
								<?php endif ?>

							</th>
							<th class="spacer-horizontal" style="width:20px;">
								<img src="<?= $vd->assets_base ?>im/clearpixel.gif" alt="" border="0" height="1" 
									width="20" style="border:0; display:block;" /></th>
							<th class="stack-center" style="color:#8c9095; font-family:Helvetica, Arial, sans-serif; 
								font-size:14px; font-weight:200; line-height:20px; text-align:right;" valign="top">
								Transaction Number: <?= $vd->transaction->nice_id() ?><br />
								<?php if ($vd->order): ?>
									Order Number: <?= $vd->order->nice_id() ?><br />
								<?php endif ?>
							</th>
						</tr>
					</table>
					<br /><br />
					<br /><br />
					
					<h6 style="color:#8c9095; font-family:Helvetica, Arial, sans-serif; 
						font-size:12px; font-weight:200; margin:0; padding:0; text-align:center;">This is an 
						automated email. Please visit our website to contact us.</h6>
					

<?= $ci->load->view('email/container/footer') ?>