<div class="panel with-nav-tabs panel-default">
	<div class="panel-heading">
		<ul id="tabs" class="nav nav-tabs">
			<li class="active"><a href="#press-releases" data-toggle="tab">Press Releases</a></li>
			<li><a href="#email-campaigns" data-toggle="tab">Email Campaigns 
					<?php if ($vd->email_notification_count): ?>
						<span class="badge"><?= (int) $vd->email_notification_count ?></span>
					<?php endif ?>
				</a>
			</li>
			<li><a href="#writing-orders" data-toggle="tab">Writing Orders
				<?php if ($vd->wr_sessions_notification_count): ?>
						<span class="badge"><?= (int) $vd->wr_sessions_notification_count ?></span>
					<?php endif ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="press-releases">
			<div class="dashboard-view-all-link">
				<a href="manage/publish/pr">View all Press Releases</a>
			</div>

			<div class="table-responsive">
				<?php if (!count($vd->prs)): ?>
				<div class="pad-20">
					No press releases created, 
					create <a href="manage/publish/pr/edit">one now</a>
				</div>
				<?php else: ?>
				<table class="table">
					<thead>
						<tr>
							<th class="title">Press Release Title</th>
							<th>Publish Date</th>
							<th>Type</th>
							<th class="status">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($vd->prs as $pr): ?>
						<tr>
							<td class="title">
								<h3><a href="<?= $pr->mock_nr->url() ?>manage/publish/pr/edit/<?= $pr->id ?>">
									<?= $vd->esc($vd->cut($pr->title, 40)) ?></a></h3>
								<ul class="actions">
									<li><a target="_blank" href="<?= $pr->url() ?>">View</a></li>
									<li><a href="<?= $pr->mock_nr->url() ?>manage/publish/pr/edit/<?= $pr->id ?>">Edit</a></li>
									<li><a href="<?= $pr->mock_nr->url() ?>manage/publish/pr/delete/<?= $pr->id ?>">Delete</a></li>
									<?php if (!Auth::user()->is_free_user() || ($pr->is_premium && $pr->is_published)): ?>
										<li><a href="<?= $pr->mock_nr->url() ?>manage/contact/campaign/edit/from/<?= $pr->id ?>">Email</a></li>
									<?php endif ?>
									<li><a href="<?= $pr->mock_nr->url() ?>view/pdf/<?= $pr->id ?>">PDF</a></li>
									<?php if ($pr->is_published): ?>
										<li><a href="<?= $pr->mock_nr->url() ?>manage/analyze/content/view/<?= $pr->id ?>">Stats</a></li>
									<?php endif ?>
								</ul>
							</td>
							<td>
								<?php if (!$pr->is_draft): ?>
									<?php $publish = Date::out($pr->date_publish); ?>
									<?= $publish->format('M j, Y') ?>&nbsp;
									<span class="text-muted"><?= $publish->format('H:i') ?></span>
									<?php if ($pr->requires_credit() && 
										((!$pr->is_premium && !$vd->pr_credits_basic) ||
										 ( $pr->is_premium && !$vd->pr_credits_premium))): ?>
									<div class"clearfix"></div><span class="label label-danger">Credit Needed</span>
									<?php endif ?>
									<?php else: ?>
									<span>-</span>
								<?php endif ?>
							</td>
							<td>
								<?php if ($pr->is_premium): ?>

									<?php if ($pr->distribution_bundle): ?>
									<div><span><?= $pr->distribution_bundle->name() ?></span></div>
									<?php else: ?>
									<div><span>Premium</span></div>
									<?php endif ?>

									<?php if (isset($pr->release_plus_set)): ?>
									<div class="small-text">
									<?php foreach ($pr->release_plus_set as $release_plus): ?>
										<?php if ($release_plus->is_bundled) continue; ?>
										<?php if ($release_plus->is_confirmed): ?>
										<div class="normal-line">with <span class="status-info"><?= 
											$release_plus->name() ?></span></div>
										<?php else: ?>
										<div class="normal-line">with <span class="status-false"><?= 
											$release_plus->name() ?></span></div>
										<?php endif ?>
									<?php endforeach ?>
									</div>
									<?php endif ?>

								<?php else: ?>
									<span>Basic</span>
								<?php endif ?>
							</td>
							<td class="status">
								<?php if ($pr->is_published): ?>
								<span class="label label-success">Published</span>
								<?php elseif ($pr->is_under_review): ?>
								<span class="label label-info">Under Review</span>
								<?php elseif ($pr->is_draft): ?>
									<span class="label label-default">Draft</span>
									<?php if ($pr->is_rejected): ?>
									<div class="label label-danger">Rejected</div>
									<?php endif ?>
								<?php else: ?>
									<span class="label label-info">Scheduled</span>
								<?php endif ?>
							</td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<?php endif ?>
			</div>
		</div>
		<div class="tab-pane fade" id="email-campaigns">
			<div class="dashboard-view-all-link">
				<a href="manage/contact/campaign">View all Email Campaigns</a>
			</div>
			<div class="table-responsive">
				<?php if (!count($vd->emails)): ?>
				<div class="pad-20">
					No email campaigns created, 
					create <a href="manage/contact/campaign/edit">one now</a>
				</div>
				<?php else: ?>
				<table class="table">
					<thead>
						<tr>
							<th class="left">Name</th>
							<th class="ta-center">Content</th>
							<th class="ta-center">Status</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($vd->emails as $email): ?>
						<tr>
							<td class="left campaign-list-icon
					 			<?= value_if_test(@$email->pitch_order_id, 'pitch-order-icon') ?>">
								<div class="td-container">
								<h3>
									<?php if ($email->pitch_order_id && ! $email->is_sent): ?>
										<?= $vd->esc($vd->cut($email->content_title, 50)) ?>
									<?php else: ?>
										<a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/edit/<?= $email->id ?>">
											<?= $vd->esc($vd->cut($email->name, 50)) ?>
										</a>
									<?php endif ?>
								</h3>
								<ul class="actions">
									<?php if (@$email->pitch_order_id): ?>
										<?php if ($email->pitch_status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
											<li><a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/edit/<?= $email->id ?>">Review Pitch</a></li>
										<?php elseif ($email->pitch_status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
											<li><a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/edit/<?= $email->id ?>">View Pitch</a></li>
										<?php elseif ($email->pitch_status == 
											Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
											<li><a href="<?= $email->mock_nr->url() ?>manage/contact/pitch/process/<?= $email->pw_session_id ?>/1">
											Edit Pitch Order</a></li>
										<?php endif ?>
									<?php else: ?>
										<li><a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/edit/<?= $email->id ?>">Edit</a></li>
									<?php endif ?>
									<?php if (@$email->pitch_order_id && ! $email->is_sent): ?>
										<?php if (!$vd->is_overview): ?>
											<li class="pw-order-detail">
												<a href="#" data-id="<?= $email->pitch_order_id ?>">
													Order Details
												</a>
											</li>
										<?php endif ?>
									<?php else: ?>	
										<li><a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/delete/<?= $email->id ?>">Delete</a></li>
									<?php endif ?>

									<?php if ($email->is_sent): ?>
									<li><a href="<?= $email->mock_nr->url() ?>manage/analyze/email/view/<?= $email->id ?>">Statistics</a></li>
									<?php endif ?>
								</ul>
								</div>
							</td>
							<td class="ta-center">
								<?php if ($email->content_type): ?>
									<span><?= Model_Content::full_type($email->content_type) ?></span>
								<?php else: ?>
									<span>-</span>
								<?php endif ?>
							</td>
							<td class="ta-center">
								<?php if (@$email->pitch_order_id &&  $email->pitch_status == 
									Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
									<div class="label label-danger">
										<a href="<?= $email->mock_nr->url() ?>manage/contact/campaign/edit/<?= $email->id ?>">Review Pitch</a>
									</div>
								<?php endif ?>

								<?php if ($email->is_sent): ?>
									<span class="label label-success">Sent</span>
									<div class="pad-top-5">
										<?php $deliver = Date::out($email->date_send); ?>
										<span class="status-muted smaller"><?= $deliver->format('M j, Y') ?></span>
									</div>
								<?php elseif ($email->is_draft): ?>
									<?php if ($email->pitch_order_id): ?>
										<?php if ($email->pitch_status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
											<span class="label label-default">Draft</span>
										<?php elseif ($email->pitch_status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
											<a href="manage/contact/campaign/edit/<?= $email->id ?>">
												<span class="label label-danger">Schedule Pitch</strong>
											</a>
											<div class="label label-success">Draft</div>
										<?php else: ?>
											<span class="label label-default">In Progress</span>
										<?php endif ?>
									<?php else: ?>
										<span class="label label-default">Draft</span>
									<?php endif ?>
								<?php elseif ($email->is_send_active): ?>
								<span>Sending</span>
								<?php else: ?>
									<span class="label label-info">Scheduled</span>
									<div class="pad-top-5">
										<span class="status-muted smaller">
											<?php $deliver = Date::out($email->date_send); ?>
											<?= $deliver->format('M j, Y') ?>
										</span>
									</div>
								<?php endif ?>
							</td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
				<?php endif ?>
			</div>
		</div>
		<div class="tab-pane fade" id="writing-orders">
			<div class="dashboard-view-all-link">
				<a href="manage/publish/pr/under_writing">View all Writing Orders</a>
			</div>
			<div class="table-responsive">
				<?php if (!count($vd->wr_sessions)): ?>
				<div class="pad-20">
					No writing orders created, 
					create <a href="manage/writing/process">one now</a>
				</div>
				<?php else: ?>
				<table class="table">
					<thead>
						<tr>
							<th class="title">Title</th>
							<th>Type</th>
							<th class="status">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($vd->wr_sessions as $wr_session): ?>
							<?php $raw_data = $wr_session->raw_data(); ?>
							<tr>
								<td class="left">
									<h3>
										<?php if (!empty($raw_data->primary_keyword)): ?>
										<div class="marbot-5">
											<?php if ($wr_session->title): ?>
											<?= $vd->esc($vd->cut($wr_session->title, 45)) ?>
											<?php else: ?>
											Writing Order in Progress
											<?php endif ?>
										</div>
										<div>
											<a class="tl status-text-muted" title="<?= $wr_session->nice_id() ?>">
												<?php $dt_created = Date::out($wr_session->date_created); ?>
												<?= $dt_created->format('Y-m-d') ?>
											</a>
											<span>|</span>
											<span class="status-alternative"><?= $vd->esc($raw_data->primary_keyword) ?></span>
										</div>
										<?php else: ?>
										<div>
											<a class="tl status text-muted" title="<?= $wr_session->nice_id() ?>">
												<?php $dt_created = Date::out($wr_session->date_created); ?>
												<?= $dt_created->format('Y-m-d') ?>
											</a>
											<span>|</span>
											<span>Writing Order in Progress</span>
										</div>
										<?php endif ?>
									</h3>		
									<ul class="actions">
										<?php if (Model_Writing_Session::is_preview_available($wr_session->status)): ?>
										<li><a href="<?= $wr_session->mock_nr->url() ?>view/preview/<?= $wr_session->id ?>" target="_blank">View PR</a></li>
										<?php endif ?>
										<li><a href="<?= $wr_session->mock_nr->url() ?>manage/writing/process/<?= $wr_session->id ?>/1">Edit Details</a></li>
										<li><a href="<?= $wr_session->mock_nr->url() ?>manage/writing/process/<?= $wr_session->id ?>/4/review">Review Order</a></li>
									</ul>
								</td>
								
								<td>
									Writing Order		
									<div class="text-muted">
										<?php if ($wr_session->is_premium): ?>
										<span>Premium</span>
											<?php if (isset($wr_session->release_plus_set)): ?>
											<div class="small-text">
											<?php foreach ($wr_session->release_plus_set as $release_plus): ?>
												<?php if ($release_plus->is_confirmed): ?>
												<div class="normal-line">with <span class="status-info"><?= 
													$release_plus->name() ?></span></div>
												<?php else: ?>
												<div class="normal-line">with <span class="status-false"><?= 
													$release_plus->name() ?></span></div>
												<?php endif ?>				
											<?php endforeach ?>
											</div>
											<?php endif ?>
										<?php else: ?>
										<span>Basic</span>
										<?php endif ?>
									</div>
								</td>
								<td>
									<?php if ($wr_session->status == Model_Writing_Order::STATUS_NOT_ASSIGNED): ?>
										<span class="label label-info">Details Submitted</span>
									<?php elseif ($wr_session->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER): ?>
										<a href="<?= $wr_session->mock_nr->url() ?>view/preview/<?= $wr_session->id ?>" target="_blank">
											<span class="label label-danger">Review Required</span></a>
									<?php elseif ($wr_session->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
										<a href="<?= $wr_session->mock_nr->url() ?>manage/writing/process/<?= $wr_session->id ?>/1">
											<span class="label label-danger">Details Required</span></a>
									<?php elseif ($wr_session->status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS): ?>
										<span class="label label-info">Details Submitted</span>
									<?php elseif (!$wr_session->writing_order_id): ?>
										<a href="<?= $wr_session->mock_nr->url() ?>manage/writing/process/<?= $wr_session->id ?>/1">
											<span class="label label-danger">Details Required</span></a>
									<?php else: ?>
										<span class="label label-info">In Progress</span>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<?php endif ?>
			</div>
		</div>
	</div>

</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap3/js/bootstrap-tabcollapse.js');
	$render_basic = $ci->is_development();
	$ci->add_eob($loader->render($render_basic));

?>

<script>

$(function(){

	$(".pw-order-detail a").on("click", function(ev) {
		ev.preventDefault();
		var id = $(this).data("id");
		var content_url = "manage/contact/campaign/load_pw_order_detail_modal/" + id;
		var modal = $("#<?= $vd->pw_detail_modal_id ?>");		
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal("show");
		});
	});

	$("#tabs").tabCollapse({
		tabsClass: "hidden-sm hidden-xs",
		accordionClass: "visible-sm visible-xs"
	});

});
    
</script>