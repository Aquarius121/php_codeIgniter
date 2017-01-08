<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<?php if (@$vd->is_archive): ?>
						<h1>PW Orders  Archive</h1>
                    <?php else: ?>
						<h1>PW Order Manager</h1>
					<?php endif ?>
				</div>
			</div>
		</header>
	</div>
</div>
			
<div class="row-fluid">
	<div class="span12">
		<?= $this->load->view('admin/contact/pitch_wizard_order/sub_menu.php') ?>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th style="text-align:left">Campaign Name</th>
						<th>Writer</th>
						<th>Pitch Status</th>
						<th>Builder</th>
						<th>List Status</th>
						<th>Date Sent</th>
					</tr>

				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td width="35%" style="text-align:left">
								<?php if ($result->delivery == Model_Pitch_Order::DELIVERY_RUSH): ?>
									<strong class="label-class status-false">RUSH</strong>
								<?php endif ?>
								<a href="<?= $result->url() ?>" target="_blank">
									<?= $vd->esc($vd->cut($result->title, 30)) ?>
								</a>

								<div class="muted">
									<?php if ($result->order_type == Model_Pitch_Order::ORDER_TYPE_OUTREACH): ?>
										<?= $vd->esc($result->city) ?> | 
									<?php else: ?>
										<strong class="label-class status-alternative-2">WRITING</strong>
									<?php endif ?> <span class="status-alternative"><?= $vd->esc($result->keyword) ?></span>
								</div>
								<div>
									<a data-id="<?= $result->order_id ?>" class="pw-order-detail"  
										data-modal="<?= $vd->pw_detail_modal_id ?>" href="#">Order Details</a>

									<?php if ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER &&
										$result->status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED
										&&  ! @$vd->is_archive): ?>
										| <a href="admin/contact/pitch_wizard_order/order/mark_archived/<?=
												$result->order_id ?>">
											Archive</a>
									<?php endif ?>
									| <a href="admin/contact/campaign/edit/<?= $result->campaign_id ?>" 
										target="_blank">Campaign</a>
								</div>
							</td>

							<td>
								<?php if (@$result->writer): ?>
									<?= $result->writer->name() ?>
								<?php else: ?>
                                	-
								<?php endif ?>
							</td>
                            
							<td>                        	  
								<?php if ($result->status == Model_Pitch_Order::STATUS_NOT_ASSIGNED): ?>
									<a href="admin/writing/pitch/assign">Assign</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER ||
										$result->status == Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION || 
										$result->status == Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER || 
										$result->status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE || 
										$result->status == Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS): ?>
									<a href="admin/writing/pitch/pending_writing">Pending</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_WRITTEN_SENT_TO_ADMIN): ?>
									<a href="admin/writing/pitch/review_single/<?= $result->order_id ?>">Review</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
									<a href="admin/writing/pitch/customer_review">Sent to Customer</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_CUSTOMER_REJECTED): ?>
									<a href="admin/writing/pitch/review_single/<?= $result->order_id ?>">Rejected</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_ADMIN_REJECTED): ?>
									<a href="admin/writing/pitch/rejected">Rejected</a>
								<?php elseif ($result->status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
									<a href="admin/writing/pitch/pw_order/all">Approved</a>
								<?php endif ?>
							</td>

							<td>
								<?php if (@$result->user): ?>
                                    <?= $result->user->name() ?>
								<?php else : ?>
                                	-
								<?php endif ?>
							</td>
							<td>
								<?php if ($result->pw_list_status == Model_Pitch_List::STATUS_NOT_ASSIGNED): ?>
									<a href="admin/contact/pitch_wizard_order/assign_list">Assign</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_ASSIGNED_TO_LIST_BUILDER): ?>
									<a href="admin/contact/pitch_wizard_order/upload_list">Pending</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_ADMIN): ?>
									<a href="admin/contact/pitch_wizard_order/review_single_list/<?= $result->list_id ?>">Review</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_ADMIN_REJECTED): ?>
									<a href="admin/contact/pitch_wizard_order/review_single_list/<?= $result->list_id ?>">Admin Rej</a>

								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER): ?>
									Sent
								<?php endif ?>
							</td>

                            <td>
								<?php if ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER &&
										$result->status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
									<?php $order = Date::out($result->date_send); ?>
									<?= $order->format('M j, Y') ?>
								<?php else: ?>
									-
								<?php endif; ?>
							</td>
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
					Results
				</div>
			</div>

			<?= $vd->chunkination->render() ?>

		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$loader->add('js/required.js');
	$loader->add('js/pitch_wizard.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>