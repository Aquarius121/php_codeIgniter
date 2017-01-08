<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Store Item</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid store-item-editor required-form" action="admin/store/item/edit/save" method="post">			
	<div class="span12">
		<div class="content content-uniform">	

			<input type="hidden" name="item_id" value="<?= $vd->item->id ?>">

			<section class="form-section marbot-20">
				<div class="row-fluid">
					<div class="span6">
						<h2>Basic Information</h2>
						<div class="row-fluid">
							<div class="span12 relative">
								<input type="text" required name="name"
									class="span12 in-text has-placeholder"
									data-required-name="Item Name"
									value="<?= $vd->esc($vd->item->name) ?>" />
								<strong class="placeholder">Item Name</strong>
								<p class="help-block">This is visible to the customer.</p>
							</div>	
						</div>
						<div class="row-fluid">
							<div class="span12 relative">
								<input type="text" required name="comment"
									class="span12 in-text has-placeholder" 
									data-required-name="Item Comment"
									value="<?= $vd->esc($vd->item->comment) ?>" />
								<strong class="placeholder">Item Comment</strong>
								<p class="help-block">This is not visible to the customer. Please provide 
								information that can be used to identify this item in reports. </p>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span8 relative">
								<input type="text" required name="price"
									data-required-name="Price"
									class="span12 in-text has-placeholder"
									value="<?= $vd->esc($vd->item->price) ?>" 
									pattern="^(\d+(\.\d\d+)?)?$" />
								<strong class="placeholder">Price</strong>
							</div>
						</div>
						<div class="row-fluid marbot-20">
							<div class="span10 checkbox-container">
								<label class="checkbox-container louder">
									<input type="checkbox" value="1" id="is-auto-renew-enabled" />
									<span class="checkbox"></span>
									Enable automatic renewals.
								</label>
								<div class="muted smaller">
									An automatic renewal is created 
										and the customer is billed again 
										as needed until cancelled.
								</div>
							</div>
						</div>					
						<div class="row-fluid">
							<div class="span8 checkbox-container">
								<label class="checkbox-container louder">
									<input type="checkbox" value="1" id="advanced-checkbox" />
									<span class="checkbox"></span>
									Enable extended options
								</label>
								<script>

								$(function() {

									var checkbox = $("#advanced-checkbox");
									var advanced = $("#advanced-section");
									checkbox.on("change", function() {
										advanced.toggleClass("dnone", !checkbox.is(":checked"));
									});

								});

								</script>
							</div>
						</div>
					</div>		
				</div>
			</section>

			<section class="form-section marbot-20 dnone" id="advanced-section">
				<h2>Extended Options</h2>
				<p class="help-block status-muted">
					It's recommended that you do not edit this.
				</p>
				<div class="row-fluid">
					<div class="span6 relative">
						<input type="text" name="tracking"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->tracking) ?>" />
						<strong class="placeholder">tracking</strong>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6 relative">
						<input type="text" name="order_event"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->order_event) ?>" />
						<strong class="placeholder">order_event</strong>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6 relative">
						<input type="text" name="activate_event"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->activate_event) ?>" />
						<strong class="placeholder">activate_event</strong>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6 relative">
						<input type="text" name="secret"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->secret) ?>" />
						<strong class="placeholder">secret</strong>
					</div>
				</div>				
				<div class="row-fluid marbot-20">
					<div class="span10 checkbox-container">
						<label class="checkbox-container louder">
							<input type="checkbox" value="1" id="is-renewable" />
							<span class="checkbox"></span>
							Enable automatic activations. 
						</label>								
						<div class="muted smaller">
							The item will activate periodically as required.
							The best example of this is the yearly subscription 
							plans where the plan is activated each month
							but is only billed for every 12 months.
							This is enabled by default for all items that have 
							automatic renewal enabled but it could also be used on it's own. 
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span8 relative">
						<input type="text" value="30" id="period"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->price) ?>" 
							pattern="^\d*$" />
						<strong class="placeholder">Period</strong>
						<div class="muted smaller help-block">
							This controls how often the item reactivates. 
							The default value is 30 days (1 month).
						</div>
					</div>					
				</div>
				<div class="row-fluid">
					<div class="span8 relative">
						<input type="text" value="1"
							id="period-repeat-count"
							class="span12 in-text has-placeholder"
							value="<?= $vd->esc($vd->item->price) ?>" 
							pattern="^\d*$" value="1" />
						<strong class="placeholder">Period Repeat Count</strong>
						<div class="muted smaller help-block">
							This controls how often many times the item activates before
							it needs to be billed for again. If the <strong>Period</strong>
							was set to 30 days and this set to 1 the item would activate
							monthly and be billed monthly. However, if this is set to 12
							the item would activate monthly but be billed yearly. 
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span8 relative">
						<textarea name="raw_data" id="raw-data"
							class="span12 in-text has-placeholder edit-raw-data"><?= 
							$vd->esc($vd->item->raw_data) ?></textarea>
						<strong class="placeholder">raw_data</strong>
					</div>
				</div>
			</section>

			<div class="row-fluid">
				<div class="span2">
					<button type="submit" class="span12 bt-orange pull-right"
						name="save" value="1">Save</button>
				</div>
			</div>

		</div>
	</div>		
</form>

<script>

$(function() {

	var auto_renew = $("#is-auto-renew-enabled");
	var renewable = $("#is-renewable");
	var raw_data = $("#raw-data");
	var period_repeat_count = $("#period-repeat-count");
	var period = $("#period");

	auto_renew.on("change", function() {
		renewable.prop("checked", 
			auto_renew.is(":checked"));
		update_raw_data();
	});

	renewable.on("change", function() {
		if (!renewable.is(":checked") && 
			auto_renew.is(":checked"))
			auto_renew.prop("checked", false);
		update_raw_data();
	});

	period.on("change", function() {
		var value = parseInt(period_repeat_count.val());
		if (isNaN(value)) period_repeat_count.val(30);
		update_raw_data();
	});

	period_repeat_count.on("change", function() {
		var value = parseInt(period_repeat_count.val());
		if (isNaN(value)) period_repeat_count.val(1);
		update_raw_data();
	});

	var update_raw_data = function() {
		var value = raw_data.val();
		var object = new Object();
		if (value.length) object = JSON.parse(value);
		object["is_auto_renew_enabled"] = + auto_renew.is(":checked");
		object["is_renewable"] = + renewable.is(":checked");
		object["period_repeat_count"] = parseInt(period_repeat_count.val());
		object["period"] = parseInt(period.val());
		raw_data.val(JSON.stringify(object));
	};

	(function() {
		var value = raw_data.val();
		if (!value.length) return;
		var object = JSON.parse(value);
		auto_renew.prop("checked", !!object.is_auto_renew_enabled);
		auto_renew.prop("checked", !!object.is_renewable);
		if (parseInt(object["period_repeat_count"]))
			period_repeat_count.val(object["period_repeat_count"]);
		if (parseInt(object["period"]))
			period.val(object["period"]);
	})();

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