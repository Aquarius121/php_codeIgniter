<input type="hidden" name="instant_edit_field" id="instant_edit_field">
<input type="hidden" name="instant_edit_company_id" id="instant_edit_company_id">
<input type="hidden" name="instant_td_id" id="instant_td_id">

<div id="instant_edit_modal" 
	class="modal hide fade modal-autoheight" tabindex="-1" style="margin-top: 75px;"
	role="dialog" aria-labelledby="modalLabel" aria-hidden="true">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" 
			aria-hidden="true">
			<i class="icon-remove"></i></button>
		<h3 id="modalLabel">Edit</h3>
	</div>

	<div class="modal-body">
		<div class="span6">
			<section class="form-section">
				<div class="row-fluid">
					<div class="span12 relative">
						<strong>Company Name: </strong>
						<span id="instant_company_name"></span>
					</div>
				</div>

				<div class="row-fluid" id="instant_edit_text_div">
					<div class="span12 relative text_box_div">
						<input type="text" name="instant_edit_text"
							class="inline-edit-text span12 in-text" id="instant_edit_text"
							value=""
							onblur = "$('#instant_save_button').trigger('click');"
							onkeyup = "if (event.keyCode == 13){ event.preventDefault(); $('#instant_save_button').trigger('click'); }" 
							onkeydown = "if (event.keyCode == 13){ event.preventDefault(); }" />
						<strong class="placeholder" id="instant_edit_placeholder"></strong>
					</div>
				</div>

				<section id="instant_edit_about" class="dnone">
					<div class="row-fluid">
						<div class="span12 relative">
							<textarea name="instant_edit_short_description" id="instant_edit_short_description"
								class="span12 in-text has-placeholder" 
								placeholder="Company Summary"></textarea>						
						</div>
					</div>
										
					<div class="row-fluid">
						<div class="span12 relative">
							<textarea name="instant_edit_about_company" id="instant_edit_about_company"
								class="span12 in-text has-placeholder" 
								placeholder="About Company"></textarea>
						</div>
					</div>
					
				</section>

				<section id="instant_edit_address_section" class="dnone">
					<div class="row-fluid">
						<div class="span12 relative">
							<input type="text" name="instant_edit_address"
								class="span12 in-text" id="instant_edit_address"
								value="" placeholder="Address" />
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="instant_edit_city"
								class="span12 in-text" id="instant_edit_city"
								value="" placeholder="City" />
						</div>

						<div class="span6 relative">
							<input type="text" name="instant_edit_state"
								class="span12 in-text" id="instant_edit_state"
								value="" placeholder="State"  />
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="instant_edit_zip"
								class="span12 in-text" id="instant_edit_zip"
								value="" placeholder="Zip"  />
						</div>

						<div class="span6 relative" id="select-country">
							<select class="show-menu-arrow span12" name="instant_edit_country_id">
								<option class="selectpicker-default" title="Select Country" value=""
									'selected'>None</option>
								<?php foreach ($vd->countries as $country): ?>
								<option value="<?= $country->id ?>">
									<?= $vd->esc($country->name) ?>
								</option>
								<?php endforeach ?>
							</select>
							<script>

							$(function() {
								
								$("#select-country select")
									.on_load_select({ size: 5 });
								
							});
							
							</script>
						
						</div>
					</div>	
				</section>

				
				<div class="row-fluid">
					<div class="span4"></div>
					<div class="span4">
						<button type="submit" name="save" value="1" id="instant_save_button" 
							name="instant_save_button" class="span12 bt-orange pull-right">Save</button>
					</div>
				</div>

			</section>
		</div>
	</div>
</div>