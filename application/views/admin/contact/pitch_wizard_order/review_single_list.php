<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<style>
.grid td {
	padding: 5px 20px;
}
.fixed-height-table{
	max-height:400px !important; 
	overflow:auto !important;
}
</style>
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Review List</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?= $this->load->view('admin/contact/pitch_wizard_order/sub_menu.php') ?>
	</div>
</div>


<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			<form method="post" action="" class="tab-content required-form pr-form" id="form1">
				<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
				<input type="hidden" name="pitch_list_id" value="<?= $vd->pitch_list_id ?>" />
				<div class="row-fluid">
					<div class="span4">	    
						<?= count($vd->results) ?> Contacts
					</div>
					<div class="span8">
						<div class="pull-right">
							<a class="pw-order-detail" href="#"  
								data-modal="<?= $vd->pw_detail_modal_id ?>" 
								data-id="<?= $vd->m_pitch_list->pitch_order_id ?>">View Details</a>
						</div>
					</div>
				</div>

				<div class="marbot-30 fixed-height-table">
					<table class="grid" id="selectable-results">
						<thead>
							<tr>
								<th class="condensed">
									<label class="checkbox-container inline">
										<input type="checkbox" id="all-checkbox" />
										<span class="checkbox"></span>
									</label>
								</th>
								<th>Outlet Name</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Work Title</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($vd->results as $result): ?>
							<tr>
								<td class="condensed">
									<label class="checkbox-container inline">
										<input type="checkbox" class="selectable" 
											name="selected[<?= $result->id ?>]" value="<?= $result->id ?>"
										<?= value_if_test(@$vd->selected[$result->id], 'checked') ?> />
										<span class="checkbox"></span>
									</label>
								</td>
								<td class="left">
									<?php if ($result->company_name): ?>
										<?= $vd->esc($result->company_name) ?>
									<?php else: ?>
										<span>-</span>
									<?php endif ?>
								</td>

								<td>
									<?= $vd->esc(@$result->first_name) ?>
								</td>

								<td>
									<?= $vd->esc(@$result->last_name) ?>
								</td>

								<td>
									<?php if ($result->title): ?>
										<?= $vd->esc($result->title) ?>
									<?php else: ?>
										<span>-</span>
									<?php endif ?>
								</td>

								<td>
									<?= $vd->esc(@$result->email) ?>
								</td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
				
				<?php if (count($vd->rejection_log) > 1): ?>
					<div class="row-fluid marbot-20">				
						<button type="submit" class="bt bt-silver
							<?= value_if_test(($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER),
									'hidden')?>" value="1" 
							name="bt_delete">Delete Selected</button>
					</div>

					<div class="row-fluid marbot-20">
						<h3>Rejection Log</h3>
						<table class="grid" id="selectable-results">
							<tbody>
								<?php foreach($vd->rejection_log as $rejection): ?>
								<tr>
									<td width="15%" class="left"> 
										<?php $process = Date::out($rejection->process_date); ?>
										<?= $process->format('M j, Y') ?>
									</td>

									<td class="left">
										<?php if ($rejection->process == Model_Pitch_List_Process::PROCESS_SENT_TO_ADMIN): ?>
											<strong>List Builder uploaded list.</strong>
										<?php else : ?>
											<strong>Reseller to List Builder: </strong>
											<span class="status-false">
												<?= nl2br($this->vd->esc($rejection->comments)) ?>
											</span>
										<?php endif ?>
									</td>
								</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>

					<div class="row-fluid">
						<div class="span4">
							<button type="submit" class="bt bt-silver
								<?= value_if_test(($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER),
										'hidden')?>" value="1" 
								name="bt_upload_to_customer">Upload to Customer</button>
						</div>
						<div class="span8">
							<div class="pull-right">
								<button class="bt bt-orange
									<?= value_if_test($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER,
										'hidden')?>
									 " value="1" id="bt_send_to_list_builder"  
									name="bt_write_to_list_builder">Write to List Builder</button>
							</div>
						</div>
					</div>
				 <?php else : ?>
					<div class="row-fluid marbot-20">				
						<div class="span4">
							<button type="submit" class="bt bt-silver
								<?= value_if_test(($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER),
										'hidden')?>" value="1" 
								name="bt_delete">Delete Selected</button>
						</div>
						<div class="span8">
							<div class="pull-right">
								<button type="submit" class="bt bt-silver
								<?= value_if_test(($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER),
										'hidden')?>" value="1" 
									name="bt_upload_to_customer">Upload to Customer</button>
								<button class="bt bt-orange
								<?= value_if_test(($vd->m_pitch_list->status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER),
										'hidden')?>" value="1" id="bt_send_to_list_builder" 
									name="bt_send_to_list_builder">Write to List Builder</button>
							</div>
						</div>
					</div>
				<?php endif ?>
			</form>

			<script>
			defer(function(){

				var all_checkbox = $("#all-checkbox");
				var results = $("#selectable-results");
				
				all_checkbox.on("change", function() {
					results.find("input.selectable").prop("checked", 
					all_checkbox.is(":checked"));
				});
				
				$("#bt_send_to_list_builder").on("click", function(ev) {
					ev.preventDefault();
					var content_url = "admin/contact/pitch_wizard_order/load_rejection_log_modal/" + <?= $vd->pitch_list_id ?>;
					var modal = $("#<?= $vd->rejection_modal_id ?>");
					modal.load(content_url, function() {
						modal.modal('show');
					});
				});

			});
			</script>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$loader->add('js/pitch_wizard.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>