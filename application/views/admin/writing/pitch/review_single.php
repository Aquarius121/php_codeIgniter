<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Review Pitch</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?= $this->load->view('admin/writing/pitch/sub_menu.php') ?>
	</div>
</div>

<form method="post" action="" class="tab-content required-form pr-form" id="form1">
	<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
	<input type="hidden" name="pitch_order_id" value="<?= $vd->m_pw_order->id ?>" />
	<div class="row-fluid">
		<div class="span12">
			<div class="span10">
				<div class="pull-right">
					<a href="#" class="pw-order-detail" data-id="<?= $vd->m_pw_order->id ?>"
						data-modal="<?= $vd->pw_detail_modal_id ?>">Order Details</a>
				</div>
			</div>
			<div class="span2">
				<a href="#" class="edit-pitch">Edit Pitch</a>
			</div>
			<div class="span10">
				<div class="content content-no-tabs">
					<section class="form-section user-details marbot-20 clearfix">
						<div class="relative">
							<div id="pitch_subject_text" class="has-placeholder pad-20v">
								<?= value_if_test(@$vd->customer_edited_rejected, 
										$vd->esc($vd->m_campaign->subject), $vd->esc($vd->m_pw_content->subject))?>
							</div>
							<input type="text" name="subject" required
								class="span12 in-text has-placeholder dnone" id="subject" 
								value="<?= value_if_test(@$vd->customer_edited_rejected, 
										$vd->esc($vd->m_campaign->subject), $vd->esc($vd->m_pw_content->subject))?>"
								placeholder="Subject Line"  />
							<strong class="placeholder">Subject Line</strong>
						</div>

						<div class="relative cke-container pad-20v">
							<div id="div_pitch_text">
								<?php if (@$vd->customer_edited_rejected): ?>
									<?= $vd->m_campaign_data->content ?>
								<?php else : ?>
									<?= $vd->m_pw_content->pitch_text ?>
								<?php endif ?>
							</div>
							<div id="pitch_textarea_div" class="dnone">
								<div id="marker-buttons" class="btn-group">
									<?php foreach ($vd->markers as $marker => $label): ?>
										<button class="btn btn-small btn-marker" 
											value="((<?= $vd->esc($marker) ?>))" type="button">
											<?= $vd->esc($label) ?>
										</button>
									<?php endforeach ?>
								</div>
								<textarea name="pitch_text" required id="pitch_text"
									class="span12 in-text has-placeholder user-notes dnone" 
										data-link-default-url="((tracking-link))"
										placeholder="Pitch" readonly="readonly" /><?= 
										value_if_test(@$vd->customer_edited_rejected, 
										$vd->esc($vd->m_campaign_data->content), 
										$vd->esc($vd->m_pw_content->pitch_text))
									?></textarea>
							</div>

							<strong class="placeholder marbot-20">Pitch</strong>
						</div>

						<div class="relative pull-right dnone" id="save_cancel_buttons">
							<button class="bt-silver btn-modal span6" 
									name="bt_cancel" type="button"
									id = "bt_cancel" value="1"
									>Cancel</button>

							<button class="bt-silver btn-modal bt-orange span6" name="bt_save" type="submit"
								value="1">Save</button>                                
						</div>

						<div class="relative pull-right" id="send_buttons">
							<div class="marbot-10">
								<button class="bt-silver btn-modal bt-orange" 
										name="bt_send_to_writer" type="button"
										id = "bt_send_to_writer" value="1"
										>Send Back to Writer</button>

								<button class="<?= value_if_test(@$vd->list_completed, "btn btn-info", "btn btn-silver") ?>"
									name="bt_send_to_customer" type="submit"
									value="1" <?= value_if_test(!@$vd->list_completed, "disabled='disabled'") ?>>
									Send to Customer</button>
							</div>
							<? if ( ! @$vd->list_completed): ?>
								<div class="alert alert-error">
									The pitch list is not yet completed, can not send pitch to the customer for review.
								</div>
							<? endif ?>
						</div>
					</section>
					
					<?php if (@$vd->customer_edited_rejected): ?>
						<div class="form-section user-details pad-30v">
							<div class="border-dashed pad-10">
								<div class="relative">
									<h3 class="status-false marbot-15">Customer Edited the Pitch</h3>
									<h4 class="marbot-10">Original Subject</h4>
									<?= $vd->esc($vd->m_pw_content->subject) ?>
								</div>
								<div class="relative">
									<h4  class="pad-15v">Original Pitch </h4>
									<?= nl2br($vd->m_pw_content->pitch_text) ?>
								</div>
								<div class="relative">
									<div class="span8"></div>
									<button class="btn btn-danger" name="bt_purge_customer_changes" type="submit"
											value="1">Purge Customer Changes</button>
									</div>
								</div>
							</div>
						</div>
					<?php endif ?>

					<?php if (count($vd->rejection_log) > 1): ?>
					<div class="row-fluid marbot-30">
						<h4>Rejection Log</h4>
						<table class="grid" id="selectable-results">
							<tbody>
								<?php foreach($vd->rejection_log as $rejection): ?>
								<tr>
									<td width="25%" class="left vertical-align-top"> 
										<?php $process = Date::out($rejection->process_date); ?>
										<?= $process->format('M j, Y') ?>
									</td>

									<td class="left">
										<? if ($rejection->process == Model_Pitch_Writing_Process::
																		PROCESS_WRITTEN_SENT_TO_ADMIN): ?>
											<strong>Writer added/updated pitch</strong>
										<? elseif($rejection->process == Model_Pitch_Writing_Process::
																		PROCESS_ADMIN_REJECTED): ?>
											<strong>Admin to Writer: </strong>
											<div class="status-false">
												<?= nl2br($this->vd->esc($rejection->comments)) ?>
											</div>

										<? elseif($rejection->process == Model_Pitch_Writing_Process::
																		PROCESS_CUSTOMER_REJECTED): ?>
											<strong>Customer: </strong>
											<div class="status-false">
												<?= nl2br($this->vd->esc($rejection->comments)) ?>
											</div>

										<? elseif($rejection->process == Model_Pitch_Writing_Process::
																		PROCESS_SENT_TO_CUSTOMER): ?>
											<strong>Sent to customer for review </strong>
										<?php endif ?>
									</td>
								</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
					<?php endif ?>

					<script>					
					defer(function(){

						$("#marker-buttons .btn-marker").on("click", function() {
							var editor = CKEDITOR.instances["pitch_text"];
							var create = CKEDITOR.plugins.placeholder.createPlaceholder;
							var text = $(this).val();
							create(editor, undefined, text);
						});
						
						$("a.edit-pitch").on("click", function(ev) {
							ev.preventDefault();
							$('#subject').removeClass("dnone");
							$('#pitch_subject_text').addClass("dnone");						
							$('#pitch_textarea_div').removeClass("dnone");
							$("#div_pitch_text").addClass("dnone");
							$("#send_buttons").addClass("dnone");
							$("#save_cancel_buttons").removeClass("dnone");
							$("#save_cancel_buttons").addClass("span7");
							window.init_editor($("#pitch_text"), { height: 400 },function() {
							});	
						});
						
						$("#bt_cancel").on("click", function(ev) {
							location.href = 'admin/writing/pitch/review_single/' + <?=$vd->m_pw_order->id ?>;
						});

						$("#bt_send_to_writer").on("click", function(ev) {
							ev.preventDefault();
							var content_url = "admin/writing/pitch/load_rejection_log_modal/" + <?=$vd->m_pw_order->id ?>;
							var modal = $("#<?= $vd->rejection_modal_id ?>");
							var modal_dialog = modal.find(".modal-dialog");
							var modal_content = modal.find(".modal-content");

							$.get(content_url, function(content) {

								var m_content = $.create('div');
								m_content.html(content);

								var m_body = m_content.find('.modal-body');
								var m_body_html = m_body.html();
								var m_footer = m_content.find('.modal-footer');
								var m_footer_html = m_footer.html();

								var footer = $.create('div');
								footer.addClass('modal-footer');
								footer.html(m_footer_html);

								modal_dialog.append(footer);
								modal_content.html(m_body_html);
								modal.modal('show');
							});

						});

					});
					</script>

				</div>

			</div>
			<div class="span2"></div>
		</div>

	</div>
</form>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$loader->add('js/pitch_wizard.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>