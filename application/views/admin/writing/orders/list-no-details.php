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
								<th>Contacted Last</th>
								<th>Send Reminder</th>
							</tr>
						</thead>
						
						<tbody>
							
							<?php foreach  ($vd->results as $result): ?>
							<?php extract(get_object_vars($result)); ?>
							<tr>
								
								<?= $this->load->view('admin/writing/orders/partials/list-order', array('result' => $result)) ?>
								
								<td>
									<?php $dt_last_reminder = Date::out($writing_order_code->date_last_reminder_sent); ?>
									<?php if ($dt_last_reminder <= Date::zero()): ?>
									<span>-</span>
									<?php else: ?>
										<?= $dt_last_reminder->format('M j, Y') ?>&nbsp;
										<span class="muted"><?= $dt_last_reminder->format('H:i') ?></span>
									<?php endif ?>
								</td>

								<td>
									<a href='admin/writing/orders/no_details/send_reminder/<?= $writing_order_code->id ?>'>
										Send Reminder
									</a>
								</td>
								
							</tr>
							<?php endforeach ?>  
							
						</tbody>
					</table>
				
					<div class="grid-report">Displaying <?= count($vd->results) ?> 
						of <?= $vd->chunkination->total() ?> Orders</div>
					<?= $vd->chunkination->render() ?>
					
				</div>
			</div>			
		</div>		
	</div>
</div>