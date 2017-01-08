<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12  page-title">
				<?php if ($ci->input->get('terms')): ?>
					<h2>Search Results</h2>
				<?php else: ?>
					<h2>Email Campaigns</h2>
				<?php endif ?>
			</div>
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 actions">
				<ul class="list-inline actions">
					<li><a href="manage/contact/pitch/process/" class="btn btn-default">Order Pitch Writing</a></li>
					<li><a href="manage/contact/campaign/edit" class="btn btn-primary">New Campaign</a></li>
				</ul>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<div class="panel-heading">
					<ul id="tabs" class="nav nav-tabs nav-activate ax-loadable"
						data-ax-elements="#ax-chunkination, #ax-tab-content">
						<li><a data-on="^manage/contact/campaign/all" 
							href="<?= gstring('manage/contact/campaign/all') ?>">All</a></li>
						<li><a data-on="^manage/contact/campaign/sent" 
							href="<?= gstring('manage/contact/campaign/sent') ?>">Sent</a></li>
						<li><a data-on="^manage/contact/campaign/scheduled" 
							href="<?= gstring('manage/contact/campaign/scheduled') ?>">Scheduled</a></li>
						<li><a data-on="^manage/contact/campaign/draft" 
							href="<?= gstring('manage/contact/campaign/draft') ?>">Draft</a></li>
					</ul>
				</div>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active" id="campaign-<?= $ci->uri->segment(4) ?>">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
									<tr>
										<th class="left">Name</th>
										<th class="ta-center">Content</th>
										<th class="ta-center">Status</th>
									</tr>
								</thead>
								<tbody>

								<?php foreach ($vd->results as $result): ?>
									<tr>
										<td class="left campaign-list-icon
											<?= value_if_test(@$result->is_auto_campaign, 'auto-campaign-icon') ?> 
											<?= value_if_test(@$result->pitch_order_id, 'pitch-order-icon') ?>">
											<div class="td-container">
											<h3>
												<?php if ($result->pitch_order_id && ! $result->is_sent): ?>
													<?= $vd->esc($vd->cut($result->content_title, 50)) ?>
												<?php else: ?>
													<a href="manage/contact/campaign/edit/<?= $result->id ?>">
														<?= $vd->esc($vd->cut($result->name, 50)) ?>
													</a>
												<?php endif ?>
											</h3>
											<ul class="actions">
												<?php if (@$result->pitch_order_id): ?>
													<?php if ($result->pitch_status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
														<li><a href="manage/contact/campaign/edit/<?= $result->id ?>">Review Pitch</a></li>
													<?php elseif ($result->pitch_status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
														<li><a href="manage/contact/campaign/edit/<?= $result->id ?>">View Pitch</a></li>
													<?php elseif ($result->pitch_status == 
														Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
														<li><a href="manage/contact/pitch/process/<?= $result->pw_session_id ?>/1">
														Edit Pitch Order</a></li>
													<?php endif ?>
												<?php else: ?>
													<li><a href="manage/contact/campaign/edit/<?= $result->id ?>">Edit</a></li>
												<?php endif ?>
												<?php if (@$result->pitch_order_id && ! $result->is_sent): ?>
													<li class="pw-order-detail">
														<a href="#" data-id="<?= $result->pitch_order_id ?>">
															Order Details
														</a>
													</li>
												<?php else: ?>	
													<li><a href="manage/contact/campaign/delete/<?= $result->id ?>">Delete</a></li>
												<?php endif ?>

												<?php if ($result->is_sent): ?>
												<li><a href="manage/analyze/email/view/<?= $result->id ?>">Statistics</a></li>
												<?php endif ?>
											</ul>
											</div>
										</td>
										<td class="ta-center">
											<?php if ($result->content_type): ?>
												<span><?= Model_Content::full_type($result->content_type) ?></span>
											<?php else: ?>
												<span>-</span>
											<?php endif ?>
										</td>
										<td class="ta-center">
											<?php if (@$result->pitch_order_id &&  $result->pitch_status == 
												Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
												<a href="manage/contact/campaign/edit/<?= $result->id ?>">
													<span class="label label-danger">Review Pitch</span>
												</a>
											<?php endif ?>

											<?php if ($result->is_sent): ?>
												<span class="label label-success">Sent</span>
												<div class="pad-top-5">
													<?php $deliver = Date::out($result->date_send); ?>
													<span class="status-muted smaller"><?= $deliver->format('M j, Y') ?></span>
												</div>
											<?php elseif ($result->is_draft): ?>
												<?php if ($result->pitch_order_id): ?>
													<?php if ($result->pitch_status == Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER): ?>
														<span class="label label-default">Draft</span>
													<?php elseif ($result->pitch_status == Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED): ?>
														<a href="manage/contact/campaign/edit/<?= $result->id ?>">
															<span class="label label-danger">Schedule Pitch</span>
														</a>
														<span class="label label-default">Draft</span>
													<?php else: ?>
														<span class="label label-info">In Progress</span>
													<?php endif ?>
												<?php else: ?>
													<span class="label label-default">Draft</span>
												<?php endif ?>
											<?php elseif ($result->is_send_active): ?>
											<span class="label label-info">Sending</span>
											<?php else: ?>
												<span class="label label-info">Scheduled*</span> 
												<div class="pad-top-5">
													<span class="status-muted smaller">
														<?php $deliver = Date::out($result->date_send); ?>
														<?= $deliver->format('M j, Y') ?>
													</span>
												</div>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>
									
									<?php if (!count($vd->results)): ?>
										<tr>
											<td colspan="3" class="ta-left">
												No campaigns found, 
												<a href="manage/contact/campaign/edit">
													create</a> one now.
											</td>
										</tr>
									<?php endif ?>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
			<div class="pull-left grid-report">
				* Assumes that the content is published.
			</div>		
		</div>
	</div>

	<div id="ax-chunkination">
		<div class="ax-loadable"
			data-ax-elements="#ax-chunkination, #ax-tab-content">
			<?= $vd->chunkination->render() ?>
		</div>
		<p class="pagination-info ta-center">
			Displaying <?= count($vd->results) ?> 
			of <?= $vd->chunkination->total() ?> Campaigns
		</p>
	</div>

</div>

<script>
$(function() {

	$(document).on("click", ".pw-order-detail a", function(ev) {
		
		ev.preventDefault();
		var id = $(this).data("id");

		var content_url = "manage/contact/campaign/load_pw_order_detail_modal/" + id;
		var modal = $("#<?= $vd->pw_detail_modal_id ?>");
		
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});
			
	});

});
</script>