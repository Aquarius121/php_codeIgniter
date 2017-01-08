<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/browse.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<?php if (!empty($vd->action_message)): ?>
	
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span12">
						<h1>Thank You!</h1>
					</div>
				</div>
			</header>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="content">		
				<div class="row-fluid">
					<div class="span12">
						<?= $vd->action_message ?>
					</div>
				</div>
			</div>
		 </div>
	</div>
	                        	
<?php elseif (!empty($vd->cant_render_message)): ?>	
	
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span12">
						<h1>Error</h1>
					</div>
				</div>
			</header>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="content">		
				<div class="row-fluid">
					<div class="span12">
						<?= $vd->cant_render_message ?>
					</div>
				</div>
			</div>
		 </div>
	</div> 
	
<?php else: ?>
	
	<?php if ($vd->reseller_logged_in): ?>
		<form method="post" action="" class="required-form">
			<div class="row-fluid border_dark_gray">
				<div class="span12">
					<div class="content">	
						<div class="row-fluid">
							<div class="span12">
								<div>			
									<span class="span3">&nbsp;</span>
									<span class="span2">
										<a title="Order Details" target="_blank"
											href="writing/prdetails/preview/<?= $vd->m_order->id ?>/<?=
											$vd->m_order_code->writing_order_code ?>">Order Details</a>
									</span>
									<span class="span3">
										<strong>Writer: </strong><?= $vd->esc($vd->writer_name) ?>
									</span>
									<span class="span2">
										<a title="Order Details" 
											href="writing/draft/edit/<?= $vd->m_order->id ?>/<?=
											$vd->m_order_code->writing_order_code ?>">Edit Press Release</a>
									</span>        
									<span class="span2">     
										<a title="Order Details" target="_blank"
											href="writing/prdetails/edit/<?= $vd->m_order->id ?>/<?=
											$vd->m_order_code->writing_order_code ?>">Edit PR Details</a> 
									</span>
								</div>
								<h4>Action Required</h4>
								<br />
								<?php if ($vd->times_rejected): ?>
									<p>
										The writer has revised the press release below. If the changes are 
										acceptable please approve to send back to the reseller/customer. 
									</p>
								<?php else: ?>
									<p>
										Below is a preview of your customer's press release. Please read the 
										press release and if you think there needs to be any changes or edits, 
										please select "reject" and add your comments. 
									</p>
									<p>
										Otherwise, select the option to "Send to Customer for Review" - This 
										allows your customer to review the press release before it gets submitted 
										to Newswire
									</p>
								<?php endif ?>  
								<p>
									<input type="radio" name="reseller_action" value="approve" 
										id="reseller_action_approve"> 
										Fully Approve (If you fully approve a PR, 
										it means that it will be submitted on i-newswire) 
								</p>
								<p id="reseller_reject">
									<input type="radio" name="reseller_action" value="reject" 
										id="reseller_action_reject">
										Reject and send back to writer for revisions. 
										(Write comments to the writer based off of the customer comments below)
								</p>
								<div class="hidden" id="desc_div">
									Reason for Rejection: <br />
									<textarea class="in-text span6 required-callback"
										name="reason" id="reason"
										data-required-name="Reason for Rejection" 
										data-required-callback="rejection-reason-reseller-required"
										></textarea>
								</div>
								<p>                            
									<input type="radio" name="reseller_action" value="send_to_customer"
										id="reseller_action_send_to_customer">
									Send to Customer for Review (An email will be sent to the customer containing 
									a link to the PR and options to approve/reject the PR. 
								</p>    
								<li class="marbot-30">
									<div class="span4">
										<button type="submit" value="1" 
											class="span11 bt-silver">Submit</button>
									</div>
								</li>
							</div>
							
							<?php if (!empty($vd->last_reseller_rejection_comments)): ?>
								<h4 style="color: red">Last Rejection Comments</h4>
								<?= nl2br($vd->esc($vd->last_reseller_rejection_comments)) ?>
								<br /><br />
							<?php endif ?>   
							
							<?php if (!empty($vd->last_cust_rejection_comments)): ?>
								<h4 style="color: red">Customer Rejection Comments</h4>
								<?= nl2br($vd->esc($vd->last_cust_rejection_comments)) ?>
							<?php endif ?>
							
						</div>  
					</div>              
				</div>
			</div>
		</form>
		
	<?php else: ?>
	
		 <form method="post" action=""  class="required-form">
			<div class="row-fluid border_dark_gray">
				<div class="span12">                
					<div class="content">	
						<div class="row-fluid">
							<div class="span12">
								<div>			
									<span class="span10">&nbsp;</span>
									<span class="span2">
										<a title="Order Details" 
											href="writing/prdetails/preview/<?= $vd->m_order->id ?>/<?=
											$vd->m_order_code->writing_order_code ?>">Order Details</a>
									</span>
								</div>
								<h4>Action Required</h4>
								<br />
								<?php if (@$vd->times_rejected_by_customer): ?>
								<p>
									The PR you rejected has been revised. 
									You can approve/reject it below. 
								</p>
								<?php else: ?>
								<p>
									Below is a preview of your PR. You can approve/reject the PR 
									with appropriate comments.
								</p>                                   
								<?php endif ?>  
								<p>
									<input type="radio" name="customer_action" 
										value="approve" id="customer_action_approve" /> 
									Approve
								</p>
								<p id="customer_reject">
									<input type="radio" name="customer_action" 
										value="reject" id="customer_action_reject" />
										Reject
								</p>
								<div class="hidden" id="desc_div">
									Reason for Rejection:<br />
									<textarea class="in-text span6 required-callback"
										name="reason" id="reason"
										data-required-name="Reason for Rejection" 
										data-required-callback="rejection-reason-customer-required"
									></textarea>
								</div>
								<li class="marbot-30">
									<div class="span4">
										<button type="submit" value="1" 
											class="span11 bt-silver">Submit</button>
									</div>
									<div class="span4">
										<button type="button" value="1" id="edit_button" 
											class="span11 bt-silver">I will edit the PR</button>
									</div>
								</li>
							</div>
							
							<?php if (!empty($vd->last_cust_rejection_comments)): ?>
								<h4 style="color: red">Last Rejection Comments</h4>
								<?= $vd->esc($vd->last_cust_rejection_comments) ?>
							<?php endif ?>   
							
						</div>
					</div>
				</div>
			</div>
		</form>   
		
	<?php endif ?>
	
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span11">	
						<h1><?= $vd->esc($vd->pr_title) ?></h1> 
					</div>
				</div>
			</header>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span12">
			<div class="content">		
				<div class="row-fluid">
					
					<div class="span8 information-panel">
						
						<section class="marbot-15">
							<div class="blur_title">Summary</div>
							<?= $vd->esc($vd->summary) ?>
						</section>
						
						<section class="marbot-15 article-content">
							<div class="blur_title">PR Body</div>
							<!--<?= $vd->pure($vd->content) ?>-->                            
							<?php echo $ci->load->view('browse/view/partials/supporting_quote') ?>
							<div class="marbot-15 html-content"><?php echo $vd->content ?></div>					
						</section>
						
						<section class="marbot-15 pad-10 border_gray">
							<div><h4>Included Media</h4></div>
							On your final published PR we will be including the following media that 
							you have previously supplied to us)
							<br /><br />
							<strong>Logo: </strong>
								<?php if ($vd->logo_image_id): ?>
									Yes
								<?php else: ?>
									No
								<?php endif ?>
							<br />
							<strong>Video: </strong> 
								<?php if ($vd->web_video_id): ?>
									Yes
								<?php else: ?>
									No
								<?php endif ?>
							<br />
							<strong>Images: </strong>
								<?php if ($vd->images_count): ?>
									<?= $vd->images_count ?>
								<?php else: ?>
									No
								<?php endif ?>
							<br />
						</section>
						
					</div>   
					           
					<aside class="span4 aside aside-fluid">
						<div class="aside-properties padding-top" id="locked_aside">
							<strong>About <?= $vd->esc($vd->company_name) ?></strong>
							<br /><?= $vd->esc($vd->company_details) ?>
						</div>
						<br />
						<div class="aside-properties padding-top" id="locked_aside">
							<strong>Contact Information</strong><br />
							<a href="<?= $vd->company_website ?>">
								<?= $vd->esc($vd->company_name) ?>
							</a><br />
							<?php if (@$vd->company_contact->name): ?>
								<?= $vd->esc($vd->company_contact->name) ?><br />
							<?php endif ?>  
							<?php if (@$vd->company_profile->address_street): ?>
								<?= $vd->esc($vd->company_profile->address_street) ?><br />
							<?php endif ?>
							<?php if (@$vd->company_profile->address_apt_suite): ?>
								<?= $vd->esc($vd->company_profile->address_apt_suite) ?><br />
							<?php endif ?>  
							<?php if (@$vd->company_profile->address_city): ?>
								<?= $vd->esc($vd->company_profile->address_city) ?><br />
							<?php endif ?>  
							<?php if (@$vd->company_profile->address_state): ?>
								<?= $vd->esc($vd->company_profile->address_state) ?><br />
							<?php endif ?>  
							<?php if (@$vd->company_profile->address_zip): ?>
								<?= $vd->esc($vd->company_profile->address_zip) ?><br />
							<?php endif ?> 
							<?php if (@$vd->company_profile->phone): ?>
								<?= $vd->esc($vd->company_profile->phone) ?><br />
							<?php endif ?> 
							<?php if (@$vd->country): ?>
								<?= $vd->esc($vd->country) ?><br />
							<?php endif ?>
						</div> 
					</aside>
					
				</div>
			</div>
		</div>
	</div>
	
