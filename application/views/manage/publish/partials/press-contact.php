<div id="press-contact-container">
	<fieldset class="form-section press-contact">
		<legend>
			Press Contact
			<a data-toggle="tooltip" class="tl" href="#" 
				title="Contact details of the person that the press/media should contact.">
				<i class="fa fa-fw fa-question-circle"></i>
			</a>	
		</legend>
		<div class="header-help-block">Press contact updates will
			apply to <span class="status-black">all content</span>
			within this company profile. Please include press contact
			details within the PR body if you would like them 
			visible on the distributed release. 
			</div>
		<div class="row form-group">
			<div class="col-lg-6">
				<input name="press_contact_first_name" placeholder="First Name"
					value="<?= $vd->esc(@$vd->press_contact->first_name) ?>"
					class="form-control in-text col-lg-12 press-contact-required" type="text"  />
			</div>
			<div class="col-lg-6">
				<input name="press_contact_last_name" placeholder="Last Name"
					value="<?= $vd->esc(@$vd->press_contact->last_name) ?>"
					class="form-control in-text col-lg-12 press-contact-required" type="text"  />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-lg-6">
				<input name="press_contact_email" placeholder="Email Address"
					value="<?= $vd->esc(@$vd->press_contact->email) ?>"
					class="form-control in-text col-lg-12 press-contact-required" type="email"  />
			</div>
			<div class="col-lg-6">
				<input name="press_contact_phone" placeholder="Phone Number"
					value="<?= $vd->esc(@$vd->press_contact->phone) ?>"
					class="form-control in-text col-lg-12" type="text"  />
			</div>
		</div>
	</fieldset>
</div>

<script>
	
$(function() {

	var container = $("#press-contact-container");
	var fieldset = container.children().eq(0);
	var requiredFields = $(".press-contact-required");

	fieldset.detach();

	window.on_distribution_bundle_change.push(function(bundle) {

		var required = bundle && bundle.data.requiresPressContact;
		requiredFields.toggleClass("required", required);

		if (required) 
		     container.append(fieldset);
		else fieldset.detach();

	});

});

</script>