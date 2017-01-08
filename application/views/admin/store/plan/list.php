<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Custom Plans</h1>
				</div>
				<div class="span6">
					<a class="btn bt-silver pull-right" href="admin/store/plan/edit">New Plan</a>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/store/plan/partials/tabs') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Plan</th>
						<th>Access</th>
						<th>Price</th>
						<th>Order Links</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result" data-plan-id="<?= $result->id ?>"
						data-order-link="<?= $vd->esc($result->item->order_url()) ?>"
						data-order-reset-link="<?= $vd->esc($result->item->order_url('order/reset')) ?>">
						<td class="left">
							<h3>
								<?php if ($result->item->is_disabled): ?>
									<?= $vd->esc($result->name) ?>
								<?php else: ?>
									<a href="admin/store/plan/edit/<?= $result->id ?>">
										<?= $vd->esc($result->name) ?>
									</a>
								<?php endif ?>
							</h3>
							<ul>
								<?php if ($result->item->is_disabled): ?>
								<li><a href="admin/store/plan/restore/<?= $result->id ?>">Restore</a></li>
								<?php else: ?>
								<li><a href="admin/store/plan/edit/<?= $result->id ?>">Edit</a></li>
								<li><a href="admin/store/plan/delete/<?= $result->id ?>">Delete</a></li>								
								<li><a href="#" class="create-order-link">Create Order</a></li>
								<?php endif ?>
							</ul>
						</td>
						<td><?= Package::name($result->package) ?></td>	
						<td>
							<?= Cart::instance()->format($result->item->price) ?>
							<div class="muted smaller">
								<?= (int) $result->item->raw_data()->period_repeat_count ?> Month(s)
							</div>
						</td>
						<td>
							<?php if ($result->item->is_disabled): ?>
							<span>-</span>
							<?php else: ?>
							<div><a href="<?= $ci->website_url($result->item->order_url()) ?>">Permanent Link</a></div>
							<div class="muted smaller">
								<?php $secret = $result->item->generate_secret(1); ?>
								<a class="status-alternative" href="<?= $ci->website_url($result->item->order_url(null, $secret)) ?>">24H</a>
								<?php $secret = $result->item->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->item->order_url(null, $secret)) ?>">48H</a>
								<?php $secret = $result->item->generate_secret(1); ?>
								&bullet; <a class="status-alternative" href="<?= $ci->website_url($result->item->order_url(null, $secret)) ?>">7D</a>
								<?php $secret = $result->item->generate_secret(1); ?>
								&bullet; <a class="status-info" href="<?= $ci->website_url($result->item->order_url(null, $secret)) ?>">30D</a>
							</div>
							<?php endif ?>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">				
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Custom Plans
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