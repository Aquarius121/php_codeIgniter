<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Contact List Manager</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Contact List</th>
						<th>Details</th>
						<th>Builder</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>
								<a class="view" href="admin/contact/list/customer/edit/<?= $result->id ?>" target="_blank">
									<?= $vd->esc($vd->cut($result->name, 45)) ?>
								</a>
							</h3>	
							<ul>
								<li><a href="admin/contact/list/customer/edit/<?= $result->id ?>" target="_blank">Edit</a></li>
								<li><a href="admin/contact/list/customer/delete/<?= $result->id ?>" target="_blank">Delete</a></li>
								<li><a data-id="<?= $result->id ?>" href="#" class="transfer-link" >Transfer</a></li>
							</ul>
						</td>
						<td>
							<?php $created = Date::out($result->date_created); ?>
							<?= $created->format('M j, Y') ?>
							<div class="muted">
								<?= (int) $result->count ?> Contacts
							</div>
						</td>
						<?= $ci->load->view('admin/partials/owner-column', 
							array('result' => $result)); ?>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Results
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<script>

$(function() {

	var contact_list_id = null;
	var transfer_modal_id = <?= json_encode($vd->transfer_modal_id) ?>;
	var transfer_modal = $(document.getElementById(transfer_modal_id));
	var confirm_transfer_button = $("#confirm-transfer-button");
	var transfer_links = $(".transfer-link");		

	transfer_links.on("click", function() {
		contact_list_id = $(this).data("id");
		transfer_modal.modal("show");
		return false;
	});

	confirm_transfer_button.on("click", function() {
		var checked = transfer_modal.find("input.transfer-selected:checked");
		var company_id = checked.val();
		transfer_modal.modal("hide");
		var data = { company: company_id, contact_list_id: contact_list_id };
		$.post('admin/contact/list/customer/transfer_to_company', data, function(res) {
			if (res.redirect) return window.location = res.redirect;
			if (res.reload) return window.location = window.location;
		});
	});

});

</script>