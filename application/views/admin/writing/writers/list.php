<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Writers</h1>
				</div>
				<div class="span6">
					<a href="admin/writing/writers/view" class="btn bt-silver pull-right">New Writer</a>
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
						<th class="left">Writer</th>
						<th>Created</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3 class="marbot-5 nopad">
								<a class="view" href="admin/writing/writers/view/<?= $result->id ?>">
									<?= $vd->esc($result->first_name) ?>
									<?= $vd->esc($result->last_name) ?>
								</a>
							</h3>
							<div class="muted">
								<?= $vd->esc($result->email) ?>
							</div>
							<?php if (!$result->email): ?>
							<span class="status-muted">
								<?= $result->id ?>
							</span>
							<?php endif ?>
						</td>
						<td>
							<?php $created = Date::out($result->date_created); ?>
							<?= $created->format('M j, Y') ?>&nbsp;
							<span class="muted"><?= $created->format('H:i') ?></span>
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
					Writers
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>