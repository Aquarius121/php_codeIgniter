<?= $ci->load->view('manage/account/menu') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Orders</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="table-responsive">
					<table class="table" id="selectable-results">
						<thead>
							<tr>
								<th class="left">Order</th>
								<th>Date Created</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							
							<?php foreach ($vd->results as $order): ?>
							<tr>
								<td class="left">
									<h3>
										<a href="manage/account/order/view/<?= $order->id ?>">
											Order <span class="order_id"><?= $order->nice_id() ?></span>
										</a>
									</h3>
									<ul class="actions">
										<li><a href="manage/account/order/view/<?= $order->id ?>">View Order</a></li>
										<li><a href="manage/account/order/make/<?= $order->id ?>">Make Another</a></li>
									</ul>
								</td>
								<td>
									<?php $dt_created = Date::out($order->date_created); ?>
									<?= $dt_created->format('M j, Y') ?>
								</td>
								<td>
									$ <?= number_format($order->price_total, 2) ?>
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
	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Orders</p>
</div>
							
							