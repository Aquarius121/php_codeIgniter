<fieldset class="form-section pr-source">
	<legend>
		PR Source
		<a data-toggle="tooltip" class="tl" href="#" 
			title="The Source for the Press Release (typically company/organization discussed in PR). 
				Source Attribution must be in headline, sub-headline or first paragraph.
				PR Agencies/Firm please use 'Your Agency Name' on Behalf of 'XYZ Company'.">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>	
	</legend>
	<div class="header-help-block">The company/organization discussed in the release.</div>
	<div class="row form-group">
		<div class="col-lg-12">
			<input name="source" placeholder="Source of the PR"
				value="<?= $vd->esc(@$vd->m_content->source) ?>"
				class="form-control in-text col-lg-12 required" type="text"  />
		</div>
	</div>
</fieldset>
