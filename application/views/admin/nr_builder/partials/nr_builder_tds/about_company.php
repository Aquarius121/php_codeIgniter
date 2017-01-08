<?php if ($vars->result->short_description || $vars->result->about_company): ?>
	<td class="success" id="td_about_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->about_company ? strip_tags($vars->result->about_company) :
					strip_tags($vars->result->short_description) ?>" 
			class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_about_<?= $vars->result->source_company_id ?>">
		<a title="About company blurb not found" class="tl"
			data-toggle="modal"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="About Company"
			data-field="about_the_company"
			data-tdid="td_about_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
	</td>
<?php endif ?>