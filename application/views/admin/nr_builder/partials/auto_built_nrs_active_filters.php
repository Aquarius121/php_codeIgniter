<?php if (@$vd->not_exported || @$vd->already_exported || @$vd->already_existing_nrs 
	|| $vd->auto_built_nrs_prn_valid_not_exported): ?>
<div class="list-filters">
	<div class="list-filter-header">filters active</div>
		<div class="list-filter">
		<div class="name">search</div>
		<div class="value">
			<?php if ($vd->auto_built_nrs_prn_valid_not_exported): ?>
				PRN Valid Leads Not Yet Exported to CSV
			<?php elseif (@$vd->not_exported): ?>
				Not Yet Exported to CSV
			<?php elseif (@$vd->already_exported): ?>
				Already Exported to CSV
			<?php else: ?>
				Already Existing Newsrooms
			<?php endif ?>
			<a class="remove" href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_newsrooms"></a>
		</div>
	</div>
</div>
<?php endif ?>