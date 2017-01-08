<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<!--<div class="panel panel-default">
				<div class="panel-body">-->
		<div class="content">
			<div class="tab-content">
				<div class="marbot-20"></div>
				<form role="form" action="manage/account/billing/remove" method="post" class="required-form" autocomplete="off">
					<div class="row form-group">
						<div class="col-lg-12">
							<div class="checkbox-container-box marbot">
								<label class="checkbox-container louder">
									<input type="checkbox" name="confirm" value="1" id="confirm-checkbox" />
									<span class="checkbox"></span>
									I confirm that I want to remove all billing information.
								</label>
								<p class="text-muted">
									If you remove your billing information any existing purchases, 
									credits and packages will continue to be available but they 
									will not renew automatically. You will need to update your
									billing information before they can be renewed. 
								</p>
							</div>
						</div>
					</div>
					<button type="submit" class="btn" disabled id="submit-button">
						Remove Information
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	
$(function() {
	
	var confirm = $("#confirm-checkbox");
	var submit = $("#submit-button");
	
	confirm.on("change", function() {
		submit.prop("disabled", !confirm.is(":checked"));
	});
		
});

</script>