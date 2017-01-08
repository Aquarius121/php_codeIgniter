<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			<form class="tab-content" method="post" id="import-form" action="admin/contact/pitch_wizard_order/save_list">
				<input type="hidden" name="is_reupload" value="<?= @$vd->is_reupload ?>" />
                <input type="hidden" class="required" id="stored-file-id"
					data-required-name="CSV File" name="stored_file_id" />
				<input type="hidden" id="filename" name="filename" />
                <input type="hidden" name="pitch_order_id" value="<?= $vd->pitch_order_id ?>" />
				<div class="row-fluid" id="csv-please-wait">
					<div class="span12">
						<div class="alert alert-warning">
							Uploading, please wait
						</div>
					</div>
				</div>

				<div id="csv-upload">
					<div class="row-fluid">
						<div class="span12 file-upload-faker">
							<div class="fake row-fluid">
								<div class="span8 text-input">
									<input type="text" placeholder="Select File" class="in-text 
										span12 fake-text" />
								</div>
								<div class="span4">
									<button class="btn span12 fake-button" type="button">Browse</button>
								</div>								
							</div>

							<div class="real row-fluid">
								<input class="in-text span12 real-file required-no-submit" type="file" name="csv" />
							</div>
                            
						</div>
					</div>
				</div>
				<a name="preview"></a>
				<div id="preview" class="form-section marbot-20"></div>
                
				<div>
					<div class="row-fluid">
						<div class="span12">
							<div class="span8 text-input"></div>
								<div class="span4">
									<button class="btn span12 pull-right hidden" 
										type="submit" id="import-button" disabled>Upload</button>
								</div>
							</div>
						</div>
				</div>
            </form>
            
			
            
            
			

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
				import_button.addClass("bt-orange");
				import_button.removeClass("btn");	
				import_button.removeClass("hidden");
				preview.empty();
				preview.html(res.preview);
				preview.show();
			}
			else
			{
				fake_text.addClass("error");
				real_file.attr("disabled", false);
				fake_text.removeClass("loader");
				import_button.attr("disabled", true);
				import_button.removeClass("bt-orange");
				import_button.addClass("btn");
				import_button.addClass("hidden");
			}
			
		};
		
		setTimeout(function() {	
			csv_upload.find(".real-file").ajax_upload({
				callback: on_upload,
				url: "admin/contact/pitch_wizard_order/store_csv"
			})}, 1000);
		
	});
	
});
					

</script>