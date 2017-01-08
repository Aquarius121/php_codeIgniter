<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Custom Items</h1>
				</div>
				<div class="span6">
					<a class="btn bt-silver pull-right" href="<?= $vd->store_base ?>/item/edit">New Item</a>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/virtual/store/item/partials/tabs') ?>
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
					<tr class="result" data-item-id="<?= $result->id ?>">
						<td class="left" width="280">
							<h3>
								<?php if ($result->is_disabled): ?>
									<?= $vd->esc($result->name) ?>
								<?php else: ?>
									<a href="<?= $vd->store_base ?>/item/edit/<?= $result->id ?>">
										<?= $vd->esc($result->name) ?>
									</a>
								<?php endif ?>
							</h3>
							<ul>
								<?php if ($result->is_disabled): ?>
									<?php if ($result->is_custom): ?>
									<li><a href="<?= $vd->store_base ?>/item/restore/<?= $result->id ?>">Restore</a></li>
									<?php endif ?>
								<?php else: ?>
									<li><a href="<?= $vd->store_base ?>/item/edit/<?= $result->id ?>">Edit</a></li>
									<?php if ($result->is_custom): ?>
									<li><a href="<?= $vd->store_base ?>/item/delete/<?= $result->id ?>">Delete</a></li>
									<?php endif ?>
									<li><a href="#" class="create-order-link">Create Order</a></li>
								<?php endif ?>
							</ul>
						</td>
						<td width="300"><?= $vd->esc($result->comment) ?></td>
						<td><?= Cart::instance()->format($result->price) ?></td>
						<td>
							<div><a href="<?= $ci->website_url($result->order_url($vd->m_virtual_source->order_url)) ?>">Permanent Link</a></div>
							<div class="muted smaller">
								<?php $secret = $result->generate_secret(1); ?>
								<a class="status-alternative" href="<?= $ci->website_url($result->order_url(
									$vd->m_virtual_source->order_url, $secret)) ?>">24H</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->order_url(
									$vd->m_virtual_source->order_url, $secret)) ?>">48H</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-alternative" href="<?= $ci->website_url($result->order_url(
									$vd->m_virtual_source->order_url, $secret)) ?>">7D</a>
								<?php $secret = $result->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->order_url(
									$vd->m_virtual_source->order_url, $secret)) ?>">30D</a>
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
					Custom Items
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<script>
	
$(function() {

	var create_order_modal_id = <?= json_encode($vd->create_order_modal_id) ?>;
	var create_order_modal = $(document.getElementById(create_order_modal_id));
	var confirm_create_button = $("#confirm-transfer-button");
	var create_links = $(".create-order-link");

	var url_format = <?= json_encode(sprintf('%s/item/order/%d', 
		$vd->store_base, $result->id)) ?>;

	create_links.on("click", function() {
		var data_row = $(this).parents("tr");
		create_order_modal.modal("show");
		return false;
	});

	confirm_create_button.on("click", function() {
		var checked = create_order_modal.find("input.transfer-selected:checked");
		var user_id = checked.val();
		var query_string = construct_query_string({ user: user_id });
		var order_url = url_format + query_string;
		create_order_modal.modal("hide");
		window.location = order_url;
	});
	
});

</script>