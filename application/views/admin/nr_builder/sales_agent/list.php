<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Sales Agents</h1>
				</div>
				<div class="span6">
					<a href="admin/nr_builder/sales_agent/edit" class="bt bt-orange pull-right">New Sales Agent</a>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			<table class="grid">
				<thead>	
					<tr>
						<th class="left">Agent</th>
						<th>Lead Stats</th>
						<th>Sales</th>
					</tr>
				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>
								<div>
									<?php if ($result->is_active): ?>
										<a href="javascript:void(0)" title="Active">
											<span class="status-true"> <i class="icon-ok"></i></span>
										</a>
									<?php else: ?>
										<a href="javascript:void(0)" title="Inactive">
											<span class="status-false"> <i class="icon-remove"></i></span>
										</a>
									<?php endif ?>
									<?= $result->name() ?>
								</div>
							</h3>

							<h3>
								<a class="view" href="admin/nr_builder/sales_agent/edit/<?= $result->id ?>">
									<?= $vd->esc($result->email) ?>
								</a>
							</h3>	
							<ul>
								<li><a href="admin/nr_builder/sales_agent/edit/<?= $result->id ?>">Edit</a></li>
								<li><a href="admin/nr_builder/sales_agent/delete/<?= $result->id ?>" 
									class="status-false">Delete</a></li>
								
							</ul>
						</td>
						<td>
							<div>
								NR: <?= value_if_test($result->export_count, 
									$result->export_count, 0) ?> &nbsp; &nbsp; &nbsp;
								Claimed: <?= value_if_test($result->claim_count, 
									$result->claim_count, 0) ?> &nbsp; &nbsp; &nbsp;
								Verified: <?= value_if_test($result->verified_count, 
									$result->verified_count, 0) ?>
							</div>
						</td>
						<td>
							<?php if ($result->transaction_count): ?>
								<a href="#" class="transaction-count" data-id="<?= $result->id ?>">
									<?= $result->transaction_count ?>
								</a>
							<?php else: ?>0
							<?php endif ?>
						</td>
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
					sales agent
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<script>
defer(function(){

	$("a.transaction-count").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		var modal_id = "<?= $vd->transactions_modal_id ?>";
		var content_url = "admin/nr_builder/sales_agent/transactions/"+id;

		var modal = $("#" + modal_id);
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});
	});

})
</script>