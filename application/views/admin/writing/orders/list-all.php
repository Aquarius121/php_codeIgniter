<?= $this->load->view('admin/writing/orders/partials/list-header') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<div class="tab-content">
				<div class="tab-pane active">
			     
					<table class="grid grid-tickboxes writing-orders-grid">
						<thead>
							<tr>
								<th class="left">Order</th>
								<th class="tb">Details</th>
								<th class="tb">Assign</th>
								<th class="tb">Recv'd</th>
								<th class="tb">Sent</th>
								<th class="tb">App/Rej</th>
								<th class="tb">Submit</th>
								<th class="tb">Report</th>
							</tr>
						</thead>
						
						<tbody>
							
							<?php foreach  ($vd->results as $result): ?>
							<?php extract(get_object_vars($result)); ?>
							<tr>
								
								<?= $this->load->view('admin/writing/orders/partials/list-order', array('result' => $result)) ?>
								
								<?php if ($writing_order && $writing_order->status): ?>
								<td class="success">
									<a title="Details received on 
										<?php $dt_ordered = Date::out($writing_order->date_ordered); ?>
										<?= $dt_ordered->format('M j, Y H:i') ?>"
										<?php if ($writing_session && $company): ?>
										href="<?= $company->newsroom()->url() ?>manage/writing/process/<?= $writing_session->id ?>/4/review"
										<?php elseif (!$writing_session): ?>
										href="writing/prdetails/edit/<?= $writing_order->id ?>/<?= $writing_order_code->code() ?>"
										<?php endif ?>
										class="tl" target="_blank">
										<i class="icon-ok"></i>
									</a>
								</td>
								<?php else: ?>
								<td class="fail">
									<a title="Details not received" class="tl">
										<i class="icon-remove"></i></a>
								</td>
								<?php endif ?>
									
								<?php if ($writing_order && $writing_order->max_status_index >= 
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER)): ?>
									<td class="success">
										<a title="Assigned to <?= $writer ? $writer->name() : 'writer' ?> on 
											<?php $assigned = Date::out($writing_order->date_assigned_to_writer); ?>
											<?= $assigned->format('M j, Y H:i') ?>" 
											class="tl"><i class="icon-ok"></i></a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not assigned to a writer" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>   
								
								<?php if ($writing_order && $writing_order->max_status_index >= 
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER)): ?>
									<td class="success">
										<a title="Received from the writer on 
											<?php $written = Date::out($writing_order->date_written); ?>
											<?= $written->format('M j, Y H:i') ?>"
											href="writing/draft/review/<?= $writing_order->id ?>/<?= $writing_order_code->code() ?>"
											target="_blank" class="tl">
											<i class="icon-ok"></i>
										</a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not received from the writer" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>
								
								<?php if ($writing_order && $writing_order->max_status_index >=
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="success">
										<?php if ($writing_session && $content)
												$c_preview_url = $ci->website_url("view/preview/{$content->id}");
											else if ($reseller_details)
												$c_preview_url = $reseller_details->preview_url($writing_order->id,
												$writing_order_code->code());
											else $c_preview_url = null; ?>
										<a <?php if ($writing_order->date_sent_to_customer): ?>
											<?php $dt_sent_to_customer = Date::out($writing_order->date_sent_to_customer); ?>
												title="Sent to the customer on <?= $dt_sent_to_customer->format('M j, Y H:i') ?>"
											<?php endif ?>
											<?php if ($c_preview_url): ?>
											href="<?= $c_preview_url ?>"
											<?php endif ?>
											class="tl"><i class="icon-ok"></i></a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not sent to the customer" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>
								
								<?php if ($writing_order && $writing_order->max_status_index == 
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="fail">
										<a title="Waiting for a response from the customer" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php elseif ($writing_order && $writing_order->max_status_index >=
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="success <?= value_if_test($writing_order->max_status_index == 
										Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_CUSTOMER_REJECTED), 
										'success-rejected') ?>">
										<?php if ($writing_order->max_status_index >= Model_Writing_Process::status_to_index(
												Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED)): ?>
											<a class="tl" title="Approved on 
												<?php $approved = Date::out($writing_order->date_customer_approved); ?>
												<?= $approved->format('M j, Y H:i') ?>">
												<i class="icon-ok"></i>
											</a>
										<?php elseif ($writing_order->max_status_index == Model_Writing_Process::status_to_index(
											Model_Writing_Order::STATUS_CUSTOMER_REJECTED)): ?>
											<a class="tl" title="Rejected on 
												<?php $rejected = Date::out($writing_order->date_customer_rejected); ?>
												<?= $rejected->format('M j, Y H:i') ?>">
												<i class="icon-ban-circle"></i>
											</a>
										<?php endif ?>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Neither approved or rejected" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>  
							
								<?php if ($content && $content->is_published): ?>
									<td class="success">
										<a title="Published on 
											<?php $publish = Date::out($content->date_publish); ?>
											<?= $publish->format('M j, Y H:i') ?>" class="tl" 
											href="<?= $ci->website_url("view/preview/{$content->id}") ?>"
											target="_blank">
											<i class="icon-ok"></i>
										</a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not published" class="tl">
											<i class="icon-remove"></i></a>
									</td>  
								<?php endif ?>
								
								<?php if ($writing_order && $writing_order->max_status_index >=
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED) 
									&& $writing_order->date_report_sent): ?>
									<td class="success">
										<a title="Report sent on 
											<?php $dt_report_sent = Date::out($writing_order->date_report_sent); ?>
											<?= $dt_report_sent->format('M j, Y H:i') ?>"
										class="tl" 
										<?php if ($content): ?>
										href="admin/writing/orders/report/<?= $content->id ?>"
										<?php endif ?>
										target="_blank"><i class="icon-ok"></i></a>
									</td> 
								<?php else: ?>    
									<td class="fail">
										<a title="Not sent" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>
								
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