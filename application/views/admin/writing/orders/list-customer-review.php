<?= $this->load->view('admin/writing/orders/partials/list-header') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<div class="tab-content">
				<div class="tab-pane active">
			     
					<table class="grid writing-orders-grid">
						<thead>
							<tr>
								<th class="left">Order</th>
								<th>Details Provided<sup>&dagger;</sup></th>
								<th>Draft Written<sup>*</sup></th>
								<th>Sent For Review</th>
							</tr>
						</thead>
						
						<tbody>
							
							<?php foreach ($vd->results as $result): ?>
							<?php extract(get_object_vars($result)); ?>
							<tr>
								
								<?= $this->load->view('admin/writing/orders/partials/list-order', array('result' => $result)) ?>
																					
								<td>
									<?php if ($writing_order->date_ordered): ?>
									<?php $dt_submitted = Date::out($writing_order->date_ordered); ?>
									<?= $dt_submitted->format('M j, Y') ?>&nbsp;
									<span class="muted"><?= $dt_submitted->format('H:i') ?></span>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
								
								<td>
									<?php if ($writing_order->date_submitted_by_writer): ?>
									<?php $dt_written = Date::out($writing_order->date_submitted_by_writer); ?>
									<?= $dt_written->format('M j, Y') ?>&nbsp;
									<span class="muted"><?= $dt_written->format('H:i') ?></span>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
								
								<td>
									<?php if ($writing_order->date_sent_to_customer): ?>
										<?php $dt_sent = Date::out($writing_order->date_sent_to_customer); ?>
										<?= $dt_sent->format('M j, Y') ?>&nbsp;
										<span class="muted"><?= $dt_sent->format('H:i') ?></span>
										<?php if ($writing_session && $content): ?>
										<a target="_blank" class="block"
											href="<?= $ci->website_url("view/preview/{$content->id}") ?>">
											Customer Link
										</a>
										<?php else: ?>
										<a target="_blank" class="block" 
											href="writing/draft/review/<?= $writing_order->id ?>/<?= $writing_order_code->code() ?>">										
											Customer Link
										</a>
										<?php endif ?>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
							
							</tr>
							<?php endforeach ?>
							
						</tbody>
					</table>
				
					<div class="grid-report">
						<div class="row-fluid">
						<div class="span6 ta-left">
							<div>&dagger; The first time the customer submitted.</div>
							<div>* When the most recent version was received.</div>
						</div>
						<div class="span6">
							Displaying <?= count($vd->results) ?> 
							of <?= $vd->chunkination->total() ?> Orders
						</div>
						</div>
					</div>
					<?= $vd->chunkination->render() ?>
					
				</div>
			</div>			
		</div>		
	</div>
</div>