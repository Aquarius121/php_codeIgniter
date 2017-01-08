<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<form action="" method="post">
				<table class="grid" id="selectable-results">
					<thead>
						
						<tr>
							<th class="condensed">
								<label class="checkbox-container inline">
									<input type="checkbox" id="all-checkbox" />
									<span class="checkbox"></span>
								</label>
							</th>
							<th class="left">Contact</th>
							<th>Company</th>
							<?php if (!$vd->compact_list): ?>
							<th>Location</th>
							<th>Beats</th>
							<?php endif ?>
						</tr>
						
					</thead>
					<tbody>
						
						<?php foreach ($vd->results as $result): ?>
						<tr class="md-contact-result <?= value_if_test(@$vd->selected[$result->id], 'checked') ?>"
							data-id="<?= $result->id ?>">

							<td class="condensed">
								<label class="checkbox-container inline">
									<input type="checkbox" class="selectable" 
										name="selected[<?= $result->id ?>]" value="1"
										<?= value_if_test(@$vd->selected[$result->id], 'checked') ?> />
									<span class="checkbox"></span>
								</label>
							</td>

							<td class="left">
								<?php if ($vd->compact_list): ?>
									<?= $vd->esc($result->email) ?>
								<?php else: ?>
									<h3 class="contact-name">
										<?php if ($result->first_name || $result->last_name): ?>
											<div class="marbot-5">
												<a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">
													<?= $vd->esc($result->first_name) ?>
													<?= $vd->esc($result->last_name) ?>
												</a>
											</div>
											<div class="muted">
												<?= $vd->esc($result->email) ?>
											</div>
										<?php else: ?>
											<div>
												<a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">														
													<?= $vd->esc($result->email) ?>
												</a>
											</div>
										<?php endif ?>
									</h3>
									<ul>
										<li><a href="<?= $ci->uri->segment(1) ?>/contact/contact/edit/<?= $result->id ?>">Edit</a></li>
										<li><a href="<?= $ci->uri->segment(1) ?>/contact/contact/delete/<?= $result->id ?>">Delete</a></li>
									</ul>
								<?php endif ?>
							</td>
							<td>
								<?php if ($result->company_name): ?>
								<?= $vd->esc($result->company_name) ?>
									<?php if (!$vd->compact_list): ?>
										<?php if (!empty($result->contact_role)): ?>
										<div class="muted"><?= $vd->esc($result->contact_role->role) ?></div>
										<?php elseif ($result->title): ?>
										<div class="muted"><?= $vd->esc($result->title) ?></div>
										<?php endif ?>
									<?php endif ?>
								<?php else: ?>
								<span>-</span>
								<?php endif ?>
							</td>
							<?php if (!$vd->compact_list): ?>
							<td>								
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
									<div class="muted">
										<?= $vd->esc($result->country->name) ?>
									</div>
									<?php endif ?>
								<?php else: ?>								
								<span>-</span>
								<?php endif ?>
							</td>
							<td>
								<?php if ($result->beat_1_name): ?>
								<div><?= $vd->esc($result->beat_1_name) ?></div>
								<?php endif ?>
								<?php if ($result->beat_2_name): ?>
								<div class="muted"><?= $vd->esc($result->beat_2_name) ?></div>
								<?php endif ?>
								<?php if (!$result->beat_1_name && !$result->beat_2_name): ?>
								<span>-</span>
								<?php endif ?>
							</td>
							<?php endif ?>
						</tr>
						<?php endforeach ?>

					</tbody>
				</table>

				<script>
				defer(function(){

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
				
				<div class="clearfix">

					<?php if ($vd->results): ?>					
					<div class="pull-left pad-15" id="selectable-controls">
						<span class="contact-list-buttons">
							<?php if (@$vd->list): ?>
							<button type="submit" name="remove_from_list" value="1" 
								class="btn btn-small btn-grey">Remove from List</button>
							<button type="submit" name="delete" value="1" 
								class="btn btn-small btn-light-grey">Delete from System</button>
							<?php else: ?>
							<button type="submit" name="delete" value="1" 
								class="btn btn-small btn-light-grey">Delete from System</button>
							<?php endif ?>
						</span>
						<select name="contact_list_id" id="contact-list-id" class="show-menu-arrow smaller">
							<?php foreach ($vd->lists as $list): ?>
							<option value="<?= $list->id ?>">
								<?= $vd->esc($list->name) ?>
							</option>
							<?php endforeach ?>
						</select>
						<script> $(function() { $("#contact-list-id").on_load_select({ size: 5 }); }); </script>
						<button type="submit" name="add_to_list" value="1" 
							class="btn btn-small btn-success">Add to List</button>
					</div>
					<?php endif ?>

					<div class="pull-right grid-report">Displaying <?= count($vd->results) ?> 
						of <?= $vd->chunkination->total() ?> Contacts
					</div>

				</div>
				
			</form>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>