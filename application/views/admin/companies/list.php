<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Companies</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/companies/all" 
				href="admin/companies/all<?= $vd->esc(gstring()) ?>">All</a></li>			
			<li><a data-on="^admin/companies/basic" 
				href="admin/companies/basic<?= $vd->esc(gstring()) ?>">Basic</a></li>
			<li><a data-on="^admin/companies/newsroom" 
				href="admin/companies/newsroom<?= $vd->esc(gstring()) ?>">Newsroom</a></li>
			<li><a data-on="^admin/companies/archived" 
				href="admin/companies/archived<?= $vd->esc(gstring()) ?>">Archived</a></li>
		</ul>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Company</th>
						<th>Status</th>
						<th>Owner</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>
								<a href="admin/publish?filter_company=<?= $result->id ?>" 
									class="add-filter-icon"></a>
								<a class="view" href="<?= $result->url('manage') ?>" target="_blank">
									<?= $vd->esc($vd->cut($result->company_name, 45)) ?>
								</a>
							</h3>	
							<ul>
								<li><a href="<?= $result->url('manage/newsroom/company') ?>" target="_blank">Profile</a></li>
								<li><a href="<?= $result->url('manage/newsroom/customize') ?>" target="_blank">Customize</a></li>
								<li><a href="#" class="transfer-link">Transfer</a></li>
								<li><a href="<?= $result->url('manage/analyze') ?>" target="_blank">Stats</a></li>
								<li><a href="#" data-id="<?= $result->id ?>" class="delete-link">Delete</a></li>
							</ul>
						</td>
						<td>
							<?php if ($result->is_active): ?>
							<span><a href="<?= $result->url() ?>">Newsroom</a></span>
							<?php elseif ($result->is_archived): ?>
							<span class="muted">Archived</span>
							<?php elseif ($result->is_legacy): ?>
							<span>Basic</span>
							<?php else: ?>
							<span>Basic 
								<?php if ($result->access_token): ?>
									<a href="<?= $result->url() ?>?preview=<?= $vd->esc($result->access_token) ?>" target="_blank">
										<img class="private_preview_icon" src="assets/im/private_preview_link.jpg" 
											title="Private Preview Link">
									</a>
								<?php else: ?>
									<a href="<?= gstring("admin/companies/private_preview_link/{$result->id}") ?>">
										<img class="private_preview_icon" src="assets/im/generate_private_preview_link.png"
											title="Generate Private Preview Link">
									</a>
								<?php endif ?>
							</span>							
							<?php endif ?>
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
					Companies
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<script>
	
$(function() {

	var company_id = null;
	var transfer_modal_id = <?= json_encode($vd->transfer_modal_id) ?>;
	var transfer_modal = $(document.getElementById(transfer_modal_id));
	var confirm_transfer_button = $("#confirm-transfer-button");
	var transfer_links = $(".transfer-link");

	transfer_links.on("click", function() {
		company_id = $(this).parents("tr").data("id");
		transfer_modal.modal("show");
		return false;
	});

	confirm_transfer_button.on("click", function() {
		var checked = transfer_modal.find("input.transfer-selected:checked");
		var user_id = checked.val();
		transfer_modal.modal("hide");
		var data = { user: user_id, company: company_id };
		$.post('admin/companies/transfer_to_user', data, function(res) {
			if (res.redirect) return window.location = res.redirect;
			if (res.reload) return window.location = window.location;
		});
	});

	$(".delete-link").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		var modal_id = <?= json_encode($vd->delete_modal_id) ?>;
		var content_url = "admin/companies/delete_modal/" + id;
		var modal = $(document.getElementById(modal_id));
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal("show");
		});
	})
	
});	

</script>