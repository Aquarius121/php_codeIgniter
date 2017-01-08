<div class="table-responsive">
	<table class="table" id="selectable-results">
		<thead>
			
			<tr>
				<th class="left">Contact</th>
				<th>Company</th>
				<th class="sortable">
					Viewed
					<i class="sorter" data-column="viewed"></i>
				</th>
				<?php if ($vd->clicks): ?>
				<th class="sortable">
					Clicked
					<i class="sorter" data-column="clicked"></i>
				</th>
				<?php endif ?>
			</tr>

			<script>
		
				$(function() {

					var sort = <?= json_encode($vd->sort) ?>;
					var reverse = <?= json_encode($vd->reverse) ?>;
					var sortables = $("#selectable-results i.sorter");

					sortables.each(function() {
						
						var _this = $(this);
						var column = _this.data("column");
						_this.toggleClass("active", sort == column);
						_this.toggleClass("reverse", sort == column && reverse);

						_this.on("click", function() {

							if (sort == column) {
								reverse = !reverse;
							} else {
								sort = column;
								reverse = false;
							}

							window.location = RELATIVE_URI + 
								window.construct_query_string({
									reverse: +reverse,
									sort: sort
								}, true);

						});

					});
					
				});
				
				</script>
			
		</thead>

		<?php if (!$vd->is_search_result): ?>
			<thead>
				<tr>
					<td></td>
					<td></td>
					<td class="pad-10v">
						<a href="manage/analyze/email/save_viewed/<?= $vd->campaign->id ?>"
							class="btn btn-xs btn-default">Save Contacts</a>
					</td>
					<?php if ($vd->clicks): ?>
					<td class="pad-10v">
						<a href="manage/analyze/email/save_clicked/<?= $vd->campaign->id ?>"
							class="btn btn-xs btn-default">Save Contacts</a>
					</td>
					<?php endif ?>
				</tr>
			</thead>
		<?php endif ?>

		<tbody>
			
			<?php foreach ($vd->results as $result): ?>
			<tr>
				<td class="left">
					<?php if ($result->first_name || $result->last_name): ?>
					<div>
						<?= $vd->esc($result->first_name) ?>
						<?= $vd->esc($result->last_name) ?>
					</div>
					<div class="text-muted">
						<?php if ($result->company_id > 0): ?>
						<?= $vd->esc($result->email) ?>
						<?php else: ?>
						<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
							$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
						<?php endif ?>
					</div>
					<?php else: ?>
					<div>
						<?php if ($result->company_id > 0): ?>
						<?= $vd->esc($result->email) ?>
						<?php else: ?>
						<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
							$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
						<?php endif ?>
					</div>
					<?php endif ?>
				</td>
				<td>
					<?php if ($result->company_name): ?>
					<?= $vd->esc($result->company_name) ?>
					<?php else: ?>
					<span>-</span>
					<?php endif ?>
				</td>
				<td>
					<?php if ($result->clicked && !$result->viewed): ?>
						<strong class="label label-info">Yes<sup>&dagger;</sup></strong>
					<?php elseif ($result->viewed): ?>
					<strong class="label label-success">Yes</strong>
					<?php else: ?>
					<strong class="label label-danger">No*</strong>
					<?php endif ?>
				</td>
				<?php if ($vd->clicks): ?>
				<td>
					<?php if ($result->clicked): ?>
					<strong class="label label-success">Yes</strong>
					<?php else: ?>
					<strong class="label label-danger">No</strong>
					<?php endif ?>
				</td>
				<?php endif ?>
			</tr>
			<?php endforeach ?>

			<?php if (!count($vd->results)): ?>
				<tr>
					<td colspan="4" class="ta-left">No contacts found</td>
				</tr>
			<?php endif ?>

		</tbody>
	</table>
</div>