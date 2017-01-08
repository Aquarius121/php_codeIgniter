<div class="span12">
	<div class="pull-right">
		<strong>Filter: </strong>
		<?php if ($vd->check_prn_sop_valid_lead): ?>
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_prn_valid_not_exported">
			PRN Valid Leads Not Yet Exported to CSV
		</a> | 
		<?php endif ?>
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_not_exported">
			Not Yet Exported to CSV
		</a> | 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_already_exported">
			Already Exported to CSV
		</a> | 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_already_existing">
			Already Existing Newsrooms
		</a>
	</div>
</div>