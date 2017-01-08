<div class="analytics-report-content">

	<table class="report-table comfy">
		<thead>
			<tr>
				<th class="ta-left">Content</th>
				<th class="ta-left">Date Publish</th>
				<th class="ta-left">Date Submit</th>
				<th class="ta-left">Status</th>
				<th class="ta-left">Comments</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->mContentArr as $mContent): ?>
				<tr>
					<td class="ta-left">
						<a href="admin/publish/pr/all?filter_search=<?= $vd->esc($mContent->slug) ?>" class="listing-main-link">
							<?= $vd->esc($vd->cut($mContent->title, 80)) ?>
						</a>
						<ul class="listing-links">
							<li><a href="<?= $ci->website_url() ?>view/<?= $vd->esc($mContent->slug) ?>" target="_blank">View</a></li>
							<li><a href="admin/publish/edit/<?= $mContent->id ?>" target="_blank">Edit</a></li>
						</ul>
					</td>		
					<td class="ta-left">
						<?= Date::out($mContent->date_publish)->format('Y-m-d H:i') ?>
					</td>			
					<td class="ta-left">
						<?php if ($mContent->prn): ?>
							<?php $dtSubmit = Date::out($mContent->prn->date_submit) ?>
							<?php if ($dtSubmit > Date::first()): ?>
								<?= $dtSubmit->format('Y-m-d H:i') ?>
							<?php else: ?>
								<span>-</span>
							<?php endif ?>
						<?php else: ?>
							<span>-</span>
						<?php endif ?>
					</td>
					<td class="ta-left">
						<?php if ($mContent->prn): ?>
							<?php if ($mContent->prn->is_submitted): ?>
								<strong class="status-true">OK</strong>
							<?php elseif ($mContent->prn->is_blocked): ?>
								<strong class="status-false">ERROR</strong>
								<div>
									<a href="#" class="error-reset" 
										data-id="<?= $mContent->id ?>">Reset</a>
								</div>
							<?php else: ?>
								<strong class="status-info">QUEUED</strong>
							<?php endif ?>
						<?php else: ?>
							<strong class="status-info">QUEUED</strong>
						<?php endif ?>
					</td>
					<td class="ta-left">

						<?php if ($mContent->prn && $mContent->prn->is_submitted == 0 && 
							($rdo = $mContent->prn->raw_data_object()) && $rdo->errors): ?>
							<?php foreach ($rdo->errors as $error): ?>
								<?php if (!$error->message) continue; ?>
								<div class="status-false"><?= $vd->esc($error->date) ?></div>
								<div class="status-muted"><?= $vd->esc($error->message) ?></div>
							<?php endforeach ?>
						<?php elseif ($mContent->prn && $mContent->prn->is_submitted == 1 && 
							($rdo = $mContent->prn->raw_data_object()) && $rdo->release_number): ?>
							<?php if ($rdo->report): ?>
							<a href="<?= $vd->esc($rdo->report) ?>">
								<span>#<?= $vd->esc($rdo->release_number) ?>&nbsp;</span>
							</a>
							<?php else: ?>
							<span>#<?= $vd->esc($rdo->release_number) ?>&nbsp;</span>
							<?php endif ?>
						<?php endif ?>

						<?php if ($mContent->prn && 
							($rdo = $mContent->prn->raw_data_object()) &&
							$rdo->comment): ?>
							<div class="status-alternative">
								<?= $vd->esc($rdo->comment) ?>
							</div>
						<?php endif ?>

						<?php if ($mContent->prn && 
							($rdo = $mContent->prn->raw_data_object()) &&
							$rdo->api): ?>
							<span class="status-alternative-2"><?= $vd->esc($rdo->api) ?>&nbsp;</span>
						<?php endif ?>

					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

</div>

<script>
	
$(function() {

	var message = "<strong>Attention!</strong> You are about to reset the \
		PRN distribution status for this content. This will cause the content\
		to reset in the distribution queue and be distributed again.";

	$(".error-reset").on("click", function() {
		var $this = $(this);
		var id = $this.data("id");
		var status = $this.parents("td");
		bootbox.confirm(message, function(res) {
			if (!res) return;
			$this.remove();
			$.post("admin/logs/prn/distribution/reset", { id: id }, function(res) {
				if (!res) return;
				bootbox.alert({
					message: '<strong>Success!</strong> Distribution status reset.',
					className: 'bootbox-success'
				});
			});
		});
	});

});

</script>