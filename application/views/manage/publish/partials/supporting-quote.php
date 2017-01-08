<fieldset class="form-section supporting_quote">
	<legend>
		Supporting Quote
		<a data-toggle="tooltip" class="tl" href="#" 
			title="<?= Help::WEB_SQ ?>">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>	
	</legend>
	<div class="row form-group">
		<div class="col-lg-12">
			<textarea class="form-control in-text col-lg-12" name="supporting_quote" 
				rows="5" placeholder="Enter Supporting Quote" maxlength="600"><?= 
				$vd->esc(@$vd->m_content->supporting_quote)
			?></textarea>
			<p class="help-block">Supporting quote box is for purposes of highlighting a quote in the release,
				this box is just for Newswire and may not be included with distribution partners.</p>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-lg-6">
			<input name="supporting_quote_name" placeholder="Name of Person"
				value="<?= $vd->esc(@$vd->m_content->supporting_quote_name) ?>"
				class="form-control in-text col-lg-12" type="text"  />
		</div>
		<div class="col-lg-6">
			<input name="supporting_quote_title" placeholder="Title of Person"
				value="<?= $vd->esc(@$vd->m_content->supporting_quote_title) ?>"
				class="form-control in-text col-lg-12" type="text"  />
		</div>
	</div>
</fieldset>
