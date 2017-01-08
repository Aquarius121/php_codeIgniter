<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Transactions</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/store/partials/tabs') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Transaction</th>
						<th>Details</th>
						<th>Account</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						
						<td class="left">
							<h3>
								<a href="<?= Admo::url("manage/account/transaction/view/{$result->id}", $result->user_id) ?>"
									target="_blank">
									<strong><?= strtoupper(substr($result->id, 0, 8)) ?></strong><?= 
										strtoupper(substr($result->id, 8)) ?>
								</a>
							</h3>
							<ul>								
								<li><a href="<?= Admo::url("manage/account/transaction/view/{$result->id}", $result->user_id) ?>"
									target="_blank">View Transaction</a></li>
								<?php if ($result->order_id): ?>
								<li><a href="<?= Admo::url("manage/account/order/view/{$result->order_id}", $result->user_id) ?>"
									target="_blank">View Order</a></li>
								<?php endif ?>
								<li><a href="<?= Admo::url("manage/account/transaction/receipt/{$result->id}", $result->user_id) ?>"
									target="_blank">Send Receipt</a></li>
							</ul>
						</td>
						<td>
							<?php $dt_created = Date::out($result->date_created); ?>
							<?= $dt_created->format('M j, Y') ?>&nbsp;
							<span class="muted"><?= $dt_created->format('H:i') ?></span>
							<div class="status-muted smaller">
								<span class="status-true">$ <?= number_format($result->price, 2) ?></span>
								<span>|</span>
								<span class="status-false"><?= $vd->esc($result->gateway) ?></span>
							</div>
						</td>
						
						<?= $ci->load->view('admin/partials/owner-column', 
								array('result' => $result)); ?>
						
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Transactions
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>