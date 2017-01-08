<div class="container-fluid">

	<?= $ci->load->view('manage/publish/pr-list-header') ?>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<?= $ci->load->view('manage/publish/pr-list-tabs') ?>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
					
									<tr>
										<th class="left">Press Release</th>
										<th>Publish Date</th>
										<th>Type</th>
										<th>Status</th>
									</tr>
									
								</thead>
								<tbody>
									
									<?php foreach ($vd->results as $result): ?>
									<?php if ($result->is_under_writing && 
										isset($result->writing_session)): ?>
										<?= $ci->load->view('manage/publish/pr-result-under-writing', 
											array('result' => $result)) ?>
									<?php else: ?>
										<?= $ci->load->view('manage/publish/pr-result',
											array('result' => $result)) ?>
									<?php endif ?>
									<?php endforeach ?>

									<?php if (!count($vd->results)): ?>
									<tr>
										<td colspan="3" class="ta-left">
											No press releases found, 
											<a href="manage/publish/pr/edit">submit</a>
											one now.
										</td>
									</tr>
									<?php endif ?>
									<?= $ci->load->view('manage/publish/partials/pin-content-js') ?>
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
