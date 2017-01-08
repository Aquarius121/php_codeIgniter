<?php if ($vars->result->website): ?>
	<td class="success" id="td_website_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->website ?>" href="<?= $vars->result->website ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_website_<?= $vars->result->source_company_id ?>">
		<a title="No URL found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Website URL"
			data-field="website"
			data-tdid="td_website_<?= $vars->result->source_company_id ?>"><i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_website"></span>
	</td>
<?php endif ?>