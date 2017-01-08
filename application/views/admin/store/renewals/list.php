<div class="row-fluid padbot">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Renewals</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">

			<table class="grid">
				<thead>
					<tr>
						<th class="left">Renewal</th>
						<th>
							Start Date
							<div class="muted smaller">Expires Date</div>
						</th>
						<th>
							<div>Renewal Date*</div>
							<div class="status-false smaller">Failure Alerts</div>
						</th>
						<th>Account</th>
					</tr>
				</thead>
				<tbody>
					
					<?php foreach ($vd->results as $sub): ?>
					<tr>
						<td class="left">
							<h3>
								<?php if (!$sub->is_auto_renew_enabled): ?>
								<span class="label-class renewal-class">
									<strong class="status-false">ENDED</strong>
								</span>
								<?php elseif ($sub->is_suspended): ?>
								<span class="label-class renewal-class">
									<strong class="status-false">SUSPENDED</strong>
								</span>
								<?php else: ?>
								<span class="label-class renewal-class">
									<?php if ($sub->is_legacy): ?>
										<strong class="renewal-legacy">LEGACY</strong>
									<?php elseif ($sub->item_type == Model_Item::TYPE_PLAN): ?>
										<strong class="renewal-plan">PACKAGE</strong>
									<?php elseif ($sub->item_type == Model_Item::TYPE_CREDIT): ?>
										<strong class="renewal-credit">CREDIT</strong>
									<?php else: ?>
										<strong class="renewal-other">OTHER</strong>
									<?php endif ?>
								</span>
								<?php endif ?>
								<?= $vd->esc($vd->cut($sub->item_name, 30)) ?>
								<?php if ($sub->quantity > 1): ?>
									(<?= $sub->quantity ?>)
								<?php endif ?>
							</h3>
							<ul>
								<?php if (!empty($sub->order_id)): ?>
									<li><a href="<?= Admo::url(null, $sub->user->id) ?>manage/account/order/view/<?= $sub->order_id ?>"
										target="_blank">View Order</a></li>
								<?php endif ?>
								<?php if ($sub->is_auto_renew_enabled): ?>
									<li><a href="<?= Admo::url(null, $sub->user->id) ?>manage/account/renewal/cancel/<?= $sub->id ?>"
											target="_blank">Cancel</a></li>
									<?php if (Auth::is_admin_online() && !$sub->is_legacy): ?>
									<?php if ($sub->is_suspended): ?>
										<li><a href="<?= Admo::url(null, $sub->user->id) ?>manage/account/renewal/suspend/<?= $sub->id ?>"
											target="_blank">Activate</a></li>
									<?php else: ?>
										<li><a href="<?= Admo::url(null, $sub->user->id) ?>manage/account/renewal/suspend/<?= $sub->id ?>"
											target="_blank">Suspend</a></li>
									<?php endif ?>
									<?php endif ?>
								<?php endif ?>
							</ul>
						</td>
						<td>
							<?php if ($sub->is_legacy): ?>
							<span>-</span>
							<?php else: ?>
							<?php $dt_created = Date::utc($sub->date_created); ?>
							<?= $dt_created->format('M j, Y') ?>
							<div class="muted smaller">
								<?php $dt_expires = Date::utc($sub->date_expires); ?>
								<?= $dt_expires->format('M j, Y') ?>
							</div>
							<?php endif ?>
						</td>
						<td>
							<?php if ($sub->is_suspended): ?>
								<span>-</span>
							<?php else: ?>
								
								<?php $dt_termination = Date::utc($sub->date_termination); ?>
								<?= $dt_termination->format('M j, Y') ?>
								
								<?php if ($sub->is_legacy && Date::utc($sub->date_termination) < Date::days(-1)
									&& $sub->is_auto_renew_enabled): ?>
								<div class="smaller status-false">Update Ultracart</div>
								<?php endif ?>

								<?php if ($sub->is_on_hold): ?>
								<div class="status-false smaller">Billing Failure</div>
								<?php endif ?>

							<?php endif ?>
						</td>
	
						<?= $ci->load->view('admin/partials/owner-column', 
							array('result' => $sub)); ?>

					</tr>
					<?php endforeach ?>
					
				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report">
					* The account will be billed <?= Renewal::AUTO_RENEW_PRE_HOURS ?>
					  hours before the renewal date. 
				</div>
				<div class="pull-right grid-report">Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> Automatic Renewals</div>
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>