<form method="post" id="selectable-form" action="admin/nr_builder/<?= $vd->nr_source ?>/claim_bulk_action">
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span5">
					<h1><?= $vd->heading ?></h1>
				</div>
				<div class="span7">
					<div class="pull-right">
						<button type="submit" name="bulk_confirm_btn" value="1"
							class="btn btn-success">Bulk Confirm</button>

						<button type="submit" name="bulk_reject_btn" value="1"
							class="btn btn-danger btn-export">Bulk Reject</button>

						<button type="submit" name="bulk_ignore_btn" value="1"
							class="btn btn-silver btn-export">Bulk Ignore</button>
					</div>
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

				<div class="span12">
					<div class="pull-right">
						<strong>Filter: </strong>
						<a href="admin/nr_builder/<?= $vd->nr_source ?>/claim_submissions_from_private_link">
							From Private Link
						</a> | 
						<a href="admin/nr_builder/<?= $vd->nr_source ?>/claim_submissions_from_public_link">
							From Public Link
						</a>						
					</div>
				</div>

			</div>
			
			<?= $ci->load->view('admin/nr_builder/partials/claim_submissions_results') ?>
		
		</div>
	</div>
</div>
</form>