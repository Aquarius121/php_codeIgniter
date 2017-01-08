<form method="post" id="selectable-form" action="admin/nr_builder/<?= $vd->nr_source ?>/">
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span12">
					<h1><?= $vd->heading ?></h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/nr_builder/partials/sub_menu') ?>
<?= $this->load->view('admin/partials/filters') ?>

<?php if (@$vd->claim_filter): ?>
<div class="list-filters">
	<div class="list-filter-header">filters active</div>
		<div class="list-filter">
		<div class="name">search</div>
		<div class="value">
			<?php if (@$vd->claim_filter == "not_exported"): ?>
				Not Yet Exported to CSV
			<?php elseif(@$vd->claim_filter == "already_exported"): ?>
				Already Exported to CSV
			<?php elseif(@$vd->claim_filter == "from_private_link"): ?>
				From Private Link
			<?php elseif(@$vd->claim_filter == "from_public_link"): ?>
				From Public Link
			<?php endif ?>
			<a class="remove" 
				href="admin/nr_builder/<?= $vd->nr_source ?>/claim_submissions"></a>
		</div>
	</div>
</div>
<?php endif ?>


<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			<div class="row-fluid">
				<div class="span12">
					<div class="pull-right">
						View: 
						<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/20/<?= $vd->tab_filter ?><?= 
							$vd->esc(gstring()) ?>"
							<?= value_if_test($vd->listing_size == 20, " class='strong' ") ?>>20</a> | 
						<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/50/<?= $vd->tab_filter ?><?= 
							$vd->esc(gstring()) ?>"
							<?= value_if_test($vd->listing_size == 50, " class='strong' ") ?>>50</a> | 
						<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/100/<?= $vd->tab_filter ?><?= 
							$vd->esc(gstring()) ?>"
							<?= value_if_test($vd->listing_size == 100, " class='strong' ") ?>>100</a> | 

						<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/475/<?= $vd->tab_filter ?><?= 
							$vd->esc(gstring()) ?>"
							<?= value_if_test($vd->listing_size == 475, " class='strong' ") ?>>475</a>
					</div>
				</div>
			</div>
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left td-max-20">Company</th>
						<th>Rep Detail</th>
						<th>IP</th>
						<th>Paid Date</th>
					</tr>

				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result" id="row_<?= $result->id ?>">
						
						<td class="left">
							<h3>
								<a class="view" href="<?= $result->url('manage') ?>" target="_blank">
									<?= $vd->esc($vd->cut($result->company_name, 45)) ?>
								</a>
							</h3>	
							<ul>
								<li><a href="<?= $result->url() ?>" target="_blank">NR</a></li>
								<li><a href="<?= $result->url('manage/newsroom/customize') ?>" 
										target="_blank">Customize</a></li>
							</ul>
							
						</td>
						
						<td>
							<?= $vd->esc($result->claimant_rep_name) ?>
							<div><?= $vd->esc($result->claimant_email) ?></div>
						</td>

						<td>
							<?= $result->remote_addr ?>
						</td>
						<td>
							<?php $date_claimed = Date::out($result->date_claimed); ?>
							<?= $date_claimed->format('M j, Y') ?>
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
					Newsroom Claims
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>
</form>