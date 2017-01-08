<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Coupon</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid" action="<?= $vd->store_base ?>/coupon/edit/save" method="post">

	<?php if ($vd->coupon): ?>
	<input type="hidden" name="coupon_id" 
		id="coupon_id" value="<?= $vd->coupon->id ?>">
	<?php endif ?>
	
	<div class="span12">
		<div class="content content-no-tabs">
			
			<div class="span8 information-panel">
				
				<section class="form-section user-details">
					<h2 class="marbot-5">Basic Information</h2>
					
					<div class="row-fluid dnone" id="code-error">
						<div class="span12">
							<div class="alert alert-error">
								Coupon code already exists: (<span id="code-entered"></span>)
							</div>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" required name="code"
								id="code" class="span12 has-loader in-text"
								value="<?= @$vd->coupon->code ?>"
								placeholder="Code" />
						</div>
						<div class="span6 relative">
							<?= $this->load->view('admin/virtual/store/coupon/partials/expiry-date') ?>
						</div>
					</div>

					<script>

					$(function() {
						
						var code = $("#code");
						var code_error = $("#code-error");
						var code_entered = $("#code-entered");
						var coupon_id = $("#coupon_id");
						
						code.on("change", function() {
							
							code_to_check = code.val();
							code.addClass("loader");
							code.removeClass("error");
							code_error.slideUp();
							
							var data = {};
							data.coupon_id = coupon_id.val();
							data.code = code_to_check;
							
							$.post("<?= $vd->store_base ?>/coupon/code_check", data, function(res) {
								
								code.removeClass("loader");
								if (res.available) return code.val(code_to_check);
								code.addClass("error");
								code_error.slideDown();
								code_entered.html(code_to_check);
								code.val("");
								
							});
							
						});
						
					});
					
					</script>
					
				</section>


				<section class="form-section give-credits marbot-10">
					<h2>Items to Consider</h2>
					<p class="help-block">Don't select any items if you want to consider all items</p>
					<?php if (@$vd->coupon->raw_data->item_restriction): ?>
						<?php foreach ($vd->coupon->raw_data->item_restriction as $key => $item_id): ?>
						<div class="row-fluid items-to-consider-row">
							<div class="span10">
								<select name="item_restriction[]" data-live-search="true"
									class="selectpicker show-menu-arrow span12 marbot-15
										descriptive-bootstrap-select">
									<option value="" class="status-false" selected>None</option>
									<?php foreach ($vd->items as $item): ?>
									<option value="<?= $item->id ?>"
										data-content="<?= $vd->esc($item->name) ?> 
											<span>
												<?php if ($item->comment): ?>	
													<?= $vd->esc($item->comment) ?> - 
												<?php endif ?>
												Price: $<?= $item->price ?>
											</span>"
											<?= value_if_test($item->id == $item_id, 'selected') ?>>
											<?= $vd->esc($item->name) ?></option>
									<?php endforeach ?>
								</select>
							</div>

							<div class="span1">
								<button type="button" class="span12 add-more-items btn">+</button>
							</div>
						</div>
						<?php endforeach ?>
					<?php endif ?>

					<div class="row-fluid items-to-consider-row">
						<div class="span10">
							<select name="item_restriction[]" data-live-search="true"
								class="selectpicker show-menu-arrow span12 
									descriptive-bootstrap-select">
								<option value="" class="status-false" selected>None</option>
								<?php foreach ($vd->items as $item): ?>
								<option value="<?= $item->id ?>"
									data-content="<?= $vd->esc($item->name) ?>
										<span>
											<?php if ($item->comment): ?>	
												<?= $vd->esc($item->comment) ?> - 
											<?php endif ?>
											Price: $<?= $item->price ?>
										</span>"
									><?= $vd->esc($item->name) ?></option>
								<?php endforeach ?>
							</select>
						</div>

						<div class="span1">
							<button type="button" class="span12 add-more-items btn">+</button>
						</div>
					</div>

					<script>
				
					$(function() {						

						var select = $(".descriptive-bootstrap-select");
						select.on_load_select({ showContent: false });
						
						$(window).load(function() {
							select.trigger("change");
						});
						
						select.on("change", function() {
							select.toggleClass("invalid", !select.val());
						});
						
						$(document).on("click", ".add-more-items", function() {

							var row = $(this).parents(".items-to-consider-row");
							var new_row = $.create(row[0].tagName);
							new_row.attr("class", row.attr("class"));
							new_row.html(row.html());
							new_row.find(".bootstrap-select").remove();
							new_row.find("select.selectpicker").on_load_select({
								showContent: false
							});
							
							row.after(new_row);
							
						});	
						
					});
					
					</script>					
				</section>


				<section class="form-section give-credits marbot-10">
					<div class="row-fluid marbot-20">
						<label class="checkbox-container inline">
							<input type="checkbox" value="1" name="is_one_time" class="selectable"
								<?= value_if_test(@$vd->coupon->is_one_time, 'checked=checked') ?> 
								id="is_one_time">
							<span class="checkbox"></span>
							One time code
						</label>
					</div>

					<div class="row-fluid">
						<div class="span12 relative">
							<input type="text" name="minimum_cost"
								class="span12 in-text has-placeholder"
								value="<?= @$vd->coupon->raw_data->minimum_cost ?>"	
								placeholder="Minimum Applicable Cost" />
								<strong class="placeholder">Minimum Applicable Cost</strong>
						</div>
					</div>


					<ul>
						<li class="radio-container-box marbot">
							<label class="radio-container louder">
								<input type="radio" name="is_percentage" value="0" class="is-percentage-radio"  
									<?= value_if_test(@$vd->coupon->raw_data->percentage_discount, 'checked') ?>
									 />
								<span class="radio"></span>
								Percentage Discount								
							</label>
							<p class="muted dnone" id="percentage_discount_p">
								<input type="text" name="percentage_discount"
									class="span12 in-text has-placeholder"	
									value="<?= @$vd->coupon->raw_data->percentage_discount ?>"
									placeholder="Percentage Discount" />
							</p>
						</li>


						<li class="radio-container-box marbot">
							<label class="radio-container louder">
								<input type="radio" name="is_percentage" value="0" class="is-fixed-radio"  
									<?= value_if_test(@$vd->coupon->raw_data->fixed_discount, 'checked') ?>
									 />
								<span class="radio"></span>
								Fixed Discount								
							</label>
							<p class="muted dnone" id="fixed_discount_p">
								<input type="text" name="fixed_discount"
									class="span12 in-text has-placeholder"
									value="<?= @$vd->coupon->raw_data->fixed_discount ?>"
									placeholder="Fixed Discount ($)" />
							</p>
						</li>

						<script>
						
						$(function() {
							
							var is_percentage_radio = $(".is-percentage-radio");
							var is_fixed_radio = $(".is-fixed-radio");
							var percentage_discount_p = $("#percentage_discount_p");
							var fixed_discount_p = $("#fixed_discount_p");
							
							var discount_type_change = function() {
								if (is_percentage_radio.is(":checked"))
								{
									percentage_discount_p.slideDown();
									fixed_discount_p.slideUp();
								}
								else if (is_fixed_radio.is(":checked"))
								{
									fixed_discount_p.slideDown();
									percentage_discount_p.slideUp();
								}
								
							};
							
							is_percentage_radio.on("change", discount_type_change);
							is_fixed_radio.on("change", discount_type_change);
							discount_type_change();
							
						});
						
						</script>

					</ul>

					<!--<div class="row-fluid">
						<div class="span12 relative">
							<input type="text" name="percentage_discount"
								class="span12 in-text has-placeholder"	
								value="<?= @$vd->coupon->raw_data->percentage_discount ?>"
								placeholder="Percentage Discount" />
							<strong class="placeholder">Percentage Discount</strong>
						</div>
					</div>-->

					<!--<div class="row-fluid">
						<div class="span12 relative">
							<input type="text" name="fixed_discount"
								class="span12 in-text has-placeholder"
								value="<?= @$vd->coupon->raw_data->fixed_discount ?>"
								placeholder="Fixed Discount ($)" />
							<strong class="placeholder">Fixed Discount</strong>	
						</div>
					</div>-->

					<h2>Item Specific Price</h2>					

					<?php if (@$vd->coupon->raw_data->item_static_cost): ?>
						<?php foreach ($vd->coupon->raw_data->item_static_cost as $item_id => $price): ?>
							<div class="row-fluid item-specific-price-row">
								<div class="span5">
									<select name="item_list[]" data-live-search="true"
										class="selectpicker show-menu-arrow span12 marbot-15
											descriptive-bootstrap-select">
										<option value="" class="status-false" selected>None</option>
										<?php foreach ($vd->items as $item): ?>
										<option value="<?= $item->id ?>"
											data-content="<?= $vd->esc($item->name) ?> 
											<span>
												<?php if ($item->comment): ?>	
													<?= $vd->esc($item->comment) ?> - 
												<?php endif ?>
												Price: $<?= $item->price ?>
											</span>"											
											<?= value_if_test($item->id == $item_id, 'selected') ?>>
											<?= $vd->esc($item->name) ?></option>
										<?php endforeach ?>								
									</select>
								</div>
								<div class="span5">
									<input type="text" class="in-text span12 marbot-15" value="<?= $price ?>"
										name="item_price[]" placeholder="Price" />
								</div>						
								<div class="span1">
									<button type="button" class="span12 add-more btn">+</button>
								</div>
							</div>
						<?php endforeach ?>
					<?php endif ?>

					<div class="row-fluid item-specific-price-row">
						<div class="span5">
							<select name="item_list[]" class="selectpicker show-menu-arrow span12 marbot-15
								descriptive-bootstrap-select" data-live-search="true">
								<option value="" class="status-false" selected>None</option>
								<?php foreach ($vd->items as $item): ?>
								<option value="<?= $item->id ?>"
									data-content="<?= $vd->esc($item->name) ?> 
										<span>
											<?php if ($item->comment): ?>	
												<?= $vd->esc($item->comment) ?> - 
											<?php endif ?>
											Price: $<?= $item->price ?>
										</span>"
									><?= $vd->esc($item->name) ?></option>
								<?php endforeach ?>								
							</select>
						</div>
						<div class="span5">
							<input type="text" class="in-text span12 marbot-15" 
								name="item_price[]" placeholder="Price" />
						</div>						
						<div class="span1">
							<button type="button" class="span12 add-more btn">+</button>
						</div>
					</div>

					<script>
				
					$(function() {
						
						$(document).on("click", ".add-more", function() {
							
							var row = $(this).parents(".item-specific-price-row");
							var new_row = $.create(row[0].tagName);
							new_row.attr("class", row.attr("class"));
							new_row.html(row.html());
							new_row.find(".bootstrap-select").remove();
							new_row.find("select.selectpicker").on_load_select();
							row.after(new_row);
							
						});			
						
						//$(".add-more").click();						
						
					});
					
					</script>

					<div class="row-fluid">								
						<div class="span4">
							<button type="submit" name="save" value="1" 
								class="span12 bt-orange pull-right">Save</button>
						</div>
					</div>
					
				</section>
			</div>
		</div>
	</div>
</form>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>