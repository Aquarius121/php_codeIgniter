<?php if ($vars->result->contact_page_url): ?>
	<td class="success" id="td_contact_page_url_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->contact_page_url ?>" href="<?= $vars->result->contact_page_url ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail" id="td_contact_page_url_<?= $vars->result->source_company_id ?>">
		<a title="No URL found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Contact Page URL"
			data-field="contact_page_url"
			data-tdid="td_contact_page_url_<?= $vars->result->source_company_id ?>"
			>
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_contact_page_url"></span>
	</td>
<?php endif ?>