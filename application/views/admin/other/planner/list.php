<div class="row-fluid marbot-20">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>PR Planner</h1>
				</div>				
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Planner</th>
						<th>Date</th>
						<th>Finished</th>
						<th>Claimed</th>
						<th>Action</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result">
						<td class="left">
							<?php if ($result->email): ?>
								<strong class="label-class status-info">
									<?= $vd->esc(UUID::nice($result->id)) ?>
								</strong>
								<a href="admin/other/planner/review/<?= $vd->esc($result->id) ?>">
									<?= $vd->esc($result->email) ?>
								</a>
							<?php else: ?>
								<a href="admin/other/planner/review/<?= $vd->esc($result->id) ?>">
									<span class="muted"><?= $vd->esc($result->id) ?></span>
								</a>
							<?php endif ?>
						</td>
						<td>
							<?php $dt_created = Date::out($result->date_created) ?>
							<?=  $dt_created->format('M j, Y') ?>&nbsp;
							<span class="muted"><?=  $dt_created->format('H:i') ?></span>
						</td>
						<td>
							<?php if ($result->is_finished): ?>
								<strong class="status-true">Yes</strong>
							<?php else: ?>
								<span class="status-muted">No</span>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->user): ?>
								<a href="admin/users/view/<?= $result->user->id ?>">
									<?= $vd->esc($result->user->first_name) ?></a>
							<?php else: ?>
								<span>-</span>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->user): ?>
								<a href="admin/other/planner/claim/<?= $result->id ?>/1">Reclaim</a>
							<?php else: ?>
								<a href="admin/other/planner/claim/<?= $result->id ?>">Claim</a>
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Items
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>