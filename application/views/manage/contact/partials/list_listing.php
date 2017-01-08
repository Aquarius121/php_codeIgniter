<div class="tab-content" id="ax-tab-content">
	<div class="tab-pane fade in active">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th class="title">List Name</th>
						<th>Date Created</th>
						<th class="ta-center">Latest Campaign</th>
					</tr>
				</thead>
				
				<tbody>
					<?php foreach ($vd->results as $result): ?>
					<tr>
						<td class="left">
							<h3>
								<?php if (@$result->pitch_order_id): ?>
								<a>
									<div class="marbot-5">
										PITCH LIST
										<span class="text-muted">
											<?= $vd->esc($result->city) ?>, <?= $vd->esc($result->state_abbr) ?>
										</span>
									</div>
									<div>
										<span class="text-muted">
											<?php $created = Date::out($result->po_date_created); ?>
											<?= $created->format('M j, Y') ?> | 
										</span>
										<span class="status-alternative">
											<?= $vd->esc($result->keyword) ?>
										</span>
									</div>
								</a>
								<?php elseif (@$result->is_nr_subscriber_list): ?>
									<a href="manage/contact/list/edit/<?= $result->id ?>">
										<?= $vd->esc($result->name) ?>
									</a>
								<?php else: ?>
									<a href="manage/contact/list/edit/<?= $result->id ?>">
										<?= $vd->esc($result->name) ?> 
										<span class="text-muted">(<?= (int) $result->count_contacts ?>)</span>
									</a>
								<?php endif ?>
							</h3>
							<ul class="actions">
								<?php if (@$result->pitch_order_id): ?>
									<li>
										<a href="manage/contact/list/edit/<?= $result->id ?>">View List</a>
									</li>
									<li>
										<a href="#" data-id="<?= $result->pitch_order_id ?>" 
											class="pw-order-detail">Order Details</a>
									</li>
								<?php elseif (@$result->is_nr_subscriber_list): ?>
									<li>
										<a href="manage/contact/list/edit/<?= $result->id ?>">View List</a>
									</li>
									<li>
										<?= (int) $result->count_contacts ?> Subscribers
									</li>
								<?php else: ?>
									<li><a href="manage/contact/list/edit/<?= $result->id ?>">Edit</a></li>
									<li><a href="manage/contact/list/delete/<?= $result->id ?>">Delete</a></li>
									<li><a href="manage/contact/list/download/<?= $result->id ?>">Export</a></li>
									<li><a href="manage/contact/contact/edit/from/<?= $result->id ?>">Add Contact</a></li>
									<?php if ($result->count_actions): ?>
									<li><a class="list-history" href="#" 
										data-id="<?= $result->id ?>">History</a></li>
									<?php endif ?>
									<li><a href="manage/contact/list/duplicate/<?= $result->id ?>">Duplicate</a></li>
								<?php endif ?>
							</ul>
						</td>
						<td>
							<?php $created = Date::out($result->date_created); ?>
							<?= $created->format('M j, Y') ?>
						</td>
						<td class="ta-center">
							<?php if (@$result->pitch_order_id && ! $result->is_sent): ?>
								<a href="manage/contact/campaign/edit/<?= $result->last_campaign_id ?>">
									<?= $vd->cut($result->content_title, 35) ?>
								</a>
							<?php elseif ($result->last_campaign_id): ?>
							<a href="manage/analyze/email/view/<?= $result->last_campaign_id ?>">
								<?= $vd->esc($result->last_campaign_name) ?>
							</a>
							<?php else: ?>
							<span>-</span>
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach ?>

					<?php if (!count($vd->results)): ?>
						<tr>
							<td colspan="3" class="ta-left">
								<?php if (@$is_search_result): ?>
									No lists found
								<?php else: ?>
									<a href="manage/contact/import">Import</a> a list or find contacts in our 
									<a href="manage/contact/media_database">media database</a>
								<?php endif ?>
							</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
defer(function() {

	$(document).on("click", ".list-history", function(ev) {

		ev.preventDefault();
		var id = $(this).data("id");

		var content_url = "manage/contact/list/load_history_modal/" + id;
		var modal = $("#<?= $vd->history_modal_id ?>");
		
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});
	});

});
</script>