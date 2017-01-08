<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 page-title">
				<h2>Search Results</h2>
			</div>
		</div>
	</header>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<div class="panel-heading">
					<ul id="tabs" class="nav nav-tabs nav-activate tab-links ax-loadable"
						data-ax-elements="#ax-chunkination, #ax-tab-content">
						<li>
							<a data-on="^manage/publish/search/all" data-toggle="link"
								href="<?= $ci->add_current_qs('manage/publish/search/all') ?>">
								All
							</a>
						</li>
						<li>
							<a data-on="^manage/publish/search/published" data-toggle="link"
								href="<?= $ci->add_current_qs('manage/publish/search/published') ?>">
								Published
							</a>
						</li>
						<li>
							<a data-on="^manage/publish/search/scheduled" data-toggle="link"
								href="<?= $ci->add_current_qs('manage/publish/search/scheduled') ?>">
								Scheduled
							</a>
						</li>
						<li>
							<a data-on="^manage/publish/search/draft" data-toggle="link"
								href="<?= $ci->add_current_qs('manage/publish/search/draft') ?>">
								Draft
							</a>
						</li>
					</ul>
				</div>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
					
									<tr>
										<th class="left">Content</th>
										<th>Type</th>
										<th>Publish Date</th>
										<th>Status</th>
									</tr>
									
								</thead>
								<tbody>
									
									<?php foreach ($vd->results as $result): ?>
									<tr>
										<td class="left">
											<h3>
												<?php if ($result->type == Model_Content::TYPE_SOCIAL): ?>
													<a href="<?= $result->url() ?>">
														<?= $vd->esc($vd->cut($result->title, 45)) ?>
													</a>
												<?php else: ?>
													<a href="manage/publish/<?= $result->type ?>/edit/<?= $result->id ?>">
														<?= $vd->esc($vd->cut($result->title, 45)) ?>
													</a>
												<?php endif ?>
											</h3>

											<?php if ($result->type !== Model_Content::TYPE_SOCIAL): ?>
												<ul class="actions">
													<li><a href="<?= $result->url() ?>" target="_blank">View</a></li>
													<li><a href="manage/publish/<?= $result->type ?>/edit/<?= $result->id ?>">Edit</a></li>
													<?php if (Auth::is_admin_online()): ?>
													<li><a href="manage/publish/<?= $result->type ?>/fork/<?= $result->id ?>">Fork</a></li>
													<?php endif ?>
													<li><a href="manage/publish/<?= $result->type ?>/delete/<?= $result->id ?>">Delete</a></li>
													<li><a href="manage/contact/campaign/edit/from/<?= $result->id ?>">Email</a></li>
													<?php if ($result->is_published): ?>
													<li><a href="manage/analyze/content/view/<?= $result->id ?>">Statistics</a></li>
													<?php endif ?>
												</ul>
											<?php endif ?>
										</td>
										<td>
											<?= Model_Content::full_type($result->type) ?>
										</td>
										<td>
											<?php $publish = Date::out($result->date_publish); ?>
											<?= $publish->format('M j, Y') ?>&nbsp;
											<span class="muted"><?= $publish->format('H:i') ?></span>
										</td>
										<td>
											<?php if ($result->is_published): ?>
											<?php if ($ci->newsroom->is_active || 
												$result->type === Model_Content::TYPE_PR): ?>
											<span class="label label-success">Published</span>
											<?php else: ?>
											<span class="label label-success">Published*</span>
											<?php endif ?>
											<?php elseif ($result->is_under_review): ?>
											<span class="label label-info">Under Review</span>
											<?php elseif ($result->is_draft): ?>
											<span class="label label-default">Saved Draft</span>
											<?php else: ?>
											<span class="label label-info">Scheduled</span>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>

									<?php if (!count($vd->results)): ?>
									<tr>
										<td colspan="3" class="ta-left">
											No content found.
										</td>
									</tr>
									<?php endif ?>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<?php if (!$ci->newsroom->is_active): ?>
				<div class="clearfix">
					<div class="pull-left grid-report ta-left">
						* Private, newsroom is not active.
					</div>
				</div>
			<?php endif ?>

		</div>

		<div id="ax-chunkination">
			<div class="ax-loadable" 
				data-ax-elements="#ax-chunkination, #ax-tab-content">
				<?= $vd->chunkination->render() ?>
			</div>

			<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
				of <?= $vd->chunkination->total() ?> Results</p>
		</div>

	</div>
</div>