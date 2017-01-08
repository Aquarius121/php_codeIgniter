<?= $this->load->view('admin/writing/orders/partials/list-header') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<div class="tab-content">
				<div class="tab-pane active">
			     
					<table class="grid writing-orders-grid">
						<thead>
							<tr>
								<th class="left">Order</th>
								<th class="ta-right">Writer Selection</th>
							</tr>
						</thead>
						
						<tbody>
							
							<?php foreach ($vd->results as $result): ?>
							<?php extract(get_object_vars($result)); ?>
							<tr>
								
								<?= $this->load->view('admin/writing/orders/partials/list-order', array('result' => $result)) ?>
																
								<td class="ta-right">
									<div class="marbot-5">
										Details Received:
										<?php $dt_ordered = Date::out($writing_order->date_ordered); ?>
										<?= $dt_ordered->format('M j, Y') ?>&nbsp;
										<span class="muted"><?= $dt_ordered->format('H:i') ?></span>
									</div>
									<form method="post" action="admin/writing/orders/assign/assign_to_writer">
										<input type="hidden" name="writing_order_id" value="<?= $writing_order->id ?>" />
										<div>
											<select class="in-table-dd show-menu-arrow ta-left
												selectpicker select-writer nomarbot" name="writer_id">
												<option value="">Select Writer</option>
												<?php foreach ($vd->writers as $writer): ?>
												<option value="<?= $writer->id ?>">
													<?= $vd->esc($writer->name()) ?>
												</option>
												<?php endforeach ?>
											</select>
										</div>
									</form>
								</td>
								
							</tr>
							<?php endforeach ?>  
							
							<script>
							
							$(function() {
								
								window.on_load_select(function() {
									
									var dd_buttons = $("div.select-writer > button.dropdown-toggle");
									dd_buttons.each(function() {
										var dd_button = $(this);
										var as_button = $.create("button");
										as_button.attr("type", "submit");
										as_button.prop("disabled", true);
										as_button.addClass("assign");
										as_button.addClass("btn-info");
										as_button.addClass("btn");
										as_button.text("Save");
										dd_button.after(as_button);
									});
									
									var dd_select = $("select.select-writer");
									dd_select.on("change", function() {
										var _this = $(this);
										var as_button = _this.parent().find("button.assign");
										as_button.prop("disabled", !_this.val);
									});
									
								});
								
							});
							
							</script>
							
						</tbody>
					</table>
				
					<div class="grid-report">Displaying <?= count($vd->results) ?> 
						of <?= $vd->chunkination->total() ?> Orders</div>
					<?= $vd->chunkination->render() ?>
					
				</div>
			</div>			
		</div>		
	</div>
</div>