<fieldset class="relevant_resources section-requires-premium" id="relevant-resources">
	<legend>
		Additional Links
		<a data-toggle="tooltip" class="tl" href="#" 
			title="<?= Help::WEB_LINKS ?>">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>	
	</legend>
	<div class="header-help-block">Include additional links related to your company.</div>
	<?= $ci->load->view('manage/publish/partials/requires-premium') ?>
	<div class="row form-group">
		<div class="col-lg-5 col-md-6 col-sm-6 rr-title">
			<input class="form-control in-text col-lg-12" type="text" 
				value="<?= $vd->esc(@$vd->m_content->rel_res_pri_title) ?>"
				name="rel_res_pri_title" 
				placeholder="Resource Title" />
		</div>
		<div class="col-lg-7 col-md-6 col-sm-6 rr-link">
			<input class="form-control in-text col-lg-12 url" type="url" 
				value="<?= $vd->esc(@$vd->m_content->rel_res_pri_link) ?>"
				name="rel_res_pri_link" 
				placeholder="Resource Link" />
		</div>
	</div>

	<div class="row form-group rr-sec disabled">
		<div class="col-lg-5 col-md-6 col-sm-6 rr-title">
			<input class="form-control in-text col-lg-12" type="text"
				value="<?= $vd->esc(@$vd->m_content->rel_res_sec_title) ?>" 
				name="rel_res_sec_title" 
				placeholder="Resource Title" disabled />
		</div>
		<div class="col-lg-7 col-md-6 col-sm-6 rr-link">
			<input class="form-control in-text col-lg-12 url" type="url" 
				value="<?= $vd->esc(@$vd->m_content->rel_res_sec_link) ?>"
				name="rel_res_sec_link" 
				placeholder="Resource Link" disabled />
		</div>
	</div>
</fieldset>
<script>
	
defer(function() {
	
	var rr_boxes = $("#relevant-resources input");
	var rr_sec = $("#relevant-resources .rr-sec");
	
	if (rr_boxes.val()) {
		// already have a value => enable all
		rr_boxes.attr("disabled", false);		
		rr_sec.removeClass("disabled");
	}
		
	// a value is provided => enable all
	rr_boxes.on("change", function() {
		if ($(this).val()) {
			rr_boxes.attr("disabled", false);
			rr_sec.removeClass("disabled");
		}
	});
	
});
	
</script>