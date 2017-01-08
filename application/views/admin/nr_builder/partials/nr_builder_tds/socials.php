<?php if ($vars->result->soc_fb): ?>
	<td class="success grid-social" id="td_soc_fb_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_Facebook_Profile::url($vars->result->soc_fb)) ?>" 
			href="<?= $vd->esc(Social_Facebook_Profile::url($vars->result->soc_fb)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_fb_<?= $vars->result->source_company_id ?>">
		<a title="Facebook not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Facebook"
			data-field="soc_fb"
			data-tdid="td_soc_fb_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_fb"></span>
	</td>
<?php endif ?>

<?php if ($vars->result->soc_twitter): ?>
	<td class="success grid-social" id="td_soc_twitter_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_Twitter_Profile::url($vars->result->soc_twitter)) ?>" 
			href="<?= $vd->esc(Social_Twitter_Profile::url($vars->result->soc_twitter)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_twitter_<?= $vars->result->source_company_id ?>">
		<a title="Twitter not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Twitter"
			data-field="soc_twitter"
			data-tdid="td_soc_twitter_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_twitter"></span>
	</td>
<?php endif ?>

<?php if ($vars->result->soc_linkedin): ?>
	<td class="success grid-social" id="td_soc_linkedin_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_Linkedin_Profile::url($vars->result->soc_linkedin)) ?>" 
			href="<?= $vd->esc(Social_Linkedin_Profile::url($vars->result->soc_linkedin)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_linkedin_<?= $vars->result->source_company_id ?>">
		<a title="Linkedin not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Linkedin"
			data-field="soc_linkedin"
			data-tdid="td_soc_linkedin_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_linkedin"></span>
	</td>
<?php endif ?>


<?php if ($vars->result->soc_gplus): ?>
	<td class="success grid-social" id="td_soc_gplus_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_GPlus_Profile::url($vars->result->soc_gplus)) ?>" 
			href="<?= $vd->esc(Social_GPlus_Profile::url($vars->result->soc_gplus)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_gplus_<?= $vars->result->source_company_id ?>">
		<a title="Google Plus ID not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Google +"
			data-field="soc_gplus"
			data-tdid="td_soc_gplus_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_gplus"></span>
	</td>
<?php endif ?>

<?php if ($vars->result->soc_youtube): ?>
	<td class="success grid-social" id="td_soc_youtube_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_Youtube_Profile::url($vars->result->soc_youtube)) ?>" 
			href="<?= $vd->esc(Social_Youtube_Profile::url($vars->result->soc_youtube)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_youtube_<?= $vars->result->source_company_id ?>">
		<a title="Youtube not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Youtube"
			data-field="soc_youtube"
			data-tdid="td_soc_youtube_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_youtube"></span>
	</td>
<?php endif ?>

<?php if ($vars->result->soc_pinterest): ?>
	<td class="success grid-social" id="td_soc_pinterest_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vd->esc(Social_Pinterest_Profile::url($vars->result->soc_pinterest)) ?>" 
			href="<?= $vd->esc(Social_Pinterest_Profile::url($vars->result->soc_pinterest)) ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>
	<td class="fail grid-social" id="td_soc_pinterest_<?= $vars->result->source_company_id ?>">
		<a title="Pinterest not found" class="tl"
			href="#instant_edit_modal"
			data-id="<?= $vars->result->source_company_id ?>"
			data-title="Pinterest"
			data-field="soc_pinterest"
			data-tdid="td_soc_pinterest_<?= $vars->result->source_company_id ?>">
			<i class="icon-remove"></i></a>
		<span id="text_span_<?= $vars->result->source_company_id ?>_soc_pinterest"></span>
	</td>
<?php endif ?>