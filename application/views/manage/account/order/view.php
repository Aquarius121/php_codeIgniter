<?= $ci->load->view('manage/account/menu') ?>

<ul class="breadcrumb">
	<li><a href="manage/account">Account</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/account/order/history">Orders</a> <span class="divider">&raquo;</span></li>
	<li class="active">View Order</li>
</ul>

<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-12 page-title">
				<h2>View Order</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
			
					<?php if (!empty($vd->order_data->client_name)): ?>
						<div class="order-client-name marbot-15">
							<span class="muted">Client:</span>
							<span><?= $vd->esc($vd->order_data->client_name) ?></span>
						</div>
					<?php endif ?>
					
					<div class="table-responsive">
						<table class="table view-order-table marbot">
							<thead>
								<tr>
									<th class="ta-left">Item</th>
									<th>Quantity</th>
									<th>Price</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($vd->cart->items() as $cart_item): ?>
									<tr>
										<td class="ta-left"><?= $vd->esc($cart_item->name) ?></td>
										<td><?= $vd->esc($cart_item->quantity) ?></td>
										<td><?= $vd->cart->format($cart_item->price, true) ?></td>
										<td class="vot-line-total">
											<?= $vd->cart->format($cart_item->price * 
												$cart_item->quantity) ?>
										</td>
									</tr>
									<?php foreach ($cart_item->attached as $atd): ?>
									<tr class="attached-cart-item">
										<td class="ta-left">+ 
											<?= $vd->esc($atd->name) ?>
											<?php if ($atd->quantity > 1): ?>
												(<?= $atd->quantity ?>)
											<?php endif ?>
										</td>
										<td></td>
										<td><?= $vd->cart->format($atd->price_total(), true) ?></td>
										<td class="vot-line-total">
											<?= $vd->cart->format($atd->price_total() * 
												$cart_item->quantity) ?>
										</td>
									</tr>
									<?php endforeach ?>
								<?php endforeach ?>
								<?php if ($vd->cart->coupon()): ?>
								<tr>
									<td colspan="3">Coupon</td>
									<td class="vot-coupon"><?= $vd->esc($vd->cart->coupon()->code()) ?></td>
								</tr>
								<tr>
									<td colspan="3">Discount</td>
									<td class="vot-discount"><?= $vd->cart->format($vd->cart->discount()) ?></td>
								</tr>
								<?php endif ?>		
								<tr>
									<td colspan="3">Total Paid</td>
									<td class="vot-total">
										<?php if ($vd->gTransaction && $vd->gTransaction->is_voided()): ?>
											<del><?= $vd->cart->format($vd->cart->total_with_discount()) ?></del>
											<div class="status-false">VOIDED</div>
										<?php else: ?>
											<?= $vd->cart->format($vd->cart->total_with_discount()) ?>
										<?php endif ?>
									</td>
								</tr>
								<?php if ($vd->gTransaction && ($refunds = $vd->gTransaction->refunds())): ?>
									<?php foreach ($refunds as $k => $refund): ?>
									<tr class="vot-refund <?= value_if(!$k, 'vot-first-refund') ?>">
										<td colspan="3">
											<span class="smaller status-muted"><?= Date::out($refund->date())->format('Y-m-d H:i') ?></span>
											<strong class="status-info">REFUNDED</strong>
										</td>
										<td>
											<span class="status-false"><?= $vd->cart->format($refund->amount()) ?></span>
										</td>
									</tr>
									<?php endforeach ?>
								<?php endif ?>
							</tbody>
						</table>
					</div>
			
					<ul class="view-order-details nopad">
						<li>
							<strong>Order Number:</strong>
							<span class="order_id"><?= $vd->order->nice_id() ?></span>
						</li>
						<li>
							<strong>Date Created:</strong>
							<?php $dt_created = Date::out($vd->order->date_created) ?>
							<?= $dt_created->format('Y-m-d H:i') ?>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<?php if ($vd->advanced_view): ?>
		
		<header>
			<div class="row">
				<div class="col-lg-12 page-title">
					<h2>Transactions</h2>
				</div>
			</div>
		</header>

		<?= $ci->load->view('manage/account/transaction/partials/list') ?>

	<?php endif ?>

</div>