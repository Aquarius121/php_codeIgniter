<div class="container-fluid">

	<?= $ci->load->view("manage/publish/{$vd->type}-list-header") ?>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<?= $ci->load->view("manage/publish/{$vd->type}-list-tabs") ?>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active" id="pr-<?= $ci->uri->segment(4) ?>">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
					
									<tr>
										<th class="left">Press Release</th>
										<th>Date Saved</th>
									</tr>
									
								</thead>
								<tbody>
									
									<?php foreach ($vd->results as $result): ?>
									<tr>
										<td class="left">
											<h3>
												<a href="manage/publish/pr/autosave/edit/<?= $result->id ?>">
													<?php if ($result->data->title): ?>
													<?= $vd->esc($vd->cut($result->data->title, 45)) ?>
													<?php else: ?>
														<?= $vd->esc(Model_Content::DEFAULT_TITLE) ?>
													<?php endif ?>
												</a>
											</h3>
											<ul class="actions">
												<li><a href="manage/publish/pr/autosave/edit/<?= $result->id ?>">Edit</a></li>
												<li><a href="manage/publish/pr/autosave/delete/<?= $result->id ?>">Delete</a></li>
											</ul>
										</td>
										<td>
											<?php $created = Date::out($result->date_created); ?>
											<?= $created->format('M j, Y') ?>&nbsp;
											<span class="text-muted"><?= $created->format('H:i') ?></span>
										</td>
									</tr>
									<?php endforeach ?>

									<?php if (!count($vd->results)): ?>
									<tr>
										<td colspan="3" class="ta-left">
											No recent autosaves.
										</td>
									</tr>
									<?php endif ?>

								</tbody>
							</table>
						</div>
					</div>
				</div>
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
			of <?= $vd->chunkination->total() ?> Results
		</p>
	</div>
		
</div>