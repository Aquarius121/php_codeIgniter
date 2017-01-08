<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/reseller-base.css');
	$loader->add('css/reseller-dashboard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<aside class="span3 aside aside-left">
		
		<!-- <section class="aside-block aside-pr-views">
			<h3 align='center'>Account Credits</h3>            
			<div class="aside-content aside-content-border">                
				<ul class="aside-pr-list">
					<li>
						<a href="javascript: void(0);">
							<span class="aside-pr-content-value">
								<?= $vd->wr_credits_available ?> of <?= $vd->wr_credits_total ?>
							</span>
						</a>
						<span class="aside-pr-content-label">Press Release Writing</span>
					</li>
					
					<li>
						<a href="javascript: void(0);"><span class="aside-pr-content-value">5 of 10</span></a>
						<span class="aside-pr-content-label">Press Release Distribution</span>
					</li>
				</ul>
				
				<a href="#" class="btn">Buy Credits</a>
			</div>
		</section> -->	
				
		<section class="aside-block">
			<h3 align='center'>Orders Overview</h3>			
			<div class="aside-content aside-content-border"  style="padding: 0 !important;">                
				<ul class="aside-pr-list">
					<li>
						<span class="aside-pr-content-value status-info">
							<?= $vd->this_week_orders_count ?>
						</span>
						<span class="aside-pr-content-label">Orders For The Week</span>
					</li>					
					<li>
						<span class="aside-pr-content-value status-info">
							<?= $vd->no_details_count ?>
						</span> 
						<span class="aside-pr-content-label">Pending PR Details</span>
					</li>
				</ul>
			</div>
		</section>
	</aside>
				
	<div class="span9 main" style="width:72% !important;">
		<div class="content">
			<form method="post" name="AssignForm" id="AssignForm" action="reseller/dashboard/assign_to_writers">
			<section class="membership-level-reseller">
				<h2>Membership Level <strong>Reseller</strong></h2>
				<header class="ao-header" style="padding:0 !important;">
					<div class="row-fluid">
						<div class="span12">
							<h3>PR Status</h3>
						</div>
					</div>
				</header>
				
				<div class="rm-tabs-block">
					<ul class="nav nav-tabs row-fluid">
						<li class="span3" style="width:30% !important;">
							<a data-toggle="tab" href="#rm_pending">Pending Writing 
								<?php if (@$vd->pending_counter > 0): ?>
									<span class="label label-info">
										<?php echo $vd->pending_counter; ?>
									</span>
								<?php endif?>
							</a>
						</li>
						
						<li class="active span3">
							<a data-toggle="tab" href="#rm_review">Review 
								<?php if (@$vd->review_counter > 0): ?>
									<span class="label label-info">
										<?php echo $vd->review_counter; ?>
									</span>
								<?php endif?>
							</a>
						</li>
							
						<li class="span3" style="width:20% !important;">
							<a data-toggle="tab" href="#rm_rejected">Rejected 
								<?php if (@$vd->rejected_counter > 0): ?>
									<span class="label label-info">
										<?php echo $vd->rejected_counter; ?>
									</span>
								<?php endif?>     
							</a>
						</li>
						
						<li class="span3">
							<a data-toggle="tab" href="#rm_assign">Assign
								<?php if (@$vd->assign_counter > 0): ?>
									<span class="label label-info">
										<?php echo $vd->assign_counter; ?>
									</span>
								<?php endif?>  
							</a>
						</li>
						
					</ul>
					
					<div class="tab-content"  style="overflow:inherit !important;">
						<div id="rm_review" class="tab-pane active">
							<?php if (count($vd->prs_review)): ?>
								<ul class="rm-list">
								<?php foreach ($vd->prs_review as $pr): ?>
									<li>
										<div class="row-fluid">
											<div class="span5">
												<?php echo $pr->company_name;?> 
												(<?php echo $pr->primary_keyword;?>)
											</div>
											
											<div class="span5">
												<?php echo $vd->esc($pr->title_short); ?>
											</div>
											
											<div class="span2" style="text-align: right;">
												<a href='<?= ('writing/draft/review/'.
														$pr->id.'/'.$pr->writing_order_code); ?>' 
														target='_blank'>View &raquo;</a>
											</div>
										</div>
									</li>
								 <?php endforeach ?>  
								</ul>
							<?php else: ?>
								<br /><br /><br />
								<div align="center">
									No PRs to review.
								</div>
								<br /><br /><br />
							<?php endif ?>
							
						</div>
						
						<div id="rm_pending" class="tab-pane">
							<?php if (count($vd->prs_pending)): ?>    
							<ul class="rm-list">
							 <?php foreach ($vd->prs_pending as $pr): ?>
								<li>
									<div class="row-fluid">
										<div class="span7">
											<?= $vd->esc($pr->company_name) ?> 
											(<?= $vd->esc($pr->primary_keyword) ?>)
										</div>
										
										<div class="span3">
											<?= $vd->esc($pr->writing_order_code) ?>
										</div>  
																						  
										<div class="span2" style="text-align:right;">
											<a href="<?= ('writing/prdetails/edit/'.
												$pr->id.'/'.$pr->writing_order_code);?>" 
												target="_blank">Edit Details&raquo;</a>
										</div>
									</div>
								</li>	
							<?php endforeach ?>
							</ul>
							<?php else: ?>
								<br /><br /><br /> 
								<div align="center">
									No pending PR writing task.
								</div>
								<br /><br /><br /> 
							<?php endif ?>
						</div>
						
						<div id="rm_rejected" class="tab-pane">
							<?php if (count($vd->prs_rejected)): ?>    
							<ul class="rm-list">
								<?php foreach ($vd->prs_rejected as $pr): ?>
								<li>
									<div class="row-fluid">
										<div class="span7">
											<?= $vd->esc($pr->company_name) ?><br />
											<a href='<?= ('writing/draft/review/'.
												$pr->id.'/'.$pr->writing_order_code);?>' 
												target='_blank'><?= $vd->esc($pr->title_short) ?></a>
										</div>
										
										<div class="span3">
											<?php echo $pr->writing_order_code; ?>
										</div>
										
										<div class="span2">
											 <a href='<?= ('writing/draft/review/'.
												$pr->id.'/'.$pr->writing_order_code);?>' 
												target='_blank'>Review PR&raquo;</a>
										</div>
									</div>
								</li>
								<?php endforeach ?>  
							</ul>
							<?php else: ?>
								<br /><br /><br /> 
								<div align="center">
									No rejected PR writing task.
								</div>
								<br /><br /><br /> 
							<?php endif ?>										
						</div>
						
						
						<div id="rm_assign" class="tab-pane">
							<?php if (count($vd->prs_assign)): ?>    
							<ul class="rm-list">
								<?php foreach ($vd->prs_assign as $w_order): ?>
								<li>
									<div class="row-fluid">
										<div class="span5">
											<?= $vd->esc($w_order->company_name) ?> 
											(<?= $vd->esc($w_order->primary_keyword) ?>)
										</div>
										<div class="span2 text-center">
											<?php echo $w_order->writing_order_code; ?>
										</div>
										<div class="span2 text-center">
											<?= $vd->esc($w_order->date_ordered) ?>
										</div>
									
										<div class="span3" style="padding-left:40px;">
											<div class="btn-group dd-menu-nav">
												<a class="btn dropdown-toggle" 
													id="selectWriter<?=$w_order->id?>" 
													data-toggle="dropdown" href="#">
													Select Writer
													<span class="caret"></span>
												</a>
												
												<ul class="dropdown-menu" style="height: 200px; overflow:auto;">
													<?php foreach ($vd->writers as $writer): ?>
													<li>
														<a href="javascript: void(0);" 
															onclick="select_writer_id(<?= $w_order->id ?>,
															<?= $writer->id ?>,
															'<?= $vd->esc($writer->first_name) ?> ')">
															<?=$vd->esc($writer->first_name)." ". 
																$vd->esc($writer->last_name);?></a>
													</li>
													<?php endforeach ?>
												</ul>
											   <input type="hidden" name="writer[]" 
													id="writer<?= $w_order->id ?>" value="0" />
											</div>														
										</div>
									</div>
								</li>
								
							<input type="hidden" name="prID[]" id="prID<?= $w_order->id ?>"  
								value="<?= $w_order->id ?>" />   
							<?php endforeach ?>
							<br />
							<div class="pull-right">									
							  <a class="bt-publish bt-silver" href="javascript:void(0);" 
								onclick="post_assign_form();">Bulk Assign</a>
							 </div>
							<br /><br />
							</ul>
							<?php else: ?>
								<br /><br /><br /> 
								<div align="center">
									No PR writing task to assign.
								</div>
								 <br /><br /><br /> 
							<?php endif ?>	                            
						</div>
					</div>
				</div>
			</section>
			</form>

