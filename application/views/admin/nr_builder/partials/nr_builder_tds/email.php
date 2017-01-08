<?php if ($vars->result->email): ?>
	<td class="success" id="td_email_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->email ?>" 
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_email_<?= $vars->result->source_company_id ?>">
		<a title="No email found" class="tl" 
			href="#instant_edit_modal" 
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Email Address"
			data-field="email"
			data-tdid="td_email_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_email"></span>
	</td>
<?php endif ?>