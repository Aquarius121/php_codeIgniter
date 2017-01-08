<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Content Stats</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				
				<?= $this->load->view('manage/analyze/content/tabs') ?>

				<div class="tab-content">
					<div class="tab-pane fade in active">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
					
									<tr>
										<th class="left">Title</th>
										<?php if ($vd->is_search): ?>
										<th>Type</th>
										<?php endif ?>
										<th>Publish Date</th>
										<th>Views</th>
										<th>PDF Report</th>
									</tr>
									
								</thead>
								<tbody>

									<?php foreach ($vd->results as $result): ?>
									<tr>
										<td class="left">
											<a href="manage/analyze/content/view/<?= $result->id ?>">
												<?= $vd->esc($vd->cut($result->title, 45)) ?>
											</a>
										</td>
										<?php if ($vd->is_search): ?>
										<td>
											<?= Model_Content::short_type($result->type) ?>
										</td>
										<?php endif ?>
										<td>
											<?php $publish = Date::out($result->date_publish); ?>
											<?= $publish->format('M j, Y') ?>
										</td>
										<td>
											<?= $result->hits ?>
										</td>
										<td>
											<?php if ($result->is_published && $result->is_premium 
												&& $result->is_legacy && $result->report_url): ?>
											<a href="<?= $result->report_url ?>">
												<img class="icon" src="<?= $vd->assets_base ?>im/fugue-icons/blue-document-pdf-text.png" />
												Download
											</a>
											<?php elseif ($result->is_published && $result->is_premium 
												&& !$result->is_legacy): ?>
											<a href="manage/analyze/content/report/<?= $result->id ?>">
												<img class="icon" src="<?= $vd->assets_base ?>im/fugue-icons/blue-document-pdf-text.png" />
												Download
											</a>
											<?php else: ?>
											<span>-</span>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>

								</tbody>
							</table>
							
							<script>
							
							$(function() {
								$("#analyze-results td").on("click", function(ev) {
									if ($(ev.target).is("a")) return;
									$(this).parents("tr").find("a")[0].click();
									return false;
								});
							});
							
							</script>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?= $vd->chunkination->render() ?>
	<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Items</p>
</div>