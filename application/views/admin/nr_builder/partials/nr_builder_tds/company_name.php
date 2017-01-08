<h3>
	<span id="company_name_<?= $vars->result->source_company_id ?>">
		<?= $vd->esc($vd->cut($vars->result->name, 45)) ?>
		<?php if (!empty($vars->result->num_prs)): ?>
		<a href="#" class="show-prs" 
			data-id="<?= $vars->result->source_company_id ?>">
			<strong class="admin-approve">(<?= $vars->result->num_prs ?>)
			</strong>
		</a>
		<?php endif ?>
	</span>
</h3>

<ul>
	<li><a href="#" class="inline-edit" 
			data-id="<?= $vars->result->source_company_id ?>">Edit
		</a></li>
	<li><a href="admin/nr_builder/<?= $vd->nr_source ?>/delete/<?= $vars->result->source_company_id?>"
		>Delete</a></li>
</ul>