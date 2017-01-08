<form method="post" id="selectable-form" action="admin/nr_builder/prweb/export_auto_built_nrs_to_csv">
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span5">
					<h1>Export Stats</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<?php if (@$vd->not_exported || @$vd->already_exported || @$vd->already_existing_nrs): ?>
<div class="list-filters">
	<div class="list-filter-header">filters active</div>
		<div class="list-filter">
		<div class="name">search</div>
		<div class="value">
			<?php if (@$vd->not_exported): ?>
				Not Yet Exported to CSV
			<?php elseif (@$vd->already_exported): ?>
				Already Exported to CSV
			<?php else: ?>
				Already Existing Newsrooms
			<?php endif ?>
			<a class="remove" href="admin/nr_builder/prweb/auto_built_newsrooms"></a>
		</div>
	</div>
</div>
<?php endif ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">

			<table class="grid">
				<thead>

					<tr>
						<th class="left">Type</th>
						<th>Number of Leads</th>
						<th>Date Exported</th>
						<th>Sales Agent</th>
						<th>Download</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach (@$vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<?= $result->export_type ?>
						</td>
						<td>
							<?= $result->lead_count ?>
						</td>
						
						<td>
							<?php if ($result->date_exported): ?>
								<?php $exported = Date::out($result->date_exported); ?>
								<?= $exported->format('M j, Y') ?>&nbsp;
								<span class="muted"><?= $exported->format('H:i') ?></span>
							<?php else: ?>
								-
							<?php endif ?>
						</td>

						<td>
							<?= $result->agent_first_name ?>
							<?= $result->agent_last_name ?>
						</td>

						<td>
							<?php if ($result->export_type == 'NR'): ?>
								<a href="admin/nr_builder/export_stats/download_nr_csv/<?= $result->id ?>">Download</a>
							<?php else: ?>
								<a href="admin/nr_builder/export_stats/download_verified_csv/<?= $result->id ?>">Download</a>
							<?php endif ?>
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
					exports
				</div>
			</div>

			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>
</form>