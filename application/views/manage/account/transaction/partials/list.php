<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="table-responsive">
				<table class="table marbot">
					<thead>
						<tr>
							<th class="left">Transaction</th>
							<th>Date Created</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						
						<?php foreach ($vd->transactions as $transaction): ?>
						<tr>
							<td class="left">
								<h3>
									<a href="manage/account/transaction/view/<?= $transaction->id ?>">
										Transaction <span class="transaction_id"><?= $transaction->nice_id() ?></span>
									</a>
								</h3>
								<ul class="actions">
									<li><a href="manage/account/transaction/view/<?= $transaction->id ?>">View Transaction</a></li>
									<?php if ($transaction->order_id): ?>
									<li><a href="manage/account/order/view/<?= $transaction->order_id ?>">View Order</a></li>
									<?php endif ?>
									<li class="send-receipt"><a href="manage/account/transaction/receipt/<?= $transaction->id ?>">Send Receipt</a></li>
								</ul>
							</td>
							<td>
								<?php $dt_created = Date::out($transaction->date_created); ?>
								<?= $dt_created->format('M j, Y') ?>
							</td>
							<td>
								$ <?= number_format($transaction->price, 2) ?>
							</td>
						</tr>
						<?php endforeach ?>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $vd->chunkination->render() ?>		

<p class="pagination-info ta-center">Displaying <?= count($vd->transactions) ?> 
	of <?= $vd->chunkination->total() ?> Transactions</p>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<script type="text/javascript">

	$(function() {

		var send_receipt_link = $(".send-receipt a");
		send_receipt_link.on("click", function(ev){
			ev.preventDefault();
			var default_email = <?= json_encode($vd->user_email) ?>;
			var message = "Where should we send your receipt?";
			var receipt_url = send_receipt_link.attr("href");
			bootbox.prompt({
				title : message,
				value : default_email,
				callback : function(email_to) {
					if (!email_to) return;
					var data = { email : email_to };
					$.post(receipt_url, data, function() {
						window.location = window.location;
					});
				}
			});
		});
		
	});

</script>