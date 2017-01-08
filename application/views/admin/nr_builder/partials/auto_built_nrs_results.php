<table class="grid">
	<thead>
		<tr>
			<th class="has-checkbox">
				<label class="checkbox-container inline">
					<input type="checkbox" id="all-checkbox" />
					<span class="checkbox"></span>
				</label>
			</th>
			<th class="left">Email</th>
			<th class="left">Company</th>
			<th>Private URL</th>
			<th>Date Created</th>
			<th>Date Exported</th>
		</tr>
		
	</thead>
	<tbody class="results">
		
		<?php foreach ($vd->results as $result): ?>
		<tr data-id="<?= $result->id ?>" class="result">
			<td class="has-checkbox">
				<?php if ($vd->check_prn_sop_valid_lead): ?>
					<?php if ($result->is_prn_valid_lead && $result->valid_till_now): ?>
						<label class="checkbox-container inline">
							<input type="checkbox" class="selectable" 
								name="selected[<?= $result->id ?>]" 
								value="<?= $result->company_id ?>" />
							<span class="checkbox"></span>
						</label>
					<?php endif ?>
				<?php else: ?>
					<label class="checkbox-container inline">
						<input type="checkbox" class="selectable" 
							name="selected[<?= $result->id ?>]" 
							value="<?= $result->company_id ?>" />
						<span class="checkbox"></span>
					</label>
				<?php endif ?>
			</td>

			<td class="left">
				<?= $vd->esc($result->o_user_email) ?>
				<?php if (@$result->is_dup_website): ?>
					<div class="status-false">
						Newsroom already exists
					</div>
				<?php endif ?>

				<?php if ($vd->check_prn_sop_valid_lead): ?>
					<?= $this->load->view('admin/nr_builder/partials/nr_builder_tds/prn_sop_valid_lead', 
						array('result' => $result), false) ?>
				<?php endif ?>
			</td>
			<td class="left">
				<h3>
					<a class="view" href="<?= $result->url('manage') ?>" target="_blank">
						<?= $vd->esc($vd->cut($result->company_name, 45)) ?>
					</a>								
				</h3>	
				<ul>
					<li><a href="<?= $result->url() ?>" target="_blank">Newsroom</a></li>
					<li><a href="<?= $result->url('manage/newsroom/customize') ?>" 
							target="_blank">Customize</a></li>
				</ul>
				
					
				
			</td>
			
			<td>
				<?php if ($result->token): ?>
					<a href="<?= $result->url() ?>c/<?= 
							$result->token ?>" 
							target="_blank">Private URL</a>
				<?php endif ?>
			</td>
			
			<td>
				<?php $created = Date::out($result->date_created); ?>
				<?= $created->format('M j, Y') ?>&nbsp;
			</td>
			<td>
				<?php if ($result->date_first_exported_to_csv && 
						$result->date_first_exported_to_csv != '0000-00-00 00:00:00'): ?>
					<?php $exported = Date::out($result->date_first_exported_to_csv); ?>
					<?= $exported->format('M j, Y') ?>&nbsp;
				<?php else: ?>
					-
				<?php endif ?>

			</td>
		</tr>
		<?php endforeach ?>
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

<div class="row-fluid">
	<div class="span2"></div>
	<div class="span7 pad-20v">
		<?= $ci->load->view('admin/nr_builder/partials/export_buttons_footer') ?>
	</div>
	<div class="span2"></div>
</div>

<?= $vd->chunkination->render() ?>