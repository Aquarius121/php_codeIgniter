<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Sales Agent Details</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post">
	<div class="span12">		
		<div class="content content-no-tabs">
			
			<div class="span8 information-panel">

				<section class="form-section sales_agent-details">
					<div class="row-fluid">
						<div class="span6 relative">							
							<input type="text" required name="first_name"
								class="span12 in-text has-placeholder"
								value="<?= $vd->sales_agent->first_name ?>"
								placeholder="First Name" />
							<strong class="placeholder">First Name</strong>
						</div>
						<div class="span6 relative">							
							<input type="text" required name="last_name" 
								class="span12 in-text has-placeholder"
								value="<?= $vd->sales_agent->last_name ?>"
								placeholder="Last Name" />
							<strong class="placeholder">Last Name</strong>
						</div>
					</div>
					<div class="relative">
						<input type="email" name="email" required
							class="span8 in-text has-placeholder" 
							value="<?= $vd->sales_agent->email ?>"
							placeholder="Email Address" />
						<strong class="placeholder">Email Address</strong>
					</div>

					<div class="relative marbot-30">
						<label class="checkbox-container louder">
							<input name="is_active" value="1" type="checkbox" 
								<?= value_if_test(!$vd->sales_agent->id || $vd->sales_agent->is_active, 'checked') ?>>
							<span class="checkbox"></span>
							Active
						</label>
					</div>

					<div>
						<button type="submit" name="save" value="1" 
							class="span3 bt-orange">Save</button>
					</div>
				</section>
			</div>
		</div>
	</div>
</form>

<script>

$(function() {

	var held_delete = $(".held-delete");
	held_delete.on("click", function() {

		var _this = $(this);
		var table_row = _this.parents("tr");
		var held_class = table_row.attr("data-held-class");
		var held_data = table_row.attr("data-held-data");

		var confirm_message = 'This action will delete the credit(s).';
		var success_message = 'The credit(s) were removed.';
		var failed_message  = 'Error removing credit(s).';
		bootbox.confirm(confirm_message, function(confirm) {
			if (!confirm) return;
			var url = "admin/sales_agents/view/remove_held_credits";
			$.post(url, { held_class: held_class, held_data: held_data }, function(res) {
				if (res.success) {
					bootbox.alert(success_message);
					table_row.remove();					
				} else {
					bootbox.alert(failed_message);
				}
			});
		});

		return false;

	});

});

</script>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>