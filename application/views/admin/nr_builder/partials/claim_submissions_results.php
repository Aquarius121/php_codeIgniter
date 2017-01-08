<table class="grid">
	<thead>
		
		<tr>
			<th class="has-checkbox">
				<label class="checkbox-container inline">
					<input type="checkbox" id="all-checkbox" />
					<span class="checkbox"></span>
				</label>
			</th>
			<th class="left td-max-20">Company</th>
			<th>Rep Detail</th>
			<th>Claim Date</th>
			<th>Sales Agent</th>
			<th>Action</th>
		</tr>

	</thead>
	<tbody class="results">
		
		<?php foreach ($vd->results as $result): ?>
		<tr class="result" id="row_<?= $result->id ?>">
			<td class="has-checkbox">
				<label class="checkbox-container inline">
					<input type="checkbox" class="selectable" 
						name="selected[<?= $result->claim_id ?>]" 
						value="<?= $result->claim_id ?>" />
					<span class="checkbox"></span>
				</label>
			</td>

			<td class="left">
				<h3>
					<a class="view" href="<?= $result->url('manage') ?>" target="_blank">
						<?= $vd->esc($vd->cut($result->company_name, 45)) ?>
					</a>
				</h3>	
				<ul>
					<li><a href="<?= $result->url() ?>" target="_blank">NR</a></li>
					<li><a href="<?= $result->url('manage/newsroom/customize') ?>" 
							target="_blank">Customize</a></li>
				</ul>
				
			</td>
			
			<td>
				<?= $vd->esc($result->claimant_rep_name) ?>
				<div><?= $vd->esc($result->claimant_email) ?></div>
				<div><?= $vd->esc($result->claimant_phone) ?></div>
				<strong>IP:</strong><?php if ($result->ip_rejected_counter): ?>
					<span class="status-false">
						<?= $vd->esc($result->remote_addr) ?><br>
						(<?= $result->ip_rejected_counter ?> rejected claim<?= 
						value_if_test ($result->ip_rejected_counter > 1, 's') ?>)
					</span>
				<?php else: ?>
					<?= $vd->esc($result->remote_addr) ?>
				<?php endif ?>
			
			</td>
			
			<td>
				<?php $date_claimed = Date::out($result->date_claimed); ?>
				<?= $date_claimed->format('M j, Y') ?>
				<?php if ($result->is_from_private_link): ?>
					<div class="status-true strong">From Private Link</div>
				<?php endif ?>
			</td>

			<td>
				<?php if ($result->sales_agent): ?>
					<?= $result->sales_agent->name() ?>
				<?php else: ?>
					-
				<?php endif ?>
			</td>
			
			<td>
				<a href="admin/nr_builder/<?= $vd->nr_source ?>/confirm_claim/<?= $result->claim_id ?>">
					Confirm</a>|<a 
					href="admin/nr_builder/<?= $vd->nr_source ?>/reject_claim/<?= $result->claim_id ?>">Reject </a>
					<br>
				<a href="admin/nr_builder/<?= $vd->nr_source ?>/ignore_claim/<?= $result->claim_id ?>">Ignore</a>
			</td>

		</tr>
		<?php endforeach ?>
	</tbody>
</table>

<script>
defer(function() {
	$('#all-checkbox').click(function(event) { 
		if(this.checked) { // check select status
			$('.selectable').each(function() { 
				this.checked = true;
			});
		}else{
			$('.selectable').each(function() { 
				this.checked = false; 
			});
		}
	});

});
</script>

<div class="clearfix">
	<div class="pull-left grid-report ta-left">
		All times are in UTC.
	</div>
	<div class="pull-right grid-report">
		Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> 
		Newsroom Claims
	</div>
</div>

<div class="row-fluid">
	<div class="span12 pad-20v">
		<div class="ta-center">
			<button type="submit" name="bulk_confirm_btn" value="1"
				class="btn btn-success">Bulk Confirm</button>

			<button type="submit" name="bulk_reject_btn" value="1"
				class="btn btn-danger btn-export">Bulk Reject</button>

			<button type="submit" name="bulk_ignore_btn" value="1"
				class="btn btn-silver btn-export">Bulk Ignore</button>
		</div>
	</div>
</div>

<?= $vd->chunkination->render() ?>