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
			<th>Date Confirmed</th>
			<th>Date Exported</th>
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
				<div><strong>IP:</strong> <?= $vd->esc($result->remote_addr) ?></div>
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
				<?php $date_c = Date::out($result->date_confirmed); ?>
				<?= $date_c->format('M j, Y') ?>
			</td>

			<td>
				<?php if ($result->date_exported_to_csv && 
						$result->date_exported_to_csv != '0000-00-00 00:00:00'): ?>
					<?php $exported = Date::out($result->date_exported_to_csv); ?>
					<?= $exported->format('M j, Y') ?>&nbsp;
				<?php else: ?>
					-
				<?php endif ?>

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
		Verified Claims
	</div>
</div>



<div class="row-fluid">
	<div class="span2"></div>
	<div class="span7 pad-20v">
		<?= $ci->load->view('admin/nr_builder/partials/export_buttons_footer') ?>
	</div>
	<div class="span2"></div>
</div>

<?= $vd->chunkination->render() ?>