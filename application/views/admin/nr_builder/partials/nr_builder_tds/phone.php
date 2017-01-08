<?php if ($vars->result->phone): ?>
	<td class="success" id="td_phone_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->phone ?>" 
			class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_phone_<?= $vars->result->source_company_id ?>">
		<a title="No phone number found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Phone"
			data-field="phone"
			data-tdid="td_phone_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_phone"></span>
	</td>
<?php endif ?>