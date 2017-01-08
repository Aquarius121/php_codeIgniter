<div class="row-fluid marbot-20">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>RSS Feed</h1>
				</div>
				<div class="span6">
					<a href="admin/settings/rss_feed/edit" 
						class="bt-orange pull-right">New RSS Feed</a>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">

			<table class="grid fin-services-grid">
				<thead> 

					<tr>
						<th class="left">Feed Name</th>
						<th>Feed Link</th>
						<th>Status</th>
						<th>Action</th>
					</tr>

				</thead>
				<tbody class="results">

					<?php foreach ($vd->results as $result): ?>
					<tr>
						<td class="left">
							<h3 class="nopadbot">
								<a href="<?= $vd->esc("custom_rss/{$result->slug}") ?>">
									<?= $vd->esc($result->name) ?>
								</a>
							</h3>
						</td>

						<td>
							<a href="<?= $ci->website_url("custom_rss/{$result->slug}") ?>" target="_blank">Link</a>
						</td>

						<td>
							<?php if ($result->is_enabled): ?>
								Enabled
							<?php else: ?>
								Disabled
							<?php endif ?>
						</td>

						<td>
							<a href="admin/settings/rss_feed/edit/<?= $result->id ?>">Edit</a> |
							<a href="admin/settings/rss_feed/delete/<?= $result->id ?>">Delete</a>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>

			<div class="clearfix">
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Sites
				</div>
			</div>
			<?= $vd->chunkination->render() ?>
		</div>
	</div>
</div>