<!-- Latest Activites -->
			<section class="latest-activites">
				<header class="ao-header">
					<div class="row-fluid">
						<div class="span12">
							<h3>Latest Activites</h3>
						</div>
					</div>
				</header>
			
				<div class="rm-tabs-block">
				<div class="og-header row-fluid">
					<div class="span5 text-left" style="padding-left:20px;">Action</div>
					<div class="span2 text-left" style="margin-left:0;">Date</div>
					<div class="span3 text-left">Code</div>
					<div class="span2">&nbsp;</div>
				</div>
				
				
					<div class="tab-content" style="overflow:inherit !important;">
						<div id="rm_review" class="tab-pane active" style='min-height:240px;'>
							<?php if (count($vd->activities)): ?>    
							<ul class="rm-list" style="min-height:200px;">
								<?php 
								$cnt=0;
								$k=1;
								foreach ($vd->activities as $pr): 											
								if ($cnt%5 == 0 and $cnt > 1) : 
									echo "</span>" ;
									echo "<span id='activities_span$k' style='display:none;'>"; 
									$k++;
								elseif ($cnt%5 == 0): 
									echo "<span id='activities_span$k'>"; 
									$k++;
								endif;
								$cnt++;											
								?>                                            
								<li  style="background:none;">
									<div class="row-fluid">
										<div class="span5">
											<?= $pr['caption'] ?>
										</div>
										<div class="span2">
											<?= $pr['dt'] ?>
										</div>
										<div class="span3">
											<?= $pr['code'] ?>
										</div>
										<div class="span2">
											<?php if ($pr['caption'] == "Submitted by Writer"): ?>
												<?php if (@$pr['content_slug']): ?>
													<a href="<?= ('view/'.$pr['content_slug']);
														?>" class="pull-right" target="_blank"> View &raquo;</a>
												
												<?php else: ?>
													 <a href="<?= ('writing/draft/review/'.
														 $pr['orderID'].'/'.$pr['code']);?>" 
														 class="pull-right" target="_blank"> View &raquo;</a>
												<?php endif;?>
																								
											<?php elseif (trim($pr['caption']) == "New Order Placed" 
													|| trim($pr['caption']) == "Press Release Details"): ?>
													<a href="#activity_detail<?= $cnt ?>" 
														data-toggle="modal" class="pull-right"> View &raquo;</a>
											<?php elseif (substr(trim($pr['caption']),0,11) == "Rejected by") : ?>
													<a href="#activity_detail<?= $cnt ?>" data-toggle="modal" 
														class="pull-right"> View &raquo;</a>
											<?php elseif (trim($pr['caption']) == "Assigned to Writer"): ?>
													<a href="#activity_detail<?= $cnt ?>" data-toggle="modal" 
														class="pull-right"> View &raquo;</a>
											<?php elseif ((trim($pr['caption']) == "Press Release Published" ||
													 trim($pr['caption']) == "Press Release Approved")
													 && !empty($pr['url'])): ?>
													<a href="<?= $pr['url'] ?>" class="pull-right" 
														target="_blank"> View &raquo;</a>
											<?php endif?>        
										</div>
									</div>
								</li>
								<?                                           
								endforeach;
								$k--;
								?>  
								</span>
							</ul>
							
							
							<footer class="ao-footer">
								<div class="row-fluid">
									<div id="activities_previous_button" class="span11" 
										style="display: block; text-align:left; float:left;">&nbsp;</a>
									</div>
									<div id="activities_next_button" style="float:left;">
										<a href="javascript: void(0)" class="pull-right" 
											onclick="next_activities(<?=$k?>);" 
											style="padding-left:15px;">Next &raquo;</a>
									</div>
								</div>
							</footer>
							<?php else: ?>
								<br /><br /><center>No recent activity</center>
							<?php endif ?>
						</div>
					</div>
				</div>
			
			</section>
		</div>
	</div>


