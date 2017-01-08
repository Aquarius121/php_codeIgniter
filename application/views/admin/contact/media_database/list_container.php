<div id="md-results">

	<div class="alert alert-info fl-left pad-15h">These contacts are waiting approval for the media database.</div>

	<form id="md-results-form" method="post">
		<table class="grid" id="selectable-results">
			
			<thead>
				
				<tr>
					<th class="condensed">
						<label class="checkbox-container inline">
							<input type="checkbox" id="all-checkbox" />
							<span class="checkbox"></span>
							<script>
							
							$(function() {
								
								var mdob = window.__media_database_ob;								
								$("#all-checkbox").on("change", function() {
									setTimeout(mdob.update_selected_count, 0);
								});
								
							});
							
							</script>
						</label>
					</th>
					<th class="left sortable">
						Contact (<span class="md-result-count"><?= $vd->chunkination->total() ?></span>)
					</th>
					<th class="sortable">
						Company
					</th>
					<th>
						Location
					</th>
					<th>
						Beats
					</th>
					<script>
					
					defer(function() {
						var client = window.__media_database_client;
						var sortables = $("#selectable-results i.sorter");
						sortables.each(function() {
							var _this = $(this);
							var column = _this.data("column");
							if (!client.options.sort_column)
								_this.addClass("active");
							if (client.options.sort_column == column) {
								if (client.options.sort_reverse)
									_this.addClass("reverse");
								_this.addClass("active");
							}
						});
						
					});
					
					</script>
				</tr>
				
			</thead>
			<tbody>
				
				<?php foreach ($vd->results as $result): ?>
				<tr>
					<td class="condensed">
						<label class="checkbox-container inline">
							<input type="checkbox" class="selectable has-select-all-option" 
								name="selected[<?= $result->id ?>]" value="1" />
							<span class="checkbox"></span>
						</label>
					</td>
					<td class="left">
						<?php if ($result->picture): ?>
						<img src="<?= Stored_File::url_from_filename($result->picture->finger) ?>"
							alt="<?= $result->first_name ?> <?= $result->last_name ?>"
							class="contact-picture" />
						<?php else: ?>
						<img src="<?= $vd->assets_base ?>im/media_database_finger.png"
							class="contact-picture" />
						<?php endif ?>
						<h3 class="contact-name">
							<?php if ($result->first_name || $result->last_name): ?>
								<div class="marbot-5">
									<?= $vd->esc($result->first_name) ?>
									<?= $vd->esc($result->last_name) ?>
								</div>
								<div class="muted"><?= $vd->esc($vd->cut($result->email, 20)) ?></div>
							<?php else: ?>
								<div><?= $vd->esc($vd->cut($result->email, 20)) ?></div>
							<?php endif ?>
						</h3>
						<ul style="border-top: 1px solid #bbb; padding-top: 5px">
							<li><a href="admin/contact/contact/edit/<?= $result->id ?>">Edit</a></li>
							<li><a href="admin/contact/contact/delete/<?= $result->id ?>">Delete</a></li>
						</ul>
					</td>
					<td>
						<div>
							<?php if ($result->company_name): ?>
								<?= $vd->esc($result->company_name) ?>
							<?php else: ?>
								<span>-</span>
							<?php endif ?>
						</div>
						<div class="muted">
							<?php if ($result->contact_role): ?>
								<?= $vd->esc($result->contact_role->role) ?>
							<?php else: ?>
								<span>-</span>
							<?php endif ?>
						</div>
					</td>
					<td>
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
						
						<?php if (!$result->locality && !$result->region 
							&& !$result->country): ?>
						<span>-</span>
						<?php endif ?>
					</td>
					<td class="beats-list">
						<?php if ($result->beat_1): ?>
						<div><?= $vd->esc($result->beat_1->name) ?></div>
						<?php endif ?>
						<?php if ($result->beat_2): ?>
						<div><?= $vd->esc($result->beat_2->name) ?></div>
						<?php endif ?>
						<?php if ($result->beat_3): ?>
						<div><?= $vd->esc($result->beat_3->name) ?></div>
						<?php endif ?>
					</td>
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
				cb.trigger("change");
			});

		});
		</script>
		
		<div class="clearfix md-bottom-buttons">
			<?php if ($vd->results): ?>
			<div class="pull-left pad-15" id="selectable-controls">
				<button type="submit" name="confirm" value="1" class="btn btn-small btn-green">Confirm Selected</button>
				<button type="submit" name="reject" value="1" class="btn btn-small btn-danger btn-bold">Reject</button>
			</div>
			<?php endif ?>
		</div>
		
		<div class="clearfix">
			<div class="grid-report pull-left">
				<span id="md-selected-contacts">0</span>
				Contacts Selected
			</div>
			<div class="grid-report pull-right">
				Displaying <?= count($vd->results) ?> of 
					<?= $vd->chunkination->total() ?> Contacts
			</div>
		</div>
		
	</form>

	<script>

	$(function() {

		window.selectable_results_bind_reset();

	});

	</script>
</div>

<div id="md-results-loader"></div>