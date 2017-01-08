<ul class="breadcrumb no-print nomarbot">
	<li><a href="manage/contact">Media Outreach</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/contact/contact">Contacts</a> <span class="divider">&raquo;</span></li>
	<li class="active">Import</li>
</ul>

<div class="container-fluid">
	<header class="form-col">
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 page-title">
				<h2>Import Contacts</h2>
			</div>
			<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 actions">
				<ul class="list-inline actions">
					<li><a href="<?= $vd->assets_base ?>other/template.csv" class="btn btn-primary">Download Template</a></li>
					<li><a href="<?= $vd->assets_base ?>other/example.csv" class="btn btn-default">Example</a></li>
				</ul>
			</div>
		</div>
	</header>
	
	<form class="tab-content" method="post" id="import-form" action="manage/contact/import/save">
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default form-col">
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-8 col-md-8 form-col-1">
							
							
								
								<div class="information-panel">
									
									<fieldset class="form-section company-logo">
										<legend>Select CSV File</legend>
										<input type="hidden" class="required" id="stored-file-id"
											data-required-name="CSV File" name="stored_file_id" />
										<input type="hidden" id="filename" name="filename" />
										<div class="row">
											<div class="col-md-12 marbot-15" id="csv-please-wait" style="display: none">
												<div class="alert alert-warning nomarbot">
													Please wait while the file is uploaded.
												</div>
											</div>
										</div>
										<div id="csv-upload" class="row form-group">
											<div class="col-md-12 file-upload-faker">
												<div class="fake row not-row">
													<div class="input-group">
														<div class="text-input">
															<input type="text" placeholder="Select File" 
																name="fake_file_name" class="form-control in-text fake-text" />
														</div>
														<div class="input-group-btn">
															<button class="btn btn-primary fake-button pull-right nomar" type="button">Browse</button>
														</div>
													</div>
												</div>
												<div class="real row">
													<input class="form-control in-text real-file required-no-submit" type="file" name="csv" />
												</div>
											</div>
										</div>
									
										<script>

										$(function() {
											
											var import_button = $("#import-button");
											var csv_upload = $("#csv-upload");
											var preview = $("#preview");
											
											preview.hide();
											
											csv_upload.find(".real-file").on("change", function() {
												
												var real_file = $(this);
												var fake_text = csv_upload.find(".fake-text");
												var please_wait = $("#csv-please-wait");
												
												fake_text.removeClass("error");
												fake_text.addClass("loader");
												fake_text.val(real_file.val());
												real_file.attr("disabled", true);
												please_wait.slideDown();
												please_wait.addClass("enabled");
												
												var id_input = $("input#stored-file-id");
												var filename_input = $("input#filename");
												
												var on_upload = function(res) {
													
													please_wait.hide();
													please_wait.removeClass("enabled");
													
													if (res.stored_file_id)
													{
														real_file.attr("disabled", false);
														fake_text.removeClass("loader");
														id_input.val(res.stored_file_id);
														filename_input.val(res.filename);
														import_button.attr("disabled", false);
														import_button.addClass("btn-success");
													
														preview.empty();
														preview.html(res.preview);
														preview.show();
														
														//window.location.hash = "preview";
														window.location.hash = "add-list";
													}
													else
													{
														fake_text.addClass("error");
														real_file.attr("disabled", false);
														fake_text.removeClass("loader");
														import_button.attr("disabled", true);
														import_button.removeClass("btn-primary");
														import_button.addClass("btn");
													}
													
												};
												
												setTimeout(function() {
													real_file.ajax_upload({
														callback: on_upload,
														url: "manage/contact/import/store_csv"
													})}, 1000);
												
											});
											
										});

										</script>
									</fieldset>										
									
									<?= $ci->load->view('manage/contact/partials/contact_lists', 
										array('is_from_import_form' => 1)) ?>
									
								</div>
								
							
								
							
								<script>
								
								$(function() {
								
									var import_button = $("#import-button");
									
									var update_progress = function() {
										$.get("manage/contact/import/progress", function(res) {
											import_button.text(res + " Processed");
											setTimeout(update_progress, 250);
										});
									};
									
									$("#import-form").on("submit", function() {
										import_button.prop("disabled", true);
										import_button.removeClass("btn-primary");
										import_button.addClass("btn");
										import_button.text("0 Processed");
										setTimeout(update_progress, 250);					
									});
									
								});			
								
								</script>
							</div>

							<div class="col-lg-4 col-md-4 form-col-2">
								 <div class="panel-body nopad">
								 	<div class="alert alert-info">
										The uploaded CSV file must be in the correct format. 
										Each line should have an <strong>email address</strong> and can 
										have optional <strong>first name</strong>, <strong>last name</strong>,
										<strong>company name</strong> and <strong>title</strong> fields.
									</div>

									<div class="row form-group">
										<div class="col-lg-12">
											<button class="btn pull-right" 
												type="submit" id="import-button" disabled>
												Process File
											</button>
										</div>
									</div>
										
								 </div>
							</div>

							<a name="preview"></a>
							<div id="preview" class="col-lg-12 form-section marbot-20"></div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>