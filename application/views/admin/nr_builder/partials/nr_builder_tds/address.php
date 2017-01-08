<?php if ($vars->result->address): ?>
	<td class="success" id="td_address_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->address ?> 
			<?= value_if_test($vars->result->city, ' - '.$vars->result->city)  ?><?= 
			value_if_test($vars->result->state, ', '.$vars->result->state)  ?><?= 
			value_if_test($vars->result->zip, ', '.$vars->result->zip)  ?>"
			class="tl" target="_blank"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_address_<?= $vars->result->source_company_id ?>">
		<a title="No address found" class="tl"
			data-toggle="modal"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Address"
			data-field="address"
			data-tdid="td_address_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
	</td>
<?php endif ?>