<div class="tab-content" id="ax-tab-content">
	<div class="tab-pane fade in active">
		<div class="table-responsive">
			<form action="" method="post">
				<table class="table" id="selectable-results">
					<thead>
						
						<tr>
							<?php if (! @$vd->list->is_pitch_wizard_list && !@$vd->list->is_nr_subscriber_list): ?>
								<th class="condensed">
									<label class="checkbox-container inline">
										<input type="checkbox" id="all-checkbox" />
										<span class="checkbox"></span>
									</label>
								</th>
							<?php endif ?>
							<th class="left">Contact</th>
							<th class="ta-center">Details</th>
							<?php if (@!$vd->compact_list): ?>
							<th class="ta-center">Location</th>
							<th class="ta-center">Beats</th>
							<?php endif ?>
						</tr>
						
					</thead>
					<tbody>
						
						<?php $email_obfuscator = Media_Database_Contact_Access::email_obfuscator(); ?>
						<?php foreach ($vd->results as $result): ?>
						<?php if ($result->is_media_db_contact): ?>
						<?php $result->email = $email_obfuscator->obfuscate_parts($result->email); ?>
						<?php endif ?>
						<tr class="md-contact-result <?= value_if_test(@$vd->selected[$result->id], 'checked') ?>"
							data-id="<?= $result->id ?>">

							<?php if (! @$vd->list->is_pitch_wizard_list && !@$vd->list->is_nr_subscriber_list): ?>
							<td class="condensed">
								<label class="checkbox-container inline">
									<input type="checkbox" class="selectable" 
										name="selected[<?= $result->id ?>]" value="1"
										<?= value_if_test(@$vd->selected[$result->id], 'checked') ?> />
									<span class="checkbox"></span>
								</label>
							</td>
							<?php endif ?>							
							<td class="left">

								<?php if ($vd->compact_list): ?>

									<?php if ($result->is_media_db_contact): ?>
										<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
											$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
									<?php else: ?>
										<?= $vd->esc($result->email) ?>
									<?php endif ?>

								<?php else: ?>

									<h3 class="contact-name">
										<?php if (@$vd->list->is_nr_subscriber_list): ?>
											<?= $vd->esc($result->email) ?>
										<?php elseif ($result->is_media_db_contact): ?>
											<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
												$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
										<?php else: ?>
											<a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">
												<?= $vd->esc($result->email) ?>
											</a>
										<?php endif ?>
									</h3>

									<?php if ($result->company_id > 0 && !$result->is_nr_subscriber): ?>
									<ul class="actions">
										<li><a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">Edit</a></li>
										<li><a href="<?= $ci->uri->segment(1) ?>/contact/contact/delete/<?= $result->id ?>">Delete</a></li>
									</ul>
									<?php endif ?>

								<?php endif ?>

								<?php if ($result->is_media_db_contact): ?>
									<div class="status-info-muted contact-list-mdb-contact">Media Database Contact</div>
								<?php endif ?>

							</td>
							<td class="ta-center">

								<h3 class="contact-name">
									<?php if ($result->first_name || $result->last_name): ?>
										<?php if (@$vd->list->is_pitch_wizard_list || @$vd->list->is_nr_subscriber_list): ?>
											<div>
												<?= $vd->esc($result->first_name) ?>
												<?= $vd->esc($result->last_name) ?>
											</div>
										<?php else: ?>
											<div class="marbot-5">
												<?php if ($result->is_media_db_contact && !@$vd->is_admin_panel): ?>
												<a class="md-profile-activator md-profile-activator-underline">
													<?= $vd->esc($result->first_name) ?>
													<?= $vd->esc($result->last_name) ?>
												</a>
												<?php else: ?>
												<a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">
													<?= $vd->esc($result->first_name) ?>
													<?= $vd->esc($result->last_name) ?>
												</a>
												<?php endif ?>
											</div>									
										<?php endif ?>
									<?php endif ?>
								</h3>

								<?php if ($result->company_name): ?>
									<div class="text-muted-multiple">
										<span class="text-muted"><?= $vd->esc($result->company_name) ?></span>
										<?php if (!$vd->compact_list): ?>
											<?php if (!empty($result->contact_role)): ?>
											<div class="text-muted"><?= $vd->esc($result->contact_role->role) ?></div>
											<?php elseif ($result->title): ?>
											<div class="text-muted"><?= $vd->esc($result->title) ?></div>
											<?php endif ?>
										<?php endif ?>
									</div>
								<?php else: ?>
									<span>-</span>
								<?php endif ?>

							</td>
							<?php if (!@$vd->compact_list): ?>
							<td class="ta-center">								
								<?php if (!empty($result->locality) || !empty($result->region)
									|| !empty($result->country)): ?>
									<?php if ($result->locality): ?>
										<?= $result->locality->name ?><?php 
											if ($result->region): ?>,<?php endif ?>
									<?php endif ?>
									<?php if ($result->region && $result->locality && $result->region->abbr): ?>
										<?= $vd->esc($result->region->abbr) ?>
									<?php elseif ($result->region): ?>
										<?= $vd->esc($result->region->name) ?>
									<?php endif ?>
									<?php if ($result->country): ?>
									<div class="text-muted">
										<?= $vd->esc($result->country->name) ?>
									</div>
									<?php endif ?>
								<?php else: ?>								
								<span>-</span>
								<?php endif ?>
							</td>
							<td class="ta-center">
								<?php if ($result->beat_1_name): ?>
								<div><?= $vd->esc($result->beat_1_name) ?></div>
								<?php endif ?>
								<?php if ($result->beat_2_name): ?>
								<div class="text-muted"><?= $vd->esc($result->beat_2_name) ?></div>
								<?php endif ?>
								<?php if (!$result->beat_1_name && !$result->beat_2_name): ?>
								<span>-</span>
								<?php endif ?>
							</td>
							<?php endif ?>
						</tr>
						<?php endforeach ?>

						<?php if (!count($vd->results)): ?>
						<tr>
							<td colspan="3" class="ta-left">
								<?php if (@$is_search_result): ?>
									No contacts found
								<?php else: ?>
									<a href="manage/contact/import">Import</a> or 
									<?php if (@$vd->list->id): ?>
										<a href="manage/contact/contact/edit/from/<?= $vd->list->id ?>">add</a>
									<?php else: ?>
										<a href="manage/contact/contact/edit">add</a>
									<?php endif ?> contacts
								<?php endif ?>
							</td>
						</tr>
						<?php endif ?>

					</tbody>
				</table>

				<script>
				
				$(function() {

					var all_checkbox = $("#all-checkbox");
					var results = $("#selectable-results");
					
					all_checkbox.on("change", function() {
						results.find("input.selectable").prop("checked", 
							all_checkbox.is(":checked"));
					});
					
					results.on("click", "tr", function(ev) {
						if ($(ev.target).is("a")) return;
						var cb = $(this).find("input.selectable");
						cb.prop("checked", !cb.is(":checked"));
					});
				});
				
				</script>
				
				<div class="clearfix listing-bottom-buttons">

					<?php if ($vd->results && 
						!@$vd->list->is_pitch_wizard_list && 
						!@$vd->list->is_nr_subscriber_list): ?>

					<div class="pull-left pad-15" id="selectable-controls">		

						
						<?php if ($vd->list): ?>
						<button type="submit" name="remove_from_list" value="1" 
							class="btn btn-small btn-warning">Remove from List</button>
						<?php endif ?>
						<button type="submit" name="delete" value="1" 
							class="btn btn-small btn-danger">Delete from System</button>
					

						<select name="contact_list_id" id="contact-list-id" 
							class="selectpicker show-menu-arrow dropup">
							<?php foreach ($vd->lists as $list): ?>
							<option value="<?= $list->id ?>">
								<?= $vd->esc($list->name) ?>
							</option>
							<?php endforeach ?>
						</select>

						<script>

						$(function(){

							var contact_list_id = $("#contact-list-id");
							contact_list_id.on_load_select();

						});

						</script>

						<button type="submit" name="add_to_list" value="1" 
							class="btn btn-small btn-success">Add to List</button>
						
					</div>

					<?php endif ?>

					<?php if ($vd->list && $vd->list->id): ?>
					<div class="pull-right pad-15">

						<select class="selectpicker show-menu-arrow dropup select-right" id="select-results-per-page">
							<option value="10">10 Results Per Page</option>
							<option value="20" selected>20 Results Per Page</option>
							<option value="50">50 Results Per Page</option>
							<option value="100">100 Results Per Page</option>
						</select>

						<span class="ax-loadable per-page-results dnone" data-ax-elements="#ax-chunkination, #ax-tab-content">
							<a href="#" class="fake-ax-load"></a>
						</span>

						<script> 

						$(function() {

							var results_per_page = $("#select-results-per-page");
							results_per_page.val(<?= $vd->chunk_size ?>);
							results_per_page.on_load_select();

							results_per_page.on("change", function() {
								var size = results_per_page.val();
								var per_page_results_link = $(document).find("a.fake-ax-load");
								var href = "manage/contact/list/edit/<?= $vd->list->id ?>/1/" + size;
								per_page_results_link.attr("href", href);
								per_page_results_link.trigger("click");
							});

							$("#select-results-per-page").on_load_select({
								size: 10
							});

						}); 

						</script>

					</div>
					<?php endif ?>

				</div>
			</form>
		</div>
	</div>
</div>
</div></div>

<div id="ax-chunkination">

	<div class="ax-loadable"
		data-ax-elements="#ax-chunkination, #ax-tab-content">
		<?= $vd->chunkination->render() ?>
	</div>

	<p class="pagination-info ta-center">
		Displaying <?= count($vd->results) ?> 
		of <?= $vd->chunkination->total() ?> Contacts
		<?php if (isset($vd->unique_contact_count)): ?>
			<br /><span class="text-muted">
				<?= $vd->unique_contact_count ?> unique addresses
			</span>
		<?php endif ?>
	</p>

</div>