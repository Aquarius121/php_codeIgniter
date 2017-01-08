<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

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
								<th>Status</th>
								<th>Conversation</th>
								<th>Review</th>
							</tr>
						</thead>
						
						<tbody>
							
							<?php foreach ($vd->results as $result): ?>
							<?php extract(get_object_vars($result)); ?>
							<tr>
								
								<?= $this->load->view('admin/writing/orders/partials/list-order', array('result' => $result)) ?>
																					
								<td>
									<?php $dt_updated = Date::out($writing_order->latest_status_date); ?>
									<?= $dt_updated->format('M j, Y') ?>&nbsp;
									<span class="muted"><?= $dt_updated->format('H:i') ?></span>
									<div class="muted">
										<?= Model_Writing_Order::full_process($writing_order->status) ?>
									</div>
								</td>

								<td>
									<a href="#" data-id="<?= $writing_order->id ?>"
										class="view-conversation">View Conversation</a>
								</td>

								<td>
									<a href="writing/draft/review/<?= $writing_order->id ?>/<?= 
										$writing_order_code->code() ?>" target="_blank">Review PR</a>
								</td>
							
							</tr>
							<?php endforeach ?>
							
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

<script>
defer(function(){

	$(".view-conversation").on("click", function(ev) {

		ev.preventDefault();
		var id = $(this).parents("tr")
			.children("td.order-data")
			.data("wo-id");
			
		var content_url = "admin/writing/orders/rejected/rejection_log/" + id;
		var footer_url = "admin/writing/orders/rejected/rejection_log_footer/" + id;
		
		var modal = $("#<?= $vd->rej_log_modal_id ?>");
		var modal_dialog = modal.find(".modal-dialog");
		var modal_footer = modal.find(".modal-footer");
		var modal_content = modal.find(".modal-content");
		
		if (!modal_footer.size())
		{
			var modal_footer = $.create("div");
			modal_footer.addClass("modal-footer");
			modal_dialog.append(modal_footer);
		}
		
		modal.addClass("has-footer");
		modal_footer.load(footer_url);
		modal_content.load(content_url, function() {
			modal.modal("show");
		});
	});

})
</script>