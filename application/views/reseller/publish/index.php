<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/reseller-base.css');
	$loader->add('css/reseller-publish.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
				<div class="row-fluid">
					<div class="span6">
						<?php if ($vd->archive == 0): ?>
							<h1>Writing Orders</h1>
						<?php else: ?>
							<h1>Writing Orders (Archive)</h1>
						<?php endif ?>
					</div>
					<div class="span6">
						<div class="pull-right">
							<?= $ci->load->view('reseller/publish/partials/generate-code-button') ?>
						</div>
					</div>
				</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<ul id="tabs" class="nav nav-tabs <?= value_if_test($ci->input->get('terms'), 'nomarbot') ?>">
				
			<li class="active"><a data-toggle="tab"  href="#all">All Orders</a></li>
			
			<li><a data-toggle="tab" href="#no_details">No Details
			<?php if ($vd->no_details_yet_chunkination->total() > 0): ?>
				<span class="label label-info" id="iPublishNoDetailsRowCount">
					<?= $vd->no_details_yet_chunkination->total() ?>
				</span>
			<?php endif ?>
			</a></li>
			
			<li><a data-toggle="tab" href="#pending_writing">Pending Writing 
			<?php if (count($vd->prs_pending) > 0): ?>
				<span class="label label-info" id="iPublishPendingRowCount">
					<?= count($vd->prs_pending) ?>
				</span>
			<?php endif ?>
			</a></li>
			
			<li><a data-toggle="tab" href="#review">Review
			<?php if (count($vd->prs_review) > 0): ?>
				<span class="label label-info" id="iPublishReviewRowCount">
					<?= count($vd->prs_review) ?>
				</span>
			<?php endif ?>
			</a></li>
			
			<li><a data-toggle="tab" href="#rejected">Rejected                        
			<?php if (count($vd->prs_rejected) > 0): ?>
				<span class="label label-info" id="iPublishRejectedRowCount">
					<?= count($vd->prs_rejected) ?>
				</span>
			<?php endif ?>
			</a></li>
			
			<li><a data-toggle="tab" href="#assign">Assign                      
			<?php if (count($vd->prs_assign) > 0): ?>
				<span class="label label-info" id="iPublishAssignRowCount">
					<?= count($vd->prs_assign) ?>
				</span>
			<?php endif ?>
			</a></li>
			
			<li>
				<a data-toggle="tab" href="#approved">
					Approved
				</a>
			</li>
				
		</ul>
	</div>
</div>

<script>

$(function() {
	
	var hash = window.location.hash;
	if (!hash) hash = <?= json_encode("#{$vd->tab}") ?>;
	if (!hash) return;
	
	$("#tabs a").each(function() {
		var _this = $(this);
		if (_this.attr("href") == hash) {
			_this.click();
			return false;
		}
	});
	
});

</script>

<div class="row-fluid">
<div class="span12">
<div class="content">

	<div class="tab-content">

		<div id="all" class="tab-pane active">
			
			<?php if ($vd->esc($ci->input->get('terms'))): ?>
			<div class="searchResultsFilter">
				<strong>Searched By: </strong> &nbsp; "<?= $vd->esc($ci->input->get('terms')) ?>" &nbsp; &nbsp;
				<a href="reseller/publish">Clear Filter</a></div>
			<?php endif ?>
					
			<?php if (count($vd->prs_all)): ?>  
			     
				<table class="grid grid-tickboxes">
					<thead>
						<tr>
							<th class="left">Customer / Keyword</th>
							<th>Details</th>
							<th>Assign</th>
							<th>Recv'd</th>
							<th>Sent</th>
							<th>App/Rej</th>
							<th>Submit</th>
							<th>Report</th>
							<?php if ($vd->archive == 0): ?>
								<th style="width: 0">Archive</th>
							<?php endif ?>
						</tr>
					</thead>
					
					<tbody>	
						
						<?php foreach  ($vd->prs_all as $order): ?>
						<tr id="iPublishAllRow<?= $order->writing_order_code ?>">
							<td class="left">
								<span class="label-class">
									<a href="#" class="tl" title="<?= $order->writing_order_code ?>">
										<strong class="status-muted">
											<?= Model_Writing_Order_Code::__nice_code($order->writing_order_code) ?>
										</strong>
									</a>
								</span>
								<?php if ($order->status): ?>
									<?= $vd->esc($order->company_name) ?>
									<div class="marbot-5">										
										<a href="#" class="tl status-alternative" title="<?= $vd->esc($order->customer_name) ?>">
											<?= $vd->esc($order->customer_email) ?>
										</a>
										<span>|</span>
										<span class="muted"><?= $vd->esc($order->primary_keyword) ?></span>
									</div>
								<?php elseif ($order->customer_email): ?>
									<span class="status-alternative">										
										<a href="#" class="tl" title="<?= $vd->esc($order->customer_name) ?>">
											<?= $vd->esc($order->customer_email) ?>
										</a>
									</span>
								<?php else: ?>
									<span>No Details</span>
									<span>|</span>
									<span class="muted">
										<?= $order->writing_order_code ?>
									</span>
								<?php endif ?>
								<ul>
									<?php if ($order->status): ?>
									<li>
										<a target="_blank" title="<?= $vd->esc($vd->cut($order->title, 35)) ?>" class="tl"
											href="<?= $ci->website_url("view/preview/{$order->content_id}") ?>">
											Preview PR
										</a>
									</li>
									<li>
										<a target="_blank" href="writing/prdetails/edit/<?= $order->id ?>/<?= $order->writing_order_code ?>">
											Edit Order
										</a>
									</li>
									<?php endif ?>
									<?php if (!empty($order->content_id)): ?>
									<li>										
										<a target="_blank" href="reseller/publish/edit/<?= $order->content_id ?>">
											Edit Content
										</a>										
									</li>
									<?php endif ?>
								</ul>
								
							</td>
							<?php if ($order->status): ?>
							<td class="success">
								<a title="Details received on 
									<?php $dt_ordered = Date::out($order->date_ordered); ?>
									<?= $dt_ordered->format('jS M H:i') ?>"
									href="writing/prdetails/edit/<?= $order->id ?>/<?= $order->writing_order_code ?>"
									class="tl" target="_blank">
									<i class="icon-ok"></i>
								</a>
							</td>
							<?php else: ?>
							<td class="fail">
								<a title="Details not received yet" class="tl">
									<i class="icon-remove"></i></a>
							</td>
							<?php endif ?>
								
							<?php if ($order->status_num >= Model_Writing_Process::status_to_index(
								Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER )): ?>
								<td class="success">
									<a title="Assigned to writer on 
										<?php $assigned = Date::out($order->date_assigned_to_writer); ?>
										<?= $assigned->format('jS M H:i') ?>" 
										class="tl"><i class="icon-ok"></i></a>
								</td>
							<?php else: ?>
								<td class="fail">
									<a title="Not assigned to a writer" class="tl">
										<i class="icon-remove"></i></a>
								</td>
							<?php endif ?>   
							
							<?php if ($order->status_num >= Model_Writing_Process::status_to_index(
								Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER)): ?>
								<td class="success">
									<a title="Received from the writer on 
										<?php $written = Date::out($order->date_written); ?>
										<?= $written->format('jS M H:i') ?>"
										href="writing/draft/review/<?= $order->id ?>/<?= $order->writing_order_code ?>"
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
							
							<?php if ($order->status_num >= Model_Writing_Process::status_to_index(
								Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
								<td class="success">
									<a href="<?= $ci->m_reseller_details()->preview_url($order->id, $order->writing_order_code) ?>"
										<?php if (@$order->date_sent_to_customer): ?>
											title="Sent to the customer on 
											<?= Date::out($order->date_sent_to_customer)->format('jS M H:i') ?>"
										<?php endif ?>
										class="tl"><i class="icon-ok"></i></a>
								</td>
							<?php else: ?>
								<td class="fail">
									<a title="Not sent to the customer" class="tl">
										<i class="icon-remove"></i></a>
								</td>
							<?php endif ?>
							
							<?php if ($order->status_num == Model_Writing_Process::status_to_index(
								Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
								<td class="fail">
									<a title="Waiting for a response from the customer." class="tl">
										<i class="icon-remove"></i></a>
								</td>
							<?php elseif ($order->status_num >= Model_Writing_Process::status_to_index(
								Model_Writing_Order::STATUS_SENT_TO_CUSTOMER)): ?>
								<td class="success <?= value_if_test($order->status_num == 
									Model_Writing_Process::status_to_index(Model_Writing_Order::STATUS_CUSTOMER_REJECTED), 
									'success-rejected') ?>">
									<?php if ($order->status_num >= Model_Writing_Process::status_to_index(
											Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED)): ?>
										<a class="tl" title="Approved on 
											<?php $approved = Date::out($order->date_customer_approved); ?>
											<?= $approved->format('jS M H:i') ?>">
											<i class="icon-ok"></i>
										</a>
									<?php elseif ($order->status_num == Model_Writing_Process::status_to_index(
										Model_Writing_Order::STATUS_CUSTOMER_REJECTED)): ?>
										<a class="tl" title="Rejected on 
											<?php $rejected = Date::out($order->date_customer_rejected); ?>
											<?= $rejected->format('jS M H:i') ?>">
											<i class="icon-ban-circle"></i>
										</a>
									<?php endif ?>
								</td>
							<?php else: ?>
								<td class="fail">
									<a title="Not approved/rejected" class="tl">
										<i class="icon-remove"></i></a>
								</td>
							<?php endif ?>  
						
							<?php if ($order->is_published): ?>
								<td class="success">
									<a title="Published on 
										<?php $publish = Date::out($order->date_publish); ?>
										<?= $publish->format('jS M H:i') ?>" class="tl" 
										href="<?= $ci->website_url("view/preview/{$order->content_id}") ?>"
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
							
							<?php if ($order->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
										STATUS_CUSTOMER_ACCEPTED) && @$order->date_report_sent): ?>
								<td class="success">
										<a title="Report generated on 
											<?php $rsent = Date::out($order->date_report_sent); ?>
											<?= $rsent->format('jS M H:i') ?>"
										class="tl" href="reseller/publish/report/<?= $order->content_id ?>"
										target="_blank"><i class="icon-ok"></i></a>
								</td> 
							<?php else: ?>    
								<td class="fail">
									<a title="Not generated" class="tl"><i class="icon-remove"></i></a>
								</td>
							<?php endif ?>
								
							<?php if ($vd->archive == 0): ?>
							<td>
							<?php 
								$tab = "";
								if ($order->is_used == 0)
									$tab = "";
								if ($order->id == ""):
										$tab = "iPublishNoDetailsRow";
								elseif ($order->status == 'assigned_to_writer' || 
												$order->status == 'writer_request_details_revision' || 
												$order->status == 'sent_back_to_writer' || 
												$order->status == 'sent_to_customer_for_detail_change' ||
												$order->status == 'customer_revise_details' || 
												$order->status == 'revised_details_accepted'): 
										$tab = "iPublishPendingRow";	
								elseif ($order->status == 'written_sent_to_reseller'): 
										$tab = "iPublishReviewRow";	
								elseif ($order->status == 'reseller_rejected' || 
												$order->status == 'customer_rejected' || 
												$order->status == 'sent_to_customer'): 
										$tab = "iPublishRejectedRow";	
								elseif ($order->writer_id == 0 && $order->is_used == 1):
									$tab = "iPublishAssignRow";	
								elseif ($order->slug!=""):
									$tab = "iPublishApprovedRow";
																										
								endif;	
																			
							?>
								<a class="archive-button" id="ch1_<?= $order->writing_order_code ?>"
									onclick="mark_for_archiving('<?= $order->writing_order_code ?>',
									'<?= $tab ?>','ch1_<?= $order->writing_order_code ?>')">
									<img src="<?= $vd->assets_base ?>im/fugue-icons/folder-zipper.png" />
								</a>
							</td>
							<?php endif ?>
						</tr>
						<?php endforeach ?>  
						
					</tbody>
				</table>
				
				<div class="grid-report">Displaying <?= count($vd->prs_all) ?> 
					of <?= $vd->chunkination->total() ?> Items</div>                                
				<?= $vd->chunkination->render() ?>
				
			<?php else: ?>
			
				<div class="no-order-results">
					No orders to display.
				</div>
			
			<?php endif ?>
			
			<?php if ($vd->archive == 0): ?>
			<div class="grid-report"><a href="reseller/publish/archive">View Archive</a></div>
			<?php endif ?>
			
		</div>
		
		<div id="no_details" class="tab-pane">
			<?php if (count($vd->no_details_yet)): ?>        
			<table class="grid" id="iPublishNoDetailsRowTable">
				<thead>
					<tr>
						<th class="left">Customer</th>
						<th>Code</th>
						<th>Ordered</th>
						<th>Contacted Last</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($vd->no_details_yet as $order): ?>
					<tr id="iPublishNoDetailsRow<?= $order->writing_order_code ?>">
						<td class="left">
							<?php if ($order->customer_name): ?>
								<?= $vd->esc($order->customer_name) ?><br />
							<?php endif ?>    
							<span class="muted">
								<?php if ($order->customer_email): ?>
									<?= $vd->esc($order->customer_email) ?>
								<?php else: ?>
								<span>Unknown</span>
								<?php endif ?>
							</span>
						</td>
						<td>
							<?= $vd->esc($order->writing_order_code) ?>
						</td>
						<td>
							<?php $ordered = Date::out($order->date_ordered); ?>
							<?= $ordered->format('jS M H:i') ?>
						</td>
						<td>
							<?php if ($order->date_last_reminder_sent != "0000-00-00 00:00:00"): ?>
								<?php $rem_sent = Date::out($order->date_last_reminder_sent); ?>
								<?= $rem_sent->format('jS M H:i') ?>
							<?php else: ?>
								N/A
							<?php endif ?>
						</td>
						<td>
							<?php if ($order->customer_email): ?>
							<a href="/reseller/publish/send_reminder_email/<?= $order->id ?>
							<?= value_if_test($vd->archive, '/archived') ?>">Send Reminder Email</a>
							<?php else: ?>
							<span class="status-muted">Send Reminder Email</span>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>  
				</tbody>
			</table>			
			<div class="grid-report">Displaying <?= count($vd->no_details_yet) ?> 
				of <?= $vd->no_details_yet_chunkination->total() ?> Items</div>
			<?= $vd->no_details_yet_chunkination->render() ?>			
			<?php else: ?>
			<div class="no-order-results">
				No orders waiting for details.
			</div>
			<?php endif ?>
		</div>
		
		<div id="pending_writing" class="tab-pane">
			<?php if (count($vd->prs_pending)): ?>
				<table class="grid" id="iPublishPendingRowTable">
				<thead>
					<tr>
							<th class="left" style="width:200px;">Customer / Keyword</th>
							<th>Code</th>
							<th>Ordered</th>
							<th>Assigned</th>
							<th>Details</th>
							<th style="width:180px;">Action</th>
					</tr>
				</thead>
				<tbody>	
					<?php foreach  ($vd->prs_pending as $order): ?>
					<tr id="iPublishPendingRow<?= $order->writing_order_code ?>">
						<td class="left">
							<?= $vd->esc($order->company_name) ?> (<?= $vd->esc($order->primary_keyword) ?>)
						</td>
						<td>
							<?= $vd->esc($order->writing_order_code) ?>
						</td>
						<td>
							<?php $assigned = Date::out($order->date_assigned); ?>
							<?= $assigned->format('jS M') ?>
						</td>
						<td>
							<?php $assigned = Date::out($order->date_assigned); ?>
							<?= $assigned->format('jS M') ?>
						<td>												
							<a href="writing/prdetails/edit/<?= $order->id ?>/<?=
								$order->writing_order_code ?>" target="_blank">Edit Details</a>
						</td>
						<td class="pending_log_td">
							<?php if ($order->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
								Sent to the Customer					
							<?php elseif ($order->status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS): ?>
								<a href="#" data-id="<?= $order->id ?>">Customer Replied/Revised Their PR Details</a>
							<?php elseif ($order->status == Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION): ?>
								<a href="#" data-id="<?= $order->id ?>">Writer Wrote to You</a>
							<?php else: ?>
								Not yet written
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach ?>  
				</tbody>
			</table>
			<?php else: ?>
			<div class="no-order-results">
				No orders pending writing.
			</div>
			<?php endif ?>
		</div>
		
		<div id="review" class="tab-pane">
			<?php if (count($vd->prs_review)): ?>        
			<table class="grid" id="iPublishReviewRowTable">
				<thead>
					<tr>
						<th class="left">Customer / Keyword</th>
						<th>PR Code</th>
						<th>Ordered</th>
						<th>Assigned</th>
						<th>Received</th>
						<th>Details</th>
						<th>PR Link</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach  ($vd->prs_review as $order): ?>
					<tr id="iPublishReviewRow<?= $order->writing_order_code ?>">
							<td class="left">
								<?= $vd->esc($order->company_name) ?> | 
								<?= $vd->esc($order->primary_keyword) ?><br />
									<a href='writing/draft/review/<?= $order->id ?>/<?=
									$order->writing_order_code ?>' target='_blank'>
								<?= $vd->esc($vd->cut($order->title, 35)) ?></a>
							</td>
							<td><?= $order->writing_order_code ?></td>
							<td>
								<?php $ordered = Date::out($order->date_ordered); ?>
								<?= $ordered->format('jS M') ?>
							</td>
							<td>
								<?php $assigned = Date::out($order->date_assigned_to_writer); ?>
								<?= $assigned->format('jS M') ?>											
							</td>
							<td>
								<?php $submitted = Date::out($order->date_submitted_by_writer); ?>
								<?= $submitted->format('jS M') ?>			
							</td>
							<td>												
								<a href="writing/prdetails/edit/<?= $order->id ?>/<?=
									$order->writing_order_code ?>" target="_blank">Edit PR Details</a>										                                          
							</td>
							<td>
								<a href='writing/draft/review/<?= $order->id ?>/<?=
									$order->writing_order_code ?>' target='_blank'>Review PR</a>
							</td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php else: ?>
			<div class="no-order-results">
				No orders to review.
			</div>
			<?php endif ?>
		</div>
		
		
		<div id="rejected" class="tab-pane">
			<?php if (count($vd->prs_rejected)): ?>
			<table class="grid" id="iPublishRejectedRowTable">
				<thead>
					<tr>
							<th class="left">Customer / Keyword</th>
							<th>PR Code</th>
							<th style="padding-right:10px;">Assigned</th>
							<th>Received</th>
							<th>Details</th>
							<th>Status</th>
							<th style="padding-right:7px;">Rejection Log</th>
							<th>PR Link</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach  ($vd->prs_rejected as $order): ?>
					<tr id="iPublishRejectedRow<?= $order->writing_order_code ?>">
							<td class="left" style="padding-right:30px !important;">
								<?= $vd->esc($order->company_name) ?> 
								(<?= $vd->esc($order->primary_keyword) ?>)<br />
								<a href='writing/draft/review/<?= $order->id ?>/<?=
									$order->writing_order_code ?>' target='_blank'>
							<?= $vd->esc($vd->cut($order->title, 35)) ?></a>
							</td>
							<td><?= $order->writing_order_code ?></td>
							<td>
								<?php $assigned = Date::out($order->date_assigned_to_writer); ?>
								<?= $assigned->format('jS M H:i') ?>
							</td>
							<td>
								<?php $submitted = Date::out($order->date_submitted_by_writer); ?>
								<?= $submitted->format('jS M H:i') ?>
							</td>
							<td>
								<a href="writing/prdetails/edit/<?= $order->id ?>/<?=
									$order->writing_order_code ?>" target="_blank">Edit PR Details</a>
							</td>
							<td>
								<?= $order->status_title ?>
							</td>
							<td class="rejected_log_td">
						<a href="#" data-id="<?= $order->id ?>">Log</a>
							</td>
							<td>
								<a href='writing/draft/review/<?= $order->id ?>/<?=
									$order->writing_order_code ?>' target='_blank'>Review PR</a>
							</td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php else: ?>
			<div class="no-order-results">
				No rejected orders.
			</div>
			<?php endif ?>
		</div>
		
		<div id="assign" class="tab-pane">
			<?php if (count($vd->prs_assign)): ?>        
			<form id="AssignForm" name="AssignForm" method="post" 
				action="reseller/publish/assign_to_writer<?= value_if_test($vd->archive, '/archived') ?>">
				<table class="grid" id="iPublishAssignRowTable">
					<thead>
						<tr>
								<th class="left">Customer</th>
								<th>Code</th>
								<th>Details Received</th>
								<th>Writer</th>
								<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach  ($vd->prs_assign as $order): ?>
						<tr id="iPublishAssignRow<?= $order->writing_order_code ?>">
							<td class="left">
								<?= $vd->esc($order->company_name) ?> 
								(<?= $vd->esc($order->primary_keyword) ?>)
							</td>
							<td><?= $vd->esc($order->writing_order_code) ?></td>
							<td>
								<?php $ordered=Date::out($order->date_ordered); ?>
								<?= $ordered->format('jS M H:i') ?>
							</td>
							<td>
								<div>
									<div class="btn-group dd-menu-nav">
										<a class="btn dropdown-toggle" id="selectWriter<?= $order->id?>" 
											data-toggle="dropdown" href="#" 
											style="padding:0; background:none; border:0; border-radius:0;
											border-shadow:0; box-shadow: none !important; color:#5F93C7; 
											font-weight:bold;">
											Select Writer
											<span class="caret"></span>
										</a>								
										<ul class="dropdown-menu" style="height: 200px; overflow: auto;">
											<?php foreach  ($vd->writers as $w): ?>
											<li style=" text-align: left;border-left:0px;">
												<a href="javascript: void(0);" 
												onclick="selectWriterId(<?= $order->id ?>,<?= $w->id ?>,
												'<?= $vd->esc($w->first_name) ?> <?= $vd->esc($w->last_name) ?> ')"
													><?= $vd->esc($w->first_name)." ".$vd->esc($w->last_name) ?> </a>
											</li>
											<?php endforeach ?>
										</ul>
									</div>
								</div>
							</td>
							<td>
								<input type="hidden" name="writer[]" id="writer<?= $order->id ?>" value="0" />
								<a class="bt-publish bt-orange btn-small" href="javascript:void(0);" 
									onclick="postAssignForm(<?= $order->id ?>);">Assign</a>
							</td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<input type="hidden" name="selected_writer" id="selected_writer" value="0" />
				<input type="hidden" name="selected_pr" id="selected_pr" value="0" />
			</form>
			<?php else: ?>
			<div class="no-order-results">
				No orders to assign.
			</div>
			<?php endif ?>
		</div>
		
		<div id="approved_scheduled" class="tab-pane">
				<?php if (count($vd->approved)): ?>       
				<table class="grid" id="iPublishApprovedRowTable">
					<thead>
						<tr>
							<th class="left">Customer / Keyword</th>
							<th>Code</th>
							<th>Ordered</th>
							<th>Published</th>
							<th>Status</th>
							<?php if ($vd->archive == 0): ?>
								<th>Archive</th>
							<?php endif ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach  ($vd->approved as $order): ?>
						<tr id="iPublishApprovedRow<?= $order->writing_order_code ?>">
								<td class="left">
							<?= $vd->esc($order->company_name) ?> | <?= $vd->esc($order->primary_keyword) ?>
									<ul>                            
										<li>
												<a target="_blank" href="<?= $ci->common()->url($order->url) ?>">
										<?= $vd->esc($vd->cut($order->title, 50)) ?>
												</a>
										</li>
									</ul>
									<br />
									<a href="<?= $ci->common()->url($order->url) ?>" target="_blank">View</a> | 
									<a href="reseller/publish/edit/<?= $order->content_id ?>">Edit</a>
										
								</td>
								<td>
									<?= $order->writing_order_code ?>
								</td>
								<td>
									<?php $dt_ordered = Date::out($order->date_ordered) ?>
									<?= $dt_ordered->format('jS M') ?>
								</td>
								<td>
									<?php if ($order->is_published): ?>
									<?php $dt_publish = Date::out($order->date_publish) ?>
									<?= $dt_publish->format('jS M') ?>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
								<td>
									<?php if ($order->is_published): ?>
									<span>Published</span>
									<?php elseif ($order->is_under_review): ?>
									<span>Under Review</span>
									<?php elseif ($order->is_draft): ?>
										<span>Draft</span>
										<?php if ($order->is_rejected): ?>
										<div class="status-false smaller">Rejected</div>
										<?php endif ?>
									<?php else: ?>
										<span>Scheduled</span>
									<?php endif ?>
								</td>
								
								<td>
									<?php if ($vd->archive == 0): ?>
									<a class="archive-button" id="ch2_<?= $order->writing_order_code ?>"
										onclick="mark_for_archiving('<?= $order->writing_order_code ?>',
											'iPublishApprovedRow','ch2_<?= $order->writing_order_code ?>')">
										<img src="<?= $vd->assets_base ?>im/fugue-icons/folder-zipper.png" />
									</a>
									<?php endif ?>
								</td>
						</tr>	
						<?php endforeach ?>									
					</tbody>
				</table>
				
				<div class="grid-report">Displaying <?= count($vd->approved) ?> 
					of <?= $vd->approved_chunkination->total() ?> Items</div>                                
				<?= $vd->approved_chunkination->render() ?>
					
				<?php else: ?>
					<div class="no-order-results">
						No approved PRs.
					</div>
				<?php endif ?>
		</div>
	</div>
</div>
</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<?= $this->load->view('reseller/publish/partials/archive_success_modal_box', null, true) ?>
<?= $this->load->view('reseller/publish/partials/archive_confirm_box', null, true) ?>
	
<script>
$(function() {
	$(".rejected_log_td a").on("click", function(ev) {
		
		ev.preventDefault();
		var id = $(this).data("id");
		var content_url = "reseller/publish/rejection_log/" + id;
		var footer_url = "reseller/publish/rejection_log_footer/" + id;
		var modal = $("#<?= $vd->rej_log_modal_id ?>");
		
		var modal_footer = modal.find(".modal-footer");
		
		if (modal_footer.length == 0) {
			$("#<?= $vd->rej_log_modal_id ?>").append( "<div class='modal-footer'></div>" );;
			modal_footer = modal.find(".modal-footer");
		}
		
		modal_footer.load(footer_url);		
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {	
			modal.modal('show');
		});
		
	});
	
	
	$(".pending_log_td a").on("click", function(ev) {
		
		ev.preventDefault();
		var id = $(this).data("id");
		var content_url = "reseller/publish/pending_log/" + id;
		var footer_url = "reseller/publish/pending_log_footer/" + id;
		var modal = $("#<?= $vd->pending_log_modal_id ?>");
		
		var modal_footer = modal.find(".modal-footer");
		
		if (modal_footer.length == 0) {
			$("#<?= $vd->pending_log_modal_id ?>").append("<div class='modal-footer'></div>");
			modal_footer = modal.find(".modal-footer");
		}
		
		modal_footer.load(footer_url);
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {	
			modal.modal('show');
		});
		
	});
	
	function selectWriterId(prId,writerId,writerName)
	{
		document.getElementById("selectWriter" + prId).innerHTML=writerName + '<span class="caret"></span>';
		document.getElementById("writer" + prId).value=writerId;
	}
	
	function postAssignForm(prID)
	{
		document.getElementById("selected_pr").value=prID;
		document.getElementById("selected_writer").value=document.getElementById("writer" + prID).value;   
		document.getElementById("AssignForm").submit();
	}
	
	function set_pr_order_id_for_hidden_var(id)
	{
		document.getElementById("pr_id_for_action").value=id;
	}
	
	var archive_tcode='';
	var archive_tab_row='';
	var arch_chbox_id='';	
	
	function mark_for_archiving(transactionCode, tabRow, chbox_id) {
		archive_tcode= transactionCode;
		archive_tab_row= tabRow;
		arch_chbox_id= chbox_id;
		var modal = $("#archive_confirm_modal");
		modal.modal("show");
	}
	
	function archive_pr_writing() {
		var transactionCode = archive_tcode;
		var tabRow = archive_tab_row;		
		var confirm_modal = $("#archive_confirm_modal");
		confirm_modal.modal('hide');		
		var modal = $("#archiveSuccessModal");		
		var rowID="iPublishAllRow" + transactionCode;
		var tabRowID=tabRow + transactionCode;
		$.ajax({
			url: 'reseller/publish/mark_archived/' + transactionCode,
			context: document.body
			}).done(function() {
				modal.modal("show");
				$("#" + rowID).slideUp(100);
				if (tabRow!="") {
					$("#" + tabRowID).slideUp(100);
					var tCountString=document.getElementById(tabRow + "Count").innerHTML;
					if (tCountString!="")
					{
						var tCount=parseInt(tCountString);
						tCount--;
						if (tCount>0)
							document.getElementById(tabRow + "Count").innerHTML=tCount;
						else
						{
							document.getElementById(tabRow + "Count").innerHTML="";
							document.getElementById(tabRow + "Table").innerHTML="<tr><td><center>Nothing to Display<center></td></tr>";
						}	
							
					}
				}		
		});		
	}
});
		
</script>