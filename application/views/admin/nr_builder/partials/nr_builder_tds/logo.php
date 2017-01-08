<?php if ($vars->result->logo_image_path): ?>
	<td id="td_logo_<?= $vars->result->source_company_id ?>" class="nr-builder-logo">
		<a href="<?= $vars->result->logo_image_path ?>" target="_blank">
			<img src="<?= $vars->result->logo_image_path ?>" alt="logo" width="100">
		</a>
	</td>
<?php else: ?>
	<td class="fail" id="td_logo_<?= $vars->result->source_company_id ?>">
		<a title="No logo found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Logo Image Path"
			data-field="logo_image_path"
			data-tdid="td_logo_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_logo_image_path"></span>
	</td>
<?php endif ?>