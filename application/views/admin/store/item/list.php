<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Store Items</h1>
				</div>
				<div class="span6">
					<a class="btn bt-silver pull-right" href="admin/store/item/edit">New Item</a>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/store/item/partials/tabs') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Item</th>
						<th>Comment</th>
						<th>Price</th>
						<th>Order Links</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result" data-item-id="<?= $result->id ?>"
						data-order-link="<?= $vd->esc($result->order_url()) ?>"						
						data-order-reset-link="<?= $vd->esc($result->order_url('order/reset')) ?>">
						<td class="left" width="280">
							<h3>
								<?php if ($result->is_disabled): ?>
									<?= $vd->esc($result->name) ?>
								<?php else: ?>
									<a href="admin/store/item/edit/<?= $result->id ?>">
										<?= $vd->esc($result->name) ?>
									</a>
								<?php endif ?>
							</h3>
							<ul>
								<?php if ($result->is_disabled): ?>
								<li><a href="admin/store/item/restore/<?= $result->id ?>">Restore</a></li>
								<?php else: ?>
								<li><a href="admin/store/item/edit/<?= $result->id ?>">Edit</a></li>
								<?php if ($result->is_custom): ?>
									<li><a href="admin/store/item/delete/<?= $result->id ?>">Delete</a></li>
								<?php endif ?>
								<li><a href="#" class="create-order-link">Create Order</a></li>
								<?php endif ?>
							</ul>
						</td>
						<td width="300"><?= $vd->esc($result->comment) ?></td>
						<td><?= Cart::instance()->format($result->price) ?></td>
						<td>
							<div><a href="<?= $ci->website_url($result->order_url()) ?>">Permanent Link</a></div>
							<div class="muted smaller">
								<?php $secret = $result->generate_secret(1); ?>
								<a class="status-alternative" href="<?= $ci->website_url($result->order_url(null, $secret)) ?>">24H</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->order_url(null, $secret)) ?>">48H</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-alternative" href="<?= $ci->website_url($result->order_url(null, $secret)) ?>">7D</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->order_url(null, $secret)) ?>">30D</a>
							</div>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">				
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Items
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<script>
	
$(function() {

	var order_reset_link = null;
	var create_order_modal_id = <?= json_encode($vd->create_order_modal_id) ?>;
	var create_order_modal = $(document.getElementById(create_order_modal_id));
	var confirm_create_button = $("#confirm-transfer-button");
	var create_links = $(".create-order-link");

	// admo url with formatted to work with String.format function
	var url_format = <?= json_encode(Admo::url_format(null, '{{uid}}')) ?>;

	create_links.on("click", function() {
		var data_row = $(this).parents("tr");
		order_reset_link = data_row.data("order-reset-link");
		create_order_modal.modal("show");
		return false;
	});

	confirm_create_button.on("click", function() {
		var checked = create_order_modal.find("input.transfer-selected:checked");
		var user_id = checked.val();
		var order_url = url_format.format({ uid: user_id }) + order_reset_link;
		create_order_modal.modal("hide");
		window.location = order_url;
	});
	
});	

</script>