</div>       
			
<div class="container">
	<div class="row-fluid">		
		<div class="span12 main">
			<div class="content">
<!-- Reseller Overview -->
				<section class="latest-activites">
					<header class="ao-header">
						<div class="row-fluid">
							<div class="span6">
								<h3>Reseller Overview</h3>
							</div>
							<div class="span6">
								<ul class="sub-header-menu">
									<li><a href="reseller/publish/archive">Archived</a></li>
									<li><a href="reseller/publish">View All &raquo;</a></li>
								</ul>
							</div>
						</div>
					</header>							
			   
				<div id="all" class="tab-pane active">
					<?php if (count($vd->prs_all)): ?>        
					<table class="grid overview-table grid-tickboxes">
						<thead>
							<tr>
								<th class="left" style="width:32%; padding-left:20px !important;">Customer / Keyword</th>
								<th>Code</th>
								<th>Details</th>
								<th>Assigned</th>
								<th>Received</th>
								<th>Sent</th>
								<th>Approved</th>
								<th>Published</th>
								<th>Report</th>											
							</tr>
						</thead>
						<tbody>
							 <?php foreach ($vd->prs_all as $pr): ?>																																					
							 <tr id="iPublishAllRow<?= $pr->writing_order_code ?>">                                         
								<td class="left" style="width:180px;">
									<?php if ($pr->status): ?>													
										<?php echo $vd->esc($pr->company_name);?> 
										<div class="muted"><?php echo $vd->esc($pr->primary_keyword);?></div>
									<?php else: ?>
										<?php echo $vd->esc($pr->customer_name);?>
									<?php endif?>    
									
									<?php if ($pr->status == "approved") : ?>
										<ul>
											<li>
												<a target="_blank" href="<?= $ci->common()->url('view/'.
													 $pr->slug)?>">
													 <?= $vd->esc($vd->cut($pr->pr_title, 40)) ?></a></li>
										
										</ul>
									<?php endif ?>
								</td>
								
								<td>
									<?= $pr->writing_order_code ?>
								</td>
								
								<?php if ($pr->status): ?>
								<td class="success">	
									<a title="Details received on <?= $vd->esc($pr->date_ordered) ?>" 
										class="tl" 
										href="<?=$ci->common()->url('writing/prdetails/edit/'.$pr->id.'/'.
											$pr->writing_order_code);?>" target="_blank">
											<i class="icon-ok"></i></a>
								</td>
								<?php else: ?>
								 <td class="fail">
									<a title="Details not received yet" class="tl">
										<i class="icon-remove"></i></a>
								</td>
								<?php endif?>
								
								<?php if ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_ASSIGNED_TO_WRITER )): ?>
									<td class="success">
										<a title="Assigned to writer on 
											<?php $assigned=Date::out($pr->date_assigned_to_writer); ?>
											<?=$assigned->format('m/j');	?>" 
											class="tl"><i class="icon-ok"></i></a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not yet assigned to a writer" class="tl">
										<i class="icon-remove"></i></a>
									</td>
								<?php endif ?> 
																			   
								<?php if ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_WRITTEN_SENT_TO_RESELLER)): ?>
									<td class="success">
										<a title="Received from the writer on 
										<?php $wrtitten=Date::out($pr->date_written);?>
										<?=$wrtitten->format('m/j');	?>"
										  href='<?= $ci->common()->url('writing/draft/edit/'.$pr->id.'/'
											.$pr->writing_order_code);?>' target='_blank' class="tl">
											<i class="icon-ok"></i></a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not yet received from the writer" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>  
								
								  
								<?php if ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="success">
										<a 
										<?php if (@$pr->date_sent_to_customer): ?>
											title="Sent to the customer on 
											<?php $sent=Date::out($pr->date_sent_to_customer);?>
											<?=$sent->format('m/j');	?>" 
										<?php endif ?>    
										 class="tl"><i class="icon-ok"></i></a>
									</td>
								<?php else: ?>
									<td class="fail">
										<a title="Not yet sent to the customer" class="tl">
										<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>                                
							   
								<?php if ($pr->status_num == Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="fail">
										<a title="Waiting for a response from the customer." class="tl">
										<i class="icon-remove"></i></a>
									</td>
									
								 <?php elseif ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_SENT_TO_CUSTOMER)): ?>
									<td class="success">
										<?php if ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
													STATUS_CUSTOMER_ACCEPTED)): 
												$msg = "Approved on ";
												$approved = Date::out($pr->date_customer_approved);
												$msg .= $approved->format('m/j');
											elseif ($pr->status_num == Model_Writing_Process::status_to_index(Model_Writing_Order::
													STATUS_CUSTOMER_REJECTED)): 
												$msg = "Rejected on ";
												$rejected = Date::out($pr->date_customer_rejected);
												$msg .= $rejected->format('m/j');
											endif
										?>
										<a title="<?php echo $msg;?>" class="tl">
											<i class="icon-ok"></i></a>
									</td>
									
								 <?php else: ?>
									<td class="fail">
										<a title="Not yet approved/rejected" class="tl">
											<i class="icon-remove"></i></a>
									</td>
								<?php endif ?>    
								
								<?php if ($pr->is_published == 1): ?>
									<td class="success">
										<a title="Published successfully on 
											<?php 	$publish=Date::out(@$pr->date_publish); ?>
											<?= 	$publish->format('m/j') ?>
												" class="tl" 
												href="<?= $ci->common()->url('view/'.$pr->slug) ?>" 
													target="_blank"><i class="icon-ok"></i></a>
									</td>                                              
								<?php else: ?>
									<td class="fail">
										<a title="Not yet published" class="tl"><i class="icon-remove"></i></a>
									</td>  
								<?php endif ?> 
									  
								<?php if ($pr->status_num >= Model_Writing_Process::status_to_index(Model_Writing_Order::
											STATUS_CUSTOMER_ACCEPTED) && @$pr->date_report_sent ): ?>
										<td class="success">
											<a title="Report generated on 
												<?php	$rsent = Date::out($pr->date_report_sent); ?>
												<?= $rsent->format('m/j') ?>" 
												class="tl" href="reseller/publish/report/<?= $pr->content_id ?>"
												target="_blank"><i class="icon-ok"></i></a>
										</td> 
								<?php else: ?>    
									<td class="fail">
										<a title="Not yet generated" class="tl"><i class="icon-remove"></i></a>
									</td>
								<?php endif ?>
								
							</tr>
							<?php endforeach ?>  
							
						</tbody>
					</table>
					<?php else: ?>
						<br /><br /><br /> 
						<div align="center">
							No PRs to display.
						</div>
						 <br /><br /><br /> 
					<?php endif ?>
						
						
						
					</div>
				
				

				</section>				
			</div>
		</div>
	</div>
