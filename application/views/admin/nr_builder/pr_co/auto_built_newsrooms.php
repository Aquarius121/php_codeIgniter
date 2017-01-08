<form method="post" id="selectable-form" action="admin/nr_builder/pr_co/export_auto_built_nrs_to_csv">
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span5">
						<h1>Auto Built NRs (PR.Co)</h1>
					</div>

					<div class="span7">
						<?= $ci->load->view('admin/nr_builder/partials/export_buttons_header') ?>
					</div>
				</div>
			</header>
		</div>
	</div>

	<?= $this->load->view('admin/nr_builder/partials/sub_menu') ?>
	<?= $this->load->view('admin/partials/filters') ?>
	<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_active_filters') ?>

	<div class="row-fluid">
		<div class="span12">
			<div class="content listing">
				<div class="row-fluid">

					<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_listing_chunk_size') ?>

					<div class="span12">
						
						<div class="pull-right">
							<strong>Filter: </strong>
							<a href="admin/nr_builder/pr_co/auto_built_nrs_not_exported">
								Not Exported to CSV
							</a> | 
							<a href="admin/nr_builder/pr_co/auto_built_nrs_already_exported">
								Already Exported to CSV
							</a> | 
							<a href="admin/nr_builder/pr_co/auto_built_nrs_already_existing">
								Already Existing NRs
							</a> | 
							<?php if (@$vd->not_exported): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_not_exported?filter_lang=English">
									English NRs
								</a>
							<?php elseif (@$vd->already_exported): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_already_exported?filter_lang=English">
									English NRs
								</a>
							<?php elseif (@$vd->already_existing_nrs): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_already_existing?filter_lang=English">
									English NRs
								</a>
							<?php else: ?>
								<a href="admin/nr_builder/pr_co/auto_built_newsrooms?filter_lang=English">
									English NRs
								</a>
							<?php endif ?>
							| 
							<?php if (@$vd->not_exported): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_not_exported?filter_lang=Non-English">
									Non/Partial English
								</a>
							<?php elseif (@$vd->already_exported): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_already_exported?filter_lang=Non-English">
									Non/Partial English
								</a>
							<?php elseif (@$vd->already_existing_nrs): ?>
								<a href="admin/nr_builder/pr_co/auto_built_nrs_already_existing?filter_lang=Non-English">
									Non/Partial English
								</a>
							<?php else: ?>
								<a href="admin/nr_builder/pr_co/auto_built_newsrooms?filter_lang=Non-English">
									Non/Partial English
								</a>
							<?php endif ?>
						</div>
						
					</div>
				</div>

				
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
							<th>Pvt. URL</th>
							<th>Date Created</th>
							<th>Date Exported</th>
						</tr>
						
					</thead>
					<tbody class="results">
						
						<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td class="has-checkbox">
								<label class="checkbox-container inline">
									<input type="checkbox" class="selectable" 
										name="selected[<?= $result->id ?>]" 
										value="<?= $result->company_id ?>" />
									<span class="checkbox"></span>
								</label>
							</td>

							<td class="left">
								<?php if (strlen($result->o_user_email) <= 40): ?>
									<?= $vd->esc($result->o_user_email) ?>
								<?php else: ?>
									<?= substr($result->o_user_email, 0, 40) ?><br>
									<?= substr($result->o_user_email, 40) ?>
								<?php endif ?>
								<?php if (@$result->is_dup_website): ?>
									<div class="status-false">
										Newsroom already exists
									</div>
								<?php endif ?>
							</td>
							<td class="left">
								<h3>
									<a class="view" href="<?= $result->url('manage') ?>" target="_blank">
										<?= $vd->esc($vd->cut($result->company_name, 45)) ?>
									</a>
									
								</h3>	
								<ul class="marbot-20">
									<li><a href="<?= $result->url() ?>" target="_blank">Newsroom</a></li>
									<li><a href="<?= $result->url('manage/newsroom/customize') ?>" 
											target="_blank">Customize</a></li>
								</ul>
								<?php if (!empty($result->newsroom_url)): ?>
									<ul>
										<li>
											<a href="<?= $result->newsroom_url ?>" target="_blank">
												PR.Co NR
											</a>
										</li>
										 <?php if (!$result->is_fetching_completed): ?>
										<li>
											<a href="admin/nr_builder/pr_co/pull_nr_data/<?= $result->id ?>">
												Pull PR.Co NR Data</a>
										</li>
										<?php else: ?>
										<li class="pad-5h">
											 PR.Co Data Pulled
										</li>
										<?php endif ?>
									</ul>
								<?php endif ?>
								<div class="clear marbot-10"></div>
								<div>
									<strong>Language: </strong>
									<?= value_if_test(@$result->is_en, 'All English') ?>
									<?= value_if_test(@$result->is_non_en, 'Non/Partial English') ?>
									<?= value_if_test(!@$result->is_en && !@$result->is_non_en, 'Not Yet Checked') ?>
								</div>
									
								
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
			
			</div>
		</div>
	</div>
</form>