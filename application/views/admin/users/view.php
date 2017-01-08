<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>User Details</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post">
	<div class="span12">		
		<div class="content content-no-tabs">
			
			<div class="span8 information-panel">
				
				<section class="form-section user-details">
					<h2 class="marbot-5">Basic Information</h2>
					<div class="row-fluid">
						<div class="span6 relative">							
							<input type="text" required name="first_name"
								class="span12 in-text has-placeholder"
								value="<?= $vd->user->first_name ?>"
								placeholder="First Name" />
							<strong class="placeholder">First Name</strong>
						</div>
						<div class="span6 relative">							
							<input type="text" required name="last_name" 
								class="span12 in-text has-placeholder"
								value="<?= $vd->user->last_name ?>"
								placeholder="Last Name" />
							<strong class="placeholder">Last Name</strong>
						</div>
					</div>
					<div class="relative">
						<input type="email" name="email" required
							class="span12 in-text has-placeholder" 
							value="<?= $vd->user->email ?>"
							placeholder="Email Address" />
						<strong class="placeholder">Email Address</strong>
					</div>
					<div class="relative">
						<textarea name="notes"
							class="span12 in-text has-placeholder user-notes" 
							placeholder="Additional Notes"><?= 
								$vd->user->notes ?></textarea>
						<strong class="placeholder">Additional Notes</strong>
					</div>
				</section>	
				
				<?php if (!$vd->user->is_virtual()): ?>
				<section class="form-section give-plan marbot-10">
					<h2>Give Plan</h2>
					<div class="row-fluid give-plan-row">
						<div class="span6 placeholder-container">
							<select name="give_plan_item_id" id="give-plan-item-id"
								class="selectpicker show-menu-arrow span12 marbot-15 has-placeholder"
								data-live-search="true">
								<option value="" class="status-false" selected>None</option>
								<?php foreach ($vd->plan_items as $item_id => $plan_item): ?>
								<option value="<?= $plan_item->id ?>"><?= $vd->esc($plan_item->name) ?></option>
								<?php endforeach ?>
							</select>
							<strong class="placeholder">Store Item</strong>
						</div>
						<div class="span3 placeholder-container">
							<input type="text" class="in-text span12 marbot-15 has-placeholder" 
								name="give_plan_period" id="give-plan-period" placeholder="Period" />
							<strong class="placeholder">Plan Period</strong>
						</div>
						<div class="span3 placeholder-container">
							<input type="text" class="in-text span12 marbot-15 has-placeholder" 
								name="give_plan_period_repeat_count" id="give-plan-period-repeat-count"
								placeholder="Repeat" />
							<strong class="placeholder">Plan Repeat</strong>
						</div>
					</div>
					<script>
					
					$(function() {
						
						var items = <?= json_encode($vd->plan_items) ?>;
						var plans = <?= json_encode($vd->plans) ?>;
						
						var period_repeat_count = $("#give-plan-period-repeat-count");
						var period = $("#give-plan-period");				
						var select = $("#give-plan-item-id");
						
						select.on("change", function() {
							
							var id;
							if (!(id = select.val())) {
								period_repeat_count.prop("disabled", false);
								period_repeat_count.val("");
								period.val("");
								return;
							}
							
							var item = items[id];
							var plan = plans[item.plan_id];
							period.val(item.period);
							
							var period_repeat_count_disabled = false;
							period_repeat_count_disabled = period_repeat_count_disabled || !!item.period_repeat_count;
							period_repeat_count_disabled = period_repeat_count_disabled || !item.is_renewable;							
							period_repeat_count.prop("disabled", period_repeat_count_disabled);
							period_repeat_count.val(item.period_repeat_count ? 
							                        item.period_repeat_count : 1);
							
						});
						
					});
					
					</script>
					<p class="help-block">
						Note: this will cancel the current plan (without notification).
					</p>
				</section>
				<?php endif ?>
				
				<section class="form-section give-credits marbot-10">
					<h2>Give Credits</h2>					
					<?php if ($vd->user->is_virtual()): ?>
						<div class="alert alert-alternative">
							These are credits for the Newswire account, not the remote website.
						</div>
					<?php endif ?>
					<div class="row-fluid give-credits-row">
						<div class="span4">
							<select name="ac_class[]" class="selectpicker show-menu-arrow span12 marbot-15">
								<?php foreach (Credit::list_types() as $type): ?>
								<option value="<?= $vd->esc($type) ?>"><?= $vd->esc(Credit::full_name($type)) ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="span3">
							<input type="text" class="in-text span12 marbot-15" 
								name="ac_amount[]" placeholder="Amount" />
						</div>
						<div class="span4">
							<div class="input-append give-credits-add-on in-text-add-on marbot-15">
								<input class="in-text ac-expires" name="ac_expires[]"
									data-date-format="yyyy-mm-dd" type="text" 
									value="<?= Date::days(Model_Setting::value('held_credit_period'))->format('Y-m-d') ?>" />
								<span class="add-on ac-expires-icon"><i class="icon-calendar"></i></span>
							</div>
						</div>
						<div class="span1">
							<button type="button" class="span12 add-more btn">+</button>
						</div>
					</div>	
					<script>
					
					$(function() {
						
						var nowTemp = new Date();
						var tomorrow = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
							nowTemp.getDate(), 0, 0, 0, 0);
						tomorrow.setDate(tomorrow.getDate() + 1);
						
						var add_datetime = function(row) {
							
							var expires_date = row.find(".ac-expires");	
							var expires_icon = row.find(".ac-expires-icon");
													
							expires_date.datepicker({
								onRender: function(date) {
									if (date.valueOf() < tomorrow.valueOf())
										return 'disabled';
								}
							});
							
							expires_icon.on("click", function() {
								expires_date.datepicker("show");
							});
							
						};
						
						$(document).on("click", ".add-more", function() {
							
							var row = $(this).parents(".give-credits-row");
							var new_row = $.create(row[0].tagName);
							new_row.attr("class", row.attr("class"));
							new_row.html(row.html());
							new_row.find(".bootstrap-select").remove();
							new_row.find("select.selectpicker").on_load_select();
							add_datetime(new_row);
							row.after(new_row);
							
						});
						
						add_datetime($(".give-credits-row"));
						$(".add-more").click();						
						
					});
					
					</script>
				</section>

				<?php if (!$vd->user->is_virtual()): ?>
				<?php if (isset($vd->active_plan)): ?>	
				<section class="form-section user-credits">
					<h2>Active Plan</h2>		
					<div class="muted smaller marbot-15">
						<span class="status-info-muted">Expires</span> is the date that the plan allocation expires. 
						The plan could be repeated or renewed such that the expires date would advance. 
						<span class="status-info-muted">Terminates</span> is the date that the plan will stop
						repeating unless renewed. 
					</div>
					<table class="credit-data grid marbot-20">
						<thead>
							<tr>
								<th class="left">Name</th>
								<th>Access Level</th>
								<th>Expires</th>
								<th>Terminates</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="left"><?= $vd->esc($vd->active_plan->name) ?></td>
								<td><?= Package::name($vd->active_plan->package) ?></td>
								<td>
									<?php $dt_expires = Date::out($vd->active_user_plan->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td>
									<?php if ($vd->active_plan_ci): ?>									
									<?php $dt_termination = Date::out($vd->active_plan_ci->date_termination); ?>
									<?php if ($dt_termination > Date::months(9)): ?>
										<?= $dt_termination->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_termination->format('M jS') ?>
									<?php endif ?>
									<?php else: ?>
									<span>-</span>
									<?php endif ?>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="row-fluid">
						<div class="span12">
							<div class="checkbox-container-box marbot-20 compact">
								<label class="checkbox-container louder">
									<input name="deactivate_plan" value="1" type="checkbox">
									<span class="checkbox"></span>
									Cancel and Deactivate Plan
								</label>
								<p class="muted">
									This will cancel the renewal and deactivate credits.
								</p>
							</div>
						</div>
					</div>
				</section>
				<?php endif ?>
				<?php endif ?>
				
				<?php if (isset($vd->credit_data)): ?>				
				<section class="form-section user-credits">
					<h2>Active Credits</h2>					
					<?php if ($vd->user->is_virtual()): ?>
						<div class="alert alert-alternative">
							These are credits for the Newswire account, not the remote website.
						</div>
					<?php endif ?>
					<table class="credit-data grid marbot-20">
						<thead>
							<tr>
								<th class="left">Class</th>
								<th>Expires <sup>*</sup></th>
								<th>Used</th>
								<th>Avail</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php $stat = $vd->credit_data->pr_premium; ?>
								<td class="left"><span class="status-package">Plan</span> Premium PR</td>
								<td>
									<?php $dt_expires = Date::out($vd->user->package_expires()); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $stat->rollover_used ?></td>
								<td><?= $stat->rollover_available ?></td>
								<td><?= $stat->rollover_total ?></td>
							</tr>
							<?php foreach ($vd->credit_data->pr_premium->held as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> Premium PR
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>										
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $held->used() ?></td>
								<td><?= $held->available() ?></td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>
							<tr>
								<?php $stat = $vd->credit_data->pr_basic; ?>
								<td class="left"><span class="status-package">Plan</span> Basic PR</td>
								<td>
									<span class="muted">Periodic</span>
								</td>
								<td><?= $stat->rollover_used ?></td>
								<td><?= $stat->rollover_available ?></td>
								<td><?= $stat->rollover_total ?></td>
							</tr>
							<?php foreach ($vd->credit_data->pr_basic->held as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> Basic PR
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>										
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $held->used() ?></td>
								<td><?= $held->available() ?></td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>							
							<tr>
								<?php $stat = $vd->credit_data->writing; ?>
								<td class="left"><span class="status-package">Plan</span> Writing</td>
								<td>
									<?php $dt_expires = Date::out($vd->user->package_expires()); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $stat->rollover_used ?></td>
								<td><?= $stat->rollover_available ?></td>
								<td><?= $stat->rollover_total ?></td>
							</tr>
							<?php foreach ($vd->credit_data->writing->held as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> Writing
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>										
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $held->used() ?></td>
								<td><?= $held->available() ?></td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>
							<tr>
								<?php $stat = $vd->credit_data->email; ?>
								<td class="left"><span class="status-package">Plan</span> Email</td>
								<td>
									<?php $dt_expires = Date::out($vd->user->package_expires()); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $stat->rollover_used ?></td>
								<td><?= $stat->rollover_available ?></td>
								<td><?= $stat->rollover_total ?></td>
							</tr>
							<?php foreach ($vd->credit_data->email->held as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> Email
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(11)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $held->used() ?></td>
								<td><?= $held->available() ?></td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>
							<tr>
								<?php $stat = $vd->credit_data->newsroom; ?>
								<td class="left"><span class="status-package">Plan</span> Newsroom</td>
								<td>
									<?php $dt_expires = Date::out($vd->user->package_expires()); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td>-</td>
								<td>-</td>
								<td><?= $stat->rollover ?></td>
							</tr>
							<?php foreach ($vd->credit_data->newsroom->held as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> Newsroom
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>										
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td>-</td>
								<td>-</td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>
							<?php foreach ($vd->credit_data->common as $type => $held_collection): ?>
							<?php foreach ($held_collection as $k => $held): ?>
							<tr data-held-class="<?= get_class($held) ?>"
								data-held-data="<?= $vd->esc(json_encode($held)) ?>">
								<td class="left">
									<span class="status-held">Held</span> 
									<?= $vd->esc(Credit::full_name($type)) ?>
									[<a href="#" class="held-delete">X</a>]
								</td>
								<td>
									<?php $dt_expires = Date::out($held->date_expires); ?>
									<?php if ($dt_expires > Date::months(9)): ?>
										<?= $dt_expires->format('Y-m-d') ?>
									<?php else: ?>										
										<?= $dt_expires->format('M jS') ?>
									<?php endif ?>
								</td>
								<td><?= $held->used() ?></td>
								<td><?= $held->available() ?></td>
								<td><?= $held->total() ?></td>
							</tr>
							<?php endforeach ?>
							<?php endforeach ?>
						</tbody>
					</table>
					<div class="help-block ta-center pad-10v">
						* unless the credit is converted to a held credit
						such that the credit life is extended 
						to <?= $ci->conf('held_credit_period') ?> days.
					</div>
				</section>
				<?php endif ?>

				<hr />

				<section class="form-section user-details">	
					<h2 class="marbot-5">Data Editor</h2>
					<div>
						<textarea name="raw_data" id="data-editor"
							class="span12 in-text user-notes raw-data"><?= 
								$vd->user->raw_data ?></textarea>
						<p class="help-block status-false">
							Be careful with this. 
							Must be valid JSON.</p>						
					</div>
					<div id="data-editor-switches">
						<div class="marbot-5">
							<label class="checkbox-container inline small-checkbox">
								<input type="checkbox" name="auto_hold_under_review" />
								<span class="checkbox"></span>	
								<span>Automatic hold on submitted content.</span>						
							</label>
						</div>
						<div class="marbot-5">
							<label class="checkbox-container inline small-checkbox">
								<input type="checkbox" name="has_media_database_plus" />
								<span class="checkbox"></span>	
								<span>Full media database access.</span>					
							</label>
						</div>
						<div class="marbot-5">
							<label class="checkbox-container inline small-checkbox">
								<input type="checkbox" name="disable_pr_body_validation" />
								<span class="checkbox"></span>	
								<span>Disable PR Body Validation.</span>					
							</label>
						</div>
						<div class="marbot-5">
							<label class="checkbox-container inline small-checkbox">
								<input type="checkbox" name="has_clients" />
								<span class="checkbox"></span>	
								<span>Customer has clients.</span>
							</label>
						</div>
					</div>
					<script>

					$(function() {

						var data_editor = $("#data-editor");
						var data_editor_switches = $("#data-editor-switches input[type=checkbox]");

						var data_read = function() {
							try { var data = JSON.parse(data_editor.val()); }
							catch (er) { return new Object; }
							if (typeof data !== "object")
								return new Object;
							return data;
						};

						var data_write = function(data) {
							data_editor.val(JSON.stringify(data));
						};
						
						data_editor_switches.on("change", function() {
							var checkbox = $(this);
							var data_name = checkbox.attr("name");
							var data_value = checkbox.is(":checked");
							var data = data_read();
							data[data_name] = data_value;
							data_write(data);
						});

						(function() {
							var data = data_read();
							data_editor_switches.each(function() {
								var checkbox = $(this);
								var data_name = checkbox.attr("name");
								checkbox.prop("checked", !!data[data_name]);
							});
						})();

					});

					</script>
				</section>

			</div>
			
			<aside class="span4 aside aside-fluid">
				<div id="locked_aside">
					
					<div class="aside-properties padding-top marbot-20">
						<section class="ap-block">
							<select class="show-menu-arrow span12 selectpicker has-true-false-button" name="is_enabled">
								<option <?= value_if_test(!$vd->user->id || $vd->user->is_enabled, 'selected')
									?> value="1">Account Enabled</option>
								<option <?= value_if_test($vd->user->id && !$vd->user->is_enabled, 'selected')
									?> value="0">Account Disabled</option>
							</select> 
						</section>
						<?php if (!$vd->user->is_virtual()): ?>
						<section class="ap-block">
							<select class="show-menu-arrow span12 selectpicker has-true-false-button" name="is_admin">
								<option <?= value_if_test(!@$vd->user->is_admin, 'selected')
									?> value="0">Admin Disabled</option>
								<option <?= value_if_test(@$vd->user->is_admin, 'selected')
									?> value="1">Admin Enabled</option>								
							</select>
						</section>
						<section class="ap-block">
							<select class="show-menu-arrow span12 selectpicker has-true-false-button" name="is_reseller">
								<option <?= value_if_test(!@$vd->user->is_reseller, 'selected')
									?> value="0">Reseller Disabled</option>
								<option <?= value_if_test(@$vd->user->is_reseller, 'selected')
									?> value="1">Reseller Enabled</option>
							</select>
						</section>
						<script>
						
						$(function() {
							
							window.on_load_select(function() {
							
								var selects = $(".has-true-false-button");
								selects.on("change", function() {
									var _this = $(this);
									var button = _this
										.next('.has-true-false-button')
										.children('button');
									if (parseInt(_this.val())) 
									     button.addClass('btn-success');
									else button.removeClass('btn-success');
								});
							
								selects.trigger("change");
							
							});
							
						});
						
						</script>
						<?php if ($vd->user->is_reseller && $vd->reseller_details): ?>
						<section class="ap-block marbot-5">
							<select class="show-menu-arrow span12 selectpicker" name="reseller_priv">
								<option <?= value_if_test(Model_Reseller_Details::PRIV_ADMIN_EDITOR 
									== $vd->reseller_details->editing_privilege, 'selected')
									?> value="<?= Model_Reseller_Details::PRIV_ADMIN_EDITOR ?>">Admin Editor</option>
								<option <?= value_if_test(Model_Reseller_Details::PRIV_RESELLER_EDITOR 
									== $vd->reseller_details->editing_privilege, 'selected')
									?> value="<?= Model_Reseller_Details::PRIV_RESELLER_EDITOR ?>">Reseller Editor</option>
								<option <?= value_if_test(Model_Reseller_Details::PRIV_DIRECTLY_QUEUE_DRAFT 
									== $vd->reseller_details->editing_privilege, 'selected')
									?> value="<?= Model_Reseller_Details::PRIV_DIRECTLY_QUEUE_DRAFT ?>">Directly Queue Draft</option>
							</select>
						</section>
						<?php endif ?>
						<?php endif ?>
						<section class="ap-block row-fluid marbot-10">
							<div class="row-fluid marbot-5">
								<div class="span12">
									<button class="span12 ta-center btn" id="reset-password" type="button"
										<?= value_if_test(!$vd->user->id, 'disabled') ?>>
										Reset Password</button>
									<script>
									
									$(function() {
										
										var message = 'This action will reset the new password.';
										$("#reset-password").on("click", function() {
											if ($(this).is(":disabled")) return;
											bootbox.confirm(message, function(confirm) {
												if (!confirm) return;
												var url = "admin/users/view/reset/<?= $vd->user->id ?>";
												$.post(url, { confirm: true }, function(res) {
													var e = $.create("input").addClass("password-text");
													e.val(res.password);
													bootbox.alert({ message: e.get(0) });
													e.focus().select();
												});
											});
										});
										
									});
									
									</script>
								</div>
							</div>

							<?php if ($vd->user->is_virtual()): ?>
							<div class="row-fluid marbot">
								<div class="span12">
									<a class="span12 ta-center btn btn-info btn-no-ts"
										<?= value_if_test(!$vd->user->id, 'disabled') ?>
										<?php if ($vd->user->id): ?>
										href="common/vuras/<?= $vd->user->id ?>/default" 
										<?php endif ?>
										target="_blank">Remote Admin Session</a>
								</div>
							</div>
							<?php endif ?>

							<div class="row-fluid">
								<div class="span8">
									<a class="span12 ta-center btn btn-no-ts btn-danger"
										<?= value_if_test(!$vd->user->id, 'disabled') ?>
										<?php if ($vd->user->id): ?>
										href="<?= Admo::url('default', $vd->user->id) ?>" 
										<?php endif ?>
										target="_blank">Admin Session</a>
								</div>
								<div class="span4">
									<button type="submit" name="save" value="1" 
										class="span12 bt-orange pull-right">Save</button>
								</div>
							</div>
							
						</section>
					</div>
					
					<?php if ($vd->user->remote_addr): ?>
					<?php $is_blocked = Model_Blocked::find($vd->user->remote_addr); ?>
					<div class="aside-properties aside-minimal marbot-20">
						<section class="ap-block row-fluid">
							<div class="input-append remote-addr-block <?= 
								value_if_test($is_blocked, 'remote-addr-blocked') ?>">
								<input type="text" id="remote-addr" name="remote_addr" readonly
									class="nomarbot" value="<?= $vd->user->remote_addr ?>" />
								<?php if ($is_blocked): ?>
								<span class="add-on">BLOCKED</span>
								<?php else: ?>
								<a class="btn nomarbot" target="_blank" id="remote-addr-btn"
									href="admin/settings/ip_block/add_ajax?user=<?= $vd->user->id ?>">
									<i class="icon-ban-circle status-false"></i>
								</a>
								<?php endif ?>
							</div>
						</section>
					</div>
					<script>
					
					$(function() {
						
						$("#remote-addr").on("focus", function() {
							$(this).select();
						});
										
						var message = 'This action will block the IP address.';
						var feedback_container = $("#feedback");
						var block_button = $("#remote-addr-btn");

						block_button.on("click", function(ev) {
							ev.preventDefault();
							bootbox.confirm(message, function(confirm) {
								if (!confirm) return;
								block_button.prop("disabled", true);
								$.get(block_button.attr("href"), function(res) {
									block_button.prop("disabled", false);
									if (res.success) {
										block_button.parent().addClass("remote-addr-blocked");
										var text_add_on = $.create("span");
										text_add_on.addClass("add-on");
										text_add_on.text("BLOCKED");
										block_button.before(text_add_on);
										block_button.remove();
									} else if (res.feedback) {
										feedback_container.append(res.feedback);
										$(window).scrollTop(0);
									}
								});
							});
						});
						
					});
					
					</script>
					<?php endif ?>	
					
					<?php if ($vd->user->id): ?>
					<div class="aside-properties padding-top marbot-20">
						<section class="ap-block marbot-5">
							<table class="grid user-details" id="user-details">
								<tbody>
									<tr>
										<th>Content</th>
										<td>
											<?= (int) @$vd->published_count ?>
											<a href="admin/publish/pr/published?filter_user=<?= $vd->user->id ?>" 
												class="add-filter-icon" target="_blank"></a>
										</td>
									</tr>
									<tr>
										<th>Campaigns</th>
										<td>
											<?= (int) @$vd->campaign_count ?>
											<a href="admin/contact/campaign/all?filter_user=<?= $vd->user->id ?>" 
												class="add-filter-icon" target="_blank"></a>
										</td>
									</tr>
									<tr>
										<th>Companies</th>
										<td>
											<?= (int) @$vd->companies_count ?>
											<a href="admin/companies/all?filter_user=<?= $vd->user->id ?>" 
												class="add-filter-icon" target="_blank"></a>
										</td>
									</tr>
									<tr>
										<th>Newsrooms</th>
										<td>
											<?= (int) @$vd->credit_data->newsroom->used ?>
											<a href="admin/companies/newsroom?filter_user=<?= $vd->user->id ?>" 
												class="add-filter-icon" target="_blank"></a>
										</td>
									</tr>
								</tbody>
							</table>
						</section>
					</div>	
					<?php endif ?>

					<?php if (!$vd->user->is_virtual()): ?>
					<?php if ($vd->user->id): ?>
					<div class="aside-properties pad-15v marbot-20">
						<div><a target="_blank" href="admin/store/order?filter_user=<?= $vd->user->id ?>">View Orders</a></div>
						<div><a target="_blank" href="<?= Admo::url('manage/account/renewal', $vd->user->id) ?>">View Renewals</a></div>
						<div><a target="_blank" href="admin/store/transaction?filter_user=<?= $vd->user->id ?>">View Transactions</a></div>
					</div>	
					<?php endif ?>	
					<?php endif ?>

					<?php if ($vd->user->id && ($vd->has_bill_block || $vd->ulbu)): ?>
					<div class="aside-properties pad-15v marbot-20">
						<?php if ($vd->ulbu): ?>
							<?php $dt_updated = Date::utc($vd->ulbu->date_updated) ?>
							<?php if ($dt_updated < Date::hours(-24)): ?>
								<div class="marbot-15"><span class="muted">Billing information was last updated </span>
								<span class="status-info"><?= $dt_updated->format('Y-m-d') ?></span></div>
							<?php else: ?>
								<div class="marbot-15"><span class="muted">Billing information was last updated at</span>
								<span class="status-info"><?= $dt_updated->format('h:i A') ?></span> UTC</div>
							<?php endif ?>
						<?php endif ?>
						<?php if ($vd->has_bill_block): ?>
							<div class="marbot-15 status-false">
								This account has been blocked from ordering due to <?= Model_Bill_Failure::BILL_BLOCK_THRESHOLD ?> 
								failures within the last <?= (int) (Model_Bill_Failure::BILL_BLOCK_PERIOD / 3600) ?> hours.
							</div>
						<?php endif ?>
						<div>
							<label class="checkbox-container louder">
								<input name="unblock_billing_change" value="1" type="checkbox">
								<span class="checkbox"></span>
								Permit Update
							</label>
							<p class="muted nopadbot">
								This will allow the user to update their billing information again.
							</p>
						</div>
					</div>	
					<?php endif ?>	

				</div>
			</aside>

			<script>
			
			$(function() {
				
				var options = { offset: { top: 20 } };
				$.lockfixed("#locked_aside", options);
				
			});
			
			</script>
					
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
			var url = "admin/users/view/remove_held_credits";
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

$render_basic = $ci->is_development();

$loader = new Assets\CSS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/bootstrap-datepicker.css');	
echo $loader->render($render_basic);

$loader = new Assets\JS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/bootstrap-datepicker.js');
$loader->add('lib/bootbox.min.js');
$loader->add('js/required.js');
$ci->add_eob($loader->render($render_basic));
