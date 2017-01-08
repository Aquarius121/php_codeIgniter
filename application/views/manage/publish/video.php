<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 page-title">
				<h2>Video Manager</h2>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 actions">
				<a href="manage/publish/video/edit" class="btn btn-primary">Submit Video</a>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<div class="panel-heading">
					<ul class="nav nav-tabs nav-activate tab-links ax-loadable" 
						data-ax-elements="#ax-chunkination, #ax-tab-content" id="tabs">
						<li><a data-on="^manage/publish/video/all" data-toggle="link"
							href="manage/publish/video/all">All</a></li>
						<li><a data-on="^manage/publish/video/published" data-toggle="link"
							href="manage/publish/video/published">Published</a></li>
						<li><a data-on="^manage/publish/video/scheduled" data-toggle="link"
							href="manage/publish/video/scheduled">Scheduled</a></li>
						<li><a data-on="^manage/publish/video/draft" data-toggle="link"
							href="manage/publish/video/draft">Draft</a></li>
					</ul>
				</div>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>

									<tr>
										<th class="left">Video</th>
										<th>Publish Date</th>
										<th>Status</th>
									</tr>

								</thead>
								<tbody>
									
									<?php foreach ($vd->results as $result): ?>
									<tr>
										<td class="left td-has-image finger">
											<a href="manage/publish/video/edit/<?= $result->id ?>">
												<img src="<?= $result->image_url ?>" />
											</a>
											<h3>
												<a href="manage/publish/video/edit/<?= $result->id ?>">
													<?= $vd->esc($vd->cut($result->title, 45)) ?>
												</a>
											</h3>
											<ul class="actions">
												<li><a href="<?= $result->url() ?>" target="_blank">View</a></li>	
												<li><a href="manage/publish/video/edit/<?= $result->id ?>">Edit</a></li>
												<?php if (Auth::is_admin_online()): ?>
												<li><a href="manage/publish/video/fork/<?= $result->id ?>">Fork</a></li>
												<?php endif ?>
												<li><a href="manage/publish/video/delete/<?= $result->id ?>">Delete</a></li>
												<?php if ($this->newsroom->is_active): ?>
												<li><a href="manage/contact/campaign/edit/from/<?= $result->id ?>">Email</a></li>
												<?php endif ?>
												<?php if ($result->is_published): ?>
												<li><a href="manage/analyze/content/view/<?= $result->id ?>">Statistics</a></li>
												<?php endif ?>
												<?= $ci->load->view('manage/publish/partials/listing-pin-content', 
													array('result' => $result), true) ?>
											</ul>
										</td>
										<td>
											<?php if ($result->is_draft): ?>
											<span>-</span>
											<?php else: ?>
											<?php $publish = Date::out($result->date_publish); ?>
											<?= $publish->format('M j, Y') ?>&nbsp;
											<span class="text-muted"><?= $publish->format('H:i') ?></span>
											<?php endif ?>
										</td>
										<td>
											<?php if ($result->is_published): ?>
											<?php if ($ci->newsroom->is_active): ?>
											<span class="label label-success">Published</span>
											<?php else: ?>
											<span class="label label-success">Published*</span>
											<?php endif ?>
											<?php elseif ($result->is_under_review): ?>
											<span class="label label-info">Under Review</span>
											<?php elseif ($result->is_draft): ?>
											<span class="label label-default">Draft</span>
											<?php else: ?>
											<span class="label label-info">Scheduled</span>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>

									<?php if (!count($vd->results)): ?>
									<tr>
										<td colspan="3" class="ta-left">
											No videos found, 
											<a href="manage/publish/video/edit">submit</a>
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
			<div class="clearfix">
				<?php if (!$ci->newsroom->is_active): ?>
				<div class="pull-left grid-report ta-left">
					* Private, newsroom is not active.
				</div>
				<?php endif ?>
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