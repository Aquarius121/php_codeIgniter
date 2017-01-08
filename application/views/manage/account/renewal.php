<?= $ci->load->view('manage/account/menu') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-12 page-title">
				<h2>Automatic Renewals</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="table-responsive">
					<table class="table marbot">
						<thead>
							<tr>
								<th class="left">Item</th>
								<th>Start Date</th>
								<th>Renewal Date*</th>
							</tr>
						</thead>
						<tbody>
							
							<?php foreach ($vd->results as $sub): ?>
							<tr>
								<td class="left">
									<h3>
										<?php if ($sub->is_suspended): ?>
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
										<?= $vd->esc($sub->item_name) ?>
										<?php if ($sub->quantity > 1): ?>
											(<?= $sub->quantity ?>)
										<?php endif ?>
									</h3>
									<ul class="actions">
										<?php if (!empty($sub->order_id)): ?>
											<li><a href="manage/account/order/view/<?= $sub->order_id ?>">View Order</a></li>
										<?php endif ?>
										<li><a href="manage/account/renewal/cancel/<?= $sub->id ?>">Request Cancellation</a></li>
										<?php if (Auth::is_admin_online()): ?>
										<?php if ($sub->is_suspended): ?>
										<li><a href="manage/account/renewal/suspend/<?= $sub->id ?>">Activate</a></li>
										<?php else: ?>
											<li><a href="manage/account/renewal/suspend/<?= $sub->id ?>">Suspend</a></li>
										<?php endif ?>
										<?php endif ?>
									</ul>
								</td>
								<td>
									<?php $dt_created = Date::out($sub->date_created); ?>
									<?= $dt_created->format('M j, Y') ?>
								</td>
								<td>
									<?php if ($sub->is_suspended): ?>
										<span>-</span>
									<?php else: ?>
										<?php $dt_termination = Date::out($sub->date_termination); ?>
										<?= $dt_termination->format('M j, Y') ?>
										<?php if ($sub->is_on_hold): ?>
										<div class="status-false smaller">Billing Failure</div>
										<?php endif ?>
									<?php endif ?>
								</td>
							</tr>
							<?php endforeach ?>
							
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="pull-left grid-report">
					* Your account will be billed <?= Renewal::AUTO_RENEW_PRE_HOURS ?>
					  hours before the renewal date. 
				</div>
			</div>
		</div>
	</div>

	<?= $vd->chunkination->render() ?>

	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Automatic Renewals</p>

</div>