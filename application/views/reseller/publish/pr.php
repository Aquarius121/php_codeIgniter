<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Press Release Manager</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
						<a href="manage/publish/pr/edit" class="bt-publish bt-orange">Submit Press Release</a>
					</div>
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
						<th class="left">Press Release Title</th>
						<th>Publish Date</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
					
				</thead>
				<tbody>
					
					<?php foreach ($vd->results as $result): ?>
					<tr>
						<td class="left">
							<h3>
								<a href="reseller/publish/edit/<?= $result->id ?>">
									<?= $vd->esc($vd->cut($result->title, 45)) ?>
								</a>
							</h3>
							<ul>
								<li><a href="<?= $result->url ?>" target="_blank">View</a></li>
								<li><a href="reseller/publish/edit/<?= $result->id ?>">Edit</a></li>
							</ul>
						</td>
						<td>
							<?php if (!$result->is_draft): ?>
							<?php $publish = Date::out($result->date_publish); ?>
							<?= $publish->format('M j, Y') ?>&nbsp;
							<span class="muted"><?= $publish->format('H:i') ?></span>
							<?php else: ?>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->is_premium): ?>
							<span>Premium</span>
							<?php else: ?>
							<span>Basic</span>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->is_published): ?>
							<span>Published</span>
							<?php elseif ($result->is_under_review): ?>
							<span>Under Review</span>
							<?php elseif ($result->is_draft): ?>
								<span>Draft</span>
								<?php if ($result->is_rejected): ?>
								<div class="status-false smaller">Rejected</div>
								<?php endif ?>
							<?php else: ?>
								<span>Scheduled</span>								
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="grid-report">Displaying <?= count($vd->results) ?> 
				of <?= $vd->chunkination->total() ?> Press Releases</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>