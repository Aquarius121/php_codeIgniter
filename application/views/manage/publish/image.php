<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 page-title">
				<h2>Image Manager</h2>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 actions">
				<a href="manage/publish/image/edit" class="btn btn-primary">Submit Image</a>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<div class="panel-heading">
					<ul id="tabs" class="nav nav-tabs nav-activate tab-links ax-loadable"
						data-ax-elements="#ax-chunkination, #ax-tab-content">
						<li><a data-on="^manage/publish/image/all"  data-toggle="link"
							href="manage/publish/image/all">All</a></li>
						<li><a data-on="^manage/publish/image/published" data-toggle="link"
							href="manage/publish/image/published">Published</a></li>
						<li><a data-on="^manage/publish/image/scheduled" data-toggle="link" 
							href="manage/publish/image/scheduled">Scheduled</a></li>
						<li><a data-on="^manage/publish/image/draft" data-toggle="link"   
							href="manage/publish/image/draft">Draft</a></li>
					</ul>
				</div>

				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active">
						<div class="table-responsive">
							<table class="table" id="selectable-results">
								<thead>
									<tr>
										<th class="left">Image</th>
										<th width="15%">Details</th>
										<th width="20%">Publish Date</th>
										<th width="20%">Status</th>
									</tr>
								</thead>
								<tbody>
					
								<?php foreach ($vd->results as $result): ?>
								<tr>
									<td class="left finger td-has-image">
										<a href="manage/publish/image/edit/<?= $result->id ?>">
											<img src="<?= $result->image_url ?>" />
										</a>
										<h3>
											<a href="manage/publish/image/edit/<?= $result->id ?>">
												<?= $vd->esc($vd->cut($result->title, 45)) ?>
											</a>
										</h3>
										<ul class="actions">
											<li><a href="<?= $result->url() ?>" target="_blank">View</a></li>	
											<li><a href="manage/publish/image/edit/<?= $result->id ?>">Edit</a></li>
											<li><a href="manage/publish/image/delete/<?= $result->id ?>">Delete</a></li>
											<?php if ($this->newsroom->is_active): ?>
											<li><a href="manage/contact/campaign/edit/from/<?= $result->id ?>">Email</a></li>
											<?php endif ?>
											<?php if ($result->is_published): ?>
											<li>
												<a href="manage/analyze/content/view/<?= $result->id ?>">Statistics</a>
											</li>
											<?php endif ?>
											<?= $ci->load->view('manage/publish/partials/listing-pin-content', 
												array('result' => $result), true) ?>
										</ul>
									</td>						
									<td>
										<div><?= $result->image_size ?></div>	
										<div class="text-muted"><?= $result->image_width ?> x 
											<?= $result->image_height ?></div>													
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
									<td class="status">
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
											No images found, 
											<a href="manage/publish/image/edit">submit</a>
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
				<div class="pull-left grid-report">
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