</div>             	
		
<?= $this->load->view('reseller/dashboard/partials/modal_boxes_activities_area', null, true) ?>

<script>
	
var activites_current_page = 1;

function select_writer_id(prId,writerId,writerName) {
   document.getElementById("selectWriter" + prId).innerHTML=writerName + '<span class="caret"></span>';
   document.getElementById("writer" + prId).value=writerId;
}

function post_assign_form() {
   document.getElementById("AssignForm").submit();
}

function hide_all_activity_pages(total_pages) {
	for(k=1; k<=total_pages; k++)
		document.getElementById("activities_span" + k).style.display="none";
}

function next_prev_activites_buttons_toggle(total_pages) {
	document.getElementById("activities_previous_button").innerHTML='<a onclick="previous_activities(' + total_pages + ');" href="javascript: void(0)">&laquo; Previous </a>';
	document.getElementById("activities_next_button").innerHTML='<a href="javascript: void(0)" class="pull-right" onclick="next_activities(' + total_pages + ');" style="padding-left:15px;">Next &raquo;</a>';
	if (activites_current_page == 1)
		document.getElementById("activities_previous_button").innerHTML="&nbsp;";
	if (activites_current_page == total_pages)
		document.getElementById("activities_next_button").innerHTML="&nbsp;";
}

function previous_activities(total_pages) {		
	if (activites_current_page > 1) {
		hide_all_activity_pages(total_pages);
		activites_current_page--;
		document.getElementById("activities_span"+activites_current_page).style.display="block";
	}	
	next_prev_activites_buttons_toggle(total_pages);	
}

function next_activities(total_pages) {		
	if (activites_current_page < total_pages) {
		hide_all_activity_pages(total_pages);
		activites_current_page++;
		document.getElementById("activities_span"+activites_current_page).style.display="block";	
	}
	next_prev_activites_buttons_toggle(total_pages);		
}

</script>