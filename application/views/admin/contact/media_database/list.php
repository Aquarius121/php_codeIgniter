<form id="md-results-form">
	<table class="grid" id="selectable-results">
		
		<thead>
			
			<tr>
				<th class="condensed">
					<label class="checkbox-container inline">
						<input type="checkbox" id="all-checkbox" class="has-select-all-option" />
						<span class="checkbox"></span>
						<div class="btn-group checkbox-caret">
							<a data-toggle="dropdown" class="dropdown-toggle">
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a>Select Visible Results</a>
									<a id="md-select-all">Select All Results</a>
								</li>
							</ul>
						</div>
						<script>
						
						defer(function() {
							
							var select_all = $("#md-select-all");
							var mdob = window.__media_database_ob;
							select_all.on("click", function() {
								$("#selectable-results").addClass("has-select-all");
								mdob.has_select_all = true;
								mdob.update_selected_count();
							});
							
							$("#all-checkbox").on("change", function() {
								setTimeout(mdob.update_selected_count, 0);
							});
							
						});
						
						</script>
					</label>
				</th>
				<th class="left sortable">
					Contacts (<span class="md-result-count"><?= $vd->chunkination->total() ?></span>)
					<i class="sorter" data-column="contact"></i>
				</th>
				<th class="sortable">
					Company
					<i class="sorter" data-column="company"></i>
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
			<tr class="md-contact-result" data-id="<?= $result->id ?>">
				<td class="condensed">
					<label class="checkbox-container inline">
						<input type="checkbox" class="selectable has-select-all-option" 
							name="selected[<?= $result->id ?>]" value="1" />
						<span class="checkbox"></span>
					</label>
				</td>
				<td class="left">
					<div class="md-contact-hover-profile-container">
						<?php if ($result->picture): ?>
						<img src="<?= Stored_File::url_from_filename($result->picture->finger) ?>"
							alt="<?= $result->first_name ?> <?= $result->last_name ?>"
							class="contact-picture md-profile-activator" />
						<?php else: ?>
						<img src="<?= $vd->assets_base ?>im/media_database_finger.png"
							class="contact-picture md-profile-activator" />
						<?php endif ?>
						<?= $ci->load->view('shared/media_database/contact_hover_profile',
							array('result' => $result)); ?>
					</div>
					<h3 class="contact-name">
						<?php if ($result->first_name || $result->last_name): ?>
							<div class="marbot-5 md-profile-activator md-profile-activator-underline">
								<?= $vd->esc($result->first_name) ?> <?= $vd->esc($result->last_name) ?>
							</div>
							<div class="muted"><?= $vd->esc($vd->cut($result->email, 30)) ?></div>
						<?php else: ?>
							<div class="md-profile-activator md-profile-activator-underline"><?= 
								$vd->esc($vd->cut($result->email, 30)) ?></div>
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
	$(function(){

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
			<select name="contact_builder_id" id="contact-list-id" class="show-menu-arrow smaller">
				<option disabled selected>Select Contact List</option>
				<?php foreach ($vd->lists as $list): ?>
				<option value="<?= $list->id ?>">
					<?= $vd->esc($list->name) ?>
				</option>
				<?php endforeach ?>
			</select>
			<script> $(function() { $("#contact-list-id").on_load_select({ size: 5 }); }); </script>
			<button type="button" name="add_to_list" id="md-add-to-list"
				class="btn btn-small btn-success">Add to List</button>
			<button type="button" id="md-create-list"
				class="btn btn-small btn-info">Create List</button>
		</div>
		<?php endif ?>
		<div class="pull-right pad-15">
			<?= $ci->load->view('shared/media_database/list_options') ?>
			<?= $ci->load->view('shared/media_database/list_results_per_page') ?>			
		</div>
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
	
	var add_to_list_url = "admin/contact/media_database/add_to_list";
	var create_list_message = "The contact list has been created. {count} new contacts were added. ";
	var add_to_list_message = "{count} new contacts have been added to the selected list. ";
	var duplicates_message = "{count} contacts were not added because they already exist. ";
	var create_list_modal_id = window.create_list_modal_id;
	var create_list_modal = $(document.getElementById(create_list_modal_id));
	var create_list_button = $("#md-create-list");
	var create_list_name = $("#md-create-list-name");
	var create_list_add = $("#md-create-list-add");
	var results_form = $("#md-results-form");
	var add_to_list_button = $("#md-add-to-list");
	var mdob = window.__media_database_ob;
	var loader = $("#eob-loader");
	
	create_list_button.on("click", function() {
		create_list_modal.modal("show");
	});
	
	// remove any previous event first
	create_list_add.off("click");
	create_list_add.on("click", function() {
		create_list_modal.modal("hide");
		var name = create_list_name.val();
		if (!name) return;
		create_list_name.val("");
		var data = {};
		data.name = name;
		data.create = 1;
		data.form_data = results_form.serialize();
		data.options = $.extend({}, mdob.client.options);
		data.options.has_select_all = mdob.has_select_all;
		data.options = JSON.stringify(data.options);
		loader.addClass("enabled");
		$.post(add_to_list_url, data, function(res) {
			if (!res) return;
			loader.removeClass("enabled");
			mdob.has_select_all = false;
			window.__media_database_refresh();
			message  = create_list_message.replace("{count}", res.added_count);
			if (res.duplicates_count) 
				message += duplicates_message.replace("{count}", res.duplicates_count);
			bootbox.alert(message);
		});
	});
	
	add_to_list_button.on("click", function() {
		var data = {};
		data.create = 0;
		data.form_data = results_form.serialize();
		data.options = $.extend({}, mdob.client.options);
		data.options.has_select_all = mdob.has_select_all;
		data.options = JSON.stringify(data.options);
		loader.addClass("enabled");
		$.post(add_to_list_url, data, function(res) {
			if (!res) return;
			loader.removeClass("enabled");
			mdob.has_select_all = false;
			window.__media_database_refresh();
			message  = add_to_list_message.replace("{count}", res.added_count);
			if (res.duplicates_count) 
				message += duplicates_message.replace("{count}", res.duplicates_count);
			bootbox.alert(message);
		});
	});
	
});

window.selectable_results_bind_reset();

</script>