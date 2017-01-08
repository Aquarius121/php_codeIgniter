<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if ($ci->input->get('terms')): ?>
				<h2>Search Results</h2>
				<?php else: ?>
				<h2>Email Stats</h2>
				<?php endif ?>
			</div>
		</div>
	</header>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="table-responsive">			
					<table class="table" id="analyze-results">
						<thead>
						
							<tr>
								<th class="left">Name</th>
								<th>Content</th>
								<th>Send Date<sup>&dagger;</sup></th>
								<th>Sent</th>
								<th>Opened*</th>
							</tr>
							
						</thead>
						<tbody>
							
							<?php foreach ($vd->results as $k => $result): ?>
							<tr>
								<td class="left">
									<a href="manage/analyze/email/view/<?= $result->id ?>">
										<?= $vd->esc($vd->cut($result->name, 40)) ?>
									</a>
								</td>
								<td>
									<?php if ($result->content_type): ?>
									<span><?= Model_Content::full_type($result->content_type) ?></span>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
								<td>
									<?php $deliver = Date::out($result->date_send); ?>
									<?= $deliver->format('M j, Y') ?>
								</td>
								<td>
									<span><?= (int) $result->contact_count ?></span>
								</td>
								<td>
									<span><?= (int) $result->views ?>+</span>
								</td>
							</tr>
							<?php endforeach ?>

						</tbody>
					</table>
				</div>
			</div>

			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					* Some email clients do not allow views to be tracked.
					<br />&dagger; Assumes that the content is published.
				</div>
			</div>
		</div>
	</div>
			
	<?= $vd->chunkination->render() ?>
	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Campaigns
	</p>

</div>