<?php endif ?>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<script>
$(function() {

	$("#customer_action_approve").click(function() {
		$("#desc_div").addClass("hidden");
	});
	
	$("#customer_action_reject").click(function() {
		$("#desc_div").removeClass("hidden");
	});
	
	$("#reseller_action_approve").click(function() {
		$("#desc_div").addClass("hidden");
	});
	
	$("#reseller_action_reject").click(function() {
		$("#desc_div").removeClass("hidden");
	});
	
	$("#reseller_action_send_to_customer").click(function() {
		$("#desc_div").addClass("hidden");
	});

	$("#edit_button").click(function() {
		location.href = 'writing/draft/edit/<?= $vd->writing_order_id ?>/<?= $vd->writing_order_code ?>';
		return false;
	});

	required_js.add_callback("rejection-reason-reseller-required", function(value) {
		var response = { valid: false, text: "is required" };
		var rej_checked = $("#reseller_action_reject").is(":checked");
		response.valid = rej_checked == false || (rej_checked == true && value);
		return response;
	});
	
	required_js.add_callback("rejection-reason-customer-required", function(value) {
		var response = { valid: false, text: "is required" };
		var rej_checked = $("#customer_action_reject").is(":checked");
		response.valid = rej_checked == false || (rej_checked == true && value);
		return response;
	});
	
});
	
</script>