<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Custom Plan</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid store-item-editor required-form" action="admin/store/plan/edit/save" method="post">			
	<div class="span12">
		<div class="content content-uniform">	

			<input type="hidden" name="plan_id" value="<?= @$vd->plan->id ?>">

			<section class="form-section marbot-20">
				<div class="row-fluid">
					<div class="span6">
						<h2>Basic Information</h2>
						<div class="row-fluid">
							<div class="span12 relative">
								<input type="text" required id="plan-name" name="plan_name"
									class="span12 required in-text has-placeholder"
									data-required-name="Item Name"
									value="<?= $vd->esc(@$vd->plan->name) ?>" />
								<strong class="placeholder">Plan Name</strong>
								<p class="help-block">Use the customer name 
									when creating a plan for a single customer.</p>
								<script>

								$(function() {

									var from_box = $("#plan-name");
									var to_box = $("#store-item-name");
									var transfer_enabled = false;

									var test_transfer_enabled = function() {
										var from_val = from_box.val();
										var to_val = to_box.val();
										var extracted = to_val.replace(/^Custom Plan \((.*)\)$/, "$1");
										transfer_enabled = extracted == from_val;
									};

									to_box.on("change", test_transfer_enabled);
									test_transfer_enabled();

									var transfer_to = function() {
										if (!transfer_enabled) return;
										var value = "Custom Plan ({{0}})";
										value = value.format(from_box.val());
										to_box.val(value);
									};

									from_box.on("keyup", transfer_to);
									from_box.on("change", transfer_to);

								});

								</script>
							</div>	
						</div>
						<div class="row-fluid">
							<div class="span12 relative">
								<input type="text" required id="store-item-name" name="store_item_name"
									data-required-name="Store Item Name"
									class="span12 required in-text has-placeholder" 
									value="<?= $vd->esc(@$vd->connected_item->name) ?>" />
								<strong class="placeholder">Store Item Name</strong>
								<p class="help-block">This is visible to the customer.</p>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12 relative">
								<input type="text" required id="store-item-comment" name="store_item_comment"
									data-required-name="Item Comments"
									class="span12 required in-text has-placeholder" 
									value="<?= value_if_test(@$vd->connected_item->comment, 
										$vd->esc(@$vd->connected_item->comment),
										'Custom Plan') ?>" />
								<strong class="placeholder">Item Comments</strong>
								<p class="help-block">This is not visible to the customer.</p>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6 relative">
								<input type="text" required name="price"
									data-required-name="Price"
									class="span12 required in-text has-placeholder"
									value="<?= $vd->esc(@$vd->connected_item->price) ?>" 
									pattern="^(\d+(\.\d\d+)?)?$" />
								<strong class="placeholder">Price</strong>
							</div>
							<div class="span6 relative">
								<select name="period_repeat_count" 
									class="selectpicker show-menu-arrow span12 has-placeholder">

									<option <?= value_if_test(@$vd->connected_item
										->data->period_repeat_count == 1, 'selected') ?>
										value="1">
										1 Month
									</option>
									<option <?= value_if_test(@$vd->connected_item
										->data->period_repeat_count == 3, 'selected') ?>
										value="3">
										3 Months
									</option>
									<option <?= value_if_test(@$vd->connected_item
										->data->period_repeat_count == 6, 'selected') ?>
										value="6">
										6 Months
									</option>
									<option <?= value_if_test(@$vd->connected_item
										->data->period_repeat_count == 12, 'selected') ?>
										value="12">
										12 Months
									</option>

								</select>
								<strong class="placeholder">Billing Cycle</strong>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6 relative">
								<select name="access_level" 
									class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder">
									<option value="<?= Model_Plan::PACKAGE_BASIC ?>"
										<?= value_if_test(@$vd->plan->package == 
											Model_Plan::PACKAGE_BASIC, 'selected') ?>>
										Basic
									</option>
									<option value="<?= Model_Plan::PACKAGE_SILVER ?>"
										<?= value_if_test(@$vd->plan->package == 
											Model_Plan::PACKAGE_SILVER, 'selected') ?>>
										Silver
									</option>
									<option value="<?= Model_Plan::PACKAGE_GOLD ?>"
										<?= value_if_test(@$vd->plan->package == 
											Model_Plan::PACKAGE_GOLD, 'selected') ?>>
										Gold
									</option>
									<option value="<?= Model_Plan::PACKAGE_PLATINUM ?>"
										<?= value_if_test(@$vd->plan->package == 
											Model_Plan::PACKAGE_PLATINUM, 'selected') ?>>
										Platinum
									</option>
								</select>
								<strong class="placeholder">Access Level</strong>
							</div>	
						</div>
					</div>					
				</div>
			</section>

			<section class="form-section marbot-20">
				<h2>Credit Information</h2>
				<?php foreach ($vd->plan_credits as $plan_credit): ?>
				<div class="row-fluid plan-credit-row">
					<div class="row-fluid span11">
						<div class="span3 relative">
							<input type="text" class="in-text span12 marbot-15 has-placeholder valid" 
								value="<?= $vd->esc(Credit::full_name($plan_credit->type)) ?>" readonly disabled />
							<strong class="placeholder">Credit</strong>
						</div>
						<div class="span2 relative">
							<input type="text" class="in-text span12 marbot-15 has-placeholder" 
								value="<?= $vd->esc(@$plan_credit->available) ?>"
								name="credit_quantity[<?= $plan_credit->type ?>]" 
								placeholder="Quantity" pattern="^[0-9]*$" />
							<strong class="placeholder">Quantity</strong>
						</div>							
						<div class="span3 relative">
							<select name="credit_period[<?= $plan_credit->type ?>]" 
								class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder"
								<?= value_if_test($plan_credit->type != Credit::TYPE_PREMIUM_PR
									&& $plan_credit->type !== Credit::TYPE_BASIC_PR, 'disabled') ?>>
								<option value="" class="status-false"
									<?= value_if_test(!$plan_credit->period, 'selected') ?>>
									Default (Month)
								</option>
								<option value="<?= Model_Plan_Credit::PERIOD_DAILY ?>"
									<?= value_if_test($plan_credit->period == 
										Model_Plan_Credit::PERIOD_DAILY, 'selected') ?>>
									Daily
								</option>
								<option value="<?= Model_Plan_Credit::PERIOD_WEEKLY ?>"
									<?= value_if_test($plan_credit->period == 
										Model_Plan_Credit::PERIOD_WEEKLY, 'selected') ?>>
									Weekly
								</option>
							</select>
							<strong class="placeholder">Per Period Limit</strong>
						</div>
						<div class="span2 relative">
							<select name="credit_rollover[<?= $plan_credit->type ?>]" 
								<?= value_if_test(!Credit::has_rollover_support($plan_credit->type), 'disabled') ?>
								class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder">
								<option <?= value_if_test(!$plan_credit->is_rollover_to_held_enabled, 'selected') ?>
									value="0">
									No
								</option>
								<option <?= value_if_test(Credit::is_common($plan_credit->type) ||
									$plan_credit->is_rollover_to_held_enabled, 'selected') ?>
									value="1">
									Yes
								</option>
							</select>
							<strong class="placeholder">
								Has <?= $ci->conf('held_credit_period') ?> Days Life <sup>*</sup>
							</strong>
						</div>
						<div class="span2 relative">
							<input type="text" class="in-text span12 marbot-15 has-placeholder" 
								name="credit_extra_price[<?= $plan_credit->type ?>]" placeholder="Price" 
								value="<?= $vd->esc(@$plan_credit->extra_item->price) ?>" pattern="^(\d+(\.\d\d+)?)?$" />
							<strong class="placeholder">Extra Credit Price <sup>&dagger;</sup></strong>
						</div>
					</div>
				</div>
				<?php endforeach ?>
				<p class="help-block clear pad-10v">
					<sup>*</sup> This only applies to credits that use the default period. 
					Remaining credits will be extended to <?= $ci->conf('held_credit_period') ?> days. 
					<br />
					<sup>&dagger;</sup> This will override the default cost 
					for additional credits purchased.
				</p>
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

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>