<?= $ci->load->view('manage/account/menu') ?>
<div class="container-fluid">
	<div class="col-lg-12 form-col-1">
		<div class="marbot-20"></div>
			<form role="form" action="manage/account/renewal/cancel/<?= $vd->component_item->id ?>"
				method="post" class="required-form" id="cancel-form">
				<div class="alert alert-info marbot">
					Did you have an issue with the product? 
					Please consider <a href="<?= $ci->website_url('helpdesk') ?>">contacting our 
					support team</a> and letting them know. 
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="checkbox-container-box marbot">
							<label class="checkbox-container louder">
								<input type="checkbox" name="confirm" value="1" id="confirm-checkbox"
									data-required-name="Confirmation" class="required" />
								<span class="checkbox"></span>
								I confirm that I want to cancel automatic renewal.
							</label>
							<p class="text-muted">
								Automatic renewal will be cancelled and you will not be charged again.
								<br />You will still have access to the purchase until it expires.
							</p>
						</div>
					</div>
				</div>
				<?php if (Auth::is_admin_online()): ?>
				<div class="row">
					<div class="col-lg-12">
						<div class="checkbox-container-box marbot">
							<label class="checkbox-container louder">
								<input type="checkbox" name="no_record" value="1" id="no-record-checkbox" />
								<span class="checkbox"></span>
								Do not record the cancellation event.
							</label>
							<p class="text-muted">
								Use this to cancel a renewal without recording a reason. <br />This
								should only be used for <strong class="status-false">test renewals</strong>. 
							</p>
						</div>
						<script>
						
						$(function() {
							
							var no_reason = $("#no-record-checkbox");
							no_reason.on("change", function() {
								$("#reason").toggleClass("required",
									!no_reason.is(":checked"));
							});
							
						});
						
						</script>
					</div>
				</div>
				<?php endif ?>
				<div class="row form-group">
					<div class="col-lg-12">
						<textarea class="form-control in-text required col-lg-12" placeholder="Cancellation Reason"
							name="reason" data-required-name="Reason" id="reason" rows="4"></textarea>
					</div>
				</div>
				<button type="submit" class="btn btn-default" disabled id="submit-button">
					Cancel Renewal
				</button>
			</form>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<script>
	
$(function() {
	
	var confirm = $("#confirm-checkbox");
	var submit = $("#submit-button");
	var form = $("#cancel-form");
	
	confirm.on("change", function() {
		submit.prop("disabled", !confirm.is(":checked"));
	});
	
	window.required_js.on_submit = function() {
		submit.prop("disabled", true);
	};
		
});

</script>