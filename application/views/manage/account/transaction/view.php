<?= $ci->load->view('manage/account/menu') ?>
<ul class="breadcrumb">
	<li><a href="manage/account">Account</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/account/transaction/history">Transactions</a> <span class="divider">&raquo;</span></li>
	<li class="active">View Transaction</li>
</ul>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-12 page-title">
				<h2>View Transaction</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
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
											<?= value_if_test($atd->quantity > 1, (int) $atd->quantity) ?> 
											<?= $vd->esc($atd->name) ?>
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
							<strong>Transaction:</strong>
							<span class="transaction_id"><?= $vd->transaction->nice_id() ?></span>
						</li>
						<?php if ($vd->order): ?>
						<li>
							<strong>Source Order:</strong>
							<a href="manage/account/order/view/<?= $vd->order->id ?>">
								<span class="order_id"><?= $vd->order->nice_id() ?></span>
							</a>
						</li>
						<?php endif ?>
						<li>
							<strong>Date Created:</strong>
							<?php $dt_created = Date::out($vd->transaction->date_created) ?>
							<?= $dt_created->format('Y-m-d H:i') ?>
						</li>
						<?php if ($vd->status): ?>
						<li>
							<strong>Status:</strong>
							<?= $vd->esc($vd->status) ?>
						</li>
						<?php endif ?>
					</ul>

					<?php if (Auth::is_admin_online()): ?>
					<div class="transaction-actions clear">
						<div class="marbot">&nbsp;</div>
						<form id="action-form" method="post">
							<input type="hidden" name="id" value="<?= $vd->transaction->id ?>" />
							<input type="hidden" id="refund-amount" name="amount" value="0" />
							<button type="button" id="button-void" value="1" class="btn 
								<?= value_if($this->vd->isVoidable, 'btn-danger', 'btn-default') ?>"
								<?= value_if(!$this->vd->isVoidable, 'disabled') ?>>Void Transaction</button>
							<button type="button" id="button-refund" value="1" class="btn 
								<?= value_if($this->vd->isRefundable, 'btn-danger', 'btn-default') ?>"
								<?= value_if(!$this->vd->isRefundable, 'disabled') ?>>Refund</button>
						</form>
					</div>
					<script>
						
					$(function() {

						var bVoid = $("#button-void");
						var bRefund = $("#button-refund");
						var refundAvailable = <?= json_encode($this->vd->refundAvailable) ?>;
						var refundAmount = $("#refund-amount");
						var form = $("#action-form");

						var actionVoid = "manage/account/transaction/view/void";
						var actionRefund = "manage/account/transaction/view/refund";

						var voidMessage = "Please confirm that you wish to <strong \
							class=\"status-false\">VOID</strong> the transaction.";
						var refundTitle = "Refund Transaction";
						var refundMessage = "<p class=\"marbot-20\">Please enter the amount that \
							you wish to <strong class=\"status-false\">REFUND</strong> for the transaction.</p>\
							<div class=\"input-group\"> <div class=\"input-group-addon\">$</div>\
							<input type=\"text\" value=\"" + refundAvailable + "\" id=\"refund-amount-box\" \
							class=\"form-control\" /></div>";

						bVoid.on("click", function() {
							if (bVoid.is(":disabled")) return;
							bootbox.confirm(voidMessage, function (confirm) {
								if (!confirm) return;
								$(document.body).addClass("wait-cursor");
								form.attr("action", actionVoid);
								form.submit();
								form.detach();
							});
						});

						bRefund.on("click", function() {
							if (bRefund.is(":disabled")) return;
							bootbox.dialog({
								message: refundMessage,
								title: refundTitle, 
								buttons: {
									cancel: {
										label: "Cancel",
										className: "btn-default"
									},
									confirm: {
										label: "Refund",
										className: "btn-danger",
										callback: function () {											
											$(document.body).addClass("wait-cursor");
											var value = $("#refund-amount-box").val();
											refundAmount.val(value);
											form.attr("action", actionRefund);
											form.submit();
											form.detach();
										}
									}
								}						
							});
						});

					});

					</script>
					<?php endif ?>
				</div>
			</div>
					
		</div>
	</div>
</div>