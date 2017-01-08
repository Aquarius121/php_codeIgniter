<fieldset class="form-section contact-lists">
	<?php if (@$lists_all): ?>
	<legend>Select Contacts</legend>	
	<div class="marbot-15">
		<label class="checkbox-container">
			<input type="checkbox" name="all_contacts" id="all-contacts" 
				value="1" <?= value_if_test(@$vd->campaign->all_contacts, 'checked') ?> />
			<span class="checkbox"></span>
			Send to all imported contacts.
		</label>
		<div class="text-muted">
			This does not include contacts added from the media database. 
			Please select the contact list(s) manually for those campaigns. 
		</div>
	</div>
	
	<?php if (@$vd->pw_list && $vd->pl_contacts_count): ?>
		<div class="marbot-15">
			<label class="checkbox-container">
				<input type="checkbox" name="lists[]"
					value="<?= @$vd->pw_list->contact_list_id ?>"
					checked=checked />
				<span class="checkbox"></span>
				Send to <a href="manage/contact/list/edit/<?= $vd->pw_list->contact_list_id?>">
							Pitch Wizard selected list (<?= $vd->pl_contacts_count ?> Contacts)</a>.
			</label>
			<div class="text-muted">
				This is the purchased list of targeted contacts selected by our team for this campaign.
			</div>
		</div>
	<?php endif ?>
	<script>
	
	$(function() {
		
		window.__update_contact_lists_ui = function() {
			var is_checked = all_contacts.is(":checked");
			$("#contact-lists select").prop("disabled", is_checked);
			$("#contact-lists .bootstrap-select .btn").prop("disabled", is_checked);
			$("#add-list").prop("disabled", is_checked);
			$("#create-list").prop("disabled", is_checked);
		};
		
		var all_contacts = $("#all-contacts");
		all_contacts.on("change", window.__update_contact_lists_ui);
		
	});
	
	</script>
	<?php else: ?>
	<legend>Contact Lists
		<?php if (@$is_from_import_form): ?>
			<p class="help-block nomar">A new list will be automatically created for the imported contacts</p>
		<?php endif ?>
	</legend>

	<?php endif ?>
	<div id="contact-lists">
		<?php if (!@$vd->related_lists && @$vd->from_m_contact_list): ?>
		<?php $vd->related_lists = array($vd->from_m_contact_list); ?>
		<?php endif ?>
		<?php $in_lists_count = count(@$vd->related_lists); ?>
	
		<?php for ($i = 0; $i < max($in_lists_count, 1); $i += 2): ?>
		<div class="row form-group">
			<?php for ($o = 0; $o < 2 && ($i + $o) < max($in_lists_count, 1); $o++): ?>
				<div class="col-md-6 col-sm-6 col-xs-12 list-container list-select-container">
					<select class="form-control show-menu-arrow selectpicker" name="lists[]">
						<option class="selectpicker-default" 
							title="Select List" value="">None</option>
						<?php if (isset($vd->related_lists[$i+$o])): ?>
							<?php $in_list = $vd->related_lists[$i+$o]; ?>
							<?php foreach ($lists as $list): ?>
							<option value="<?= $list->id ?>"
								<?= value_if_test($in_list->id == $list->id, 'selected') ?>>
								<?= $vd->esc($list->name) ?>
							</option>
							<?php endforeach ?>
						<?php else: ?>
							<?php foreach ($lists as $list): ?>
							<option value="<?= $list->id ?>">
								<?= $vd->esc($list->name) ?>
							</option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			<?php endfor ?>
		</div>
		<?php endfor ?>
	</div>
	<?php if (@$lists_allow_create): ?>
	<div class="marbot-15 btn-group">	
		<button id="add-list" class="btn btn-primary btn-sm nomar" type="button">
			Add
		</button>
		<button id="create-list" class="btn btn-sm" type="button">
			Create
		</button>
	</div>
	<?php else: ?>
	<div class="marbot-15 btn-group">	
		<button id="add-list" class="btn btn-sm btn-bordered" type="button">
			Add List
		</button>	
	</div>
	<?php endif ?>
	<script>
	
	$(function() {
	
		var conf = { size: 5, container: 'body' };		
		var contact_lists = $("#contact-lists");

		contact_lists.find("select").on_load_select(conf);
		contact_lists.addClass("added-selectpicker");
		
		$(window).load(function() {
			if (window.__update_contact_lists_ui !== undefined)
				window.__update_contact_lists_ui();
		});
		
		$("#add-list").on("click", function() {
			var source_list = contact_lists.find(".list-select-container").eq(0);
			var last_list = contact_lists.find(".list-container").last();
			
			var new_list = source_list.clone();
			var news_list_select = new_list.find("select");
			new_list.find(".bootstrap-select").remove();
			new_list.append(news_list_select);
			
			new_list.find("select").val("").on_load_select(conf);
			if (last_list.parent().children().size() === 1)
				return last_list.after(new_list);
				
			var new_row = $.create("div");
			new_row.addClass("row");
			new_row.addClass("form-group");
			var new_fluid = $.create("div");
			new_row.append(new_fluid);
			new_fluid.append(new_list);
			contact_lists.append(new_row);
		});
		
		<?php if (@$lists_allow_create): ?>
		
		$("#create-list").on("click", function() {
			var last_list = contact_lists.find(".list-container").last();
			var new_list = $.create("div").addClass("col-md-6 col-sm-6 list-container");
			var in_text = $.create("input");
			in_text.addClass("in-text form-control");
			in_text.attr("name", "create_lists[]");
			in_text.attr("placeholder", "List Name");
			in_text.attr("type", "text");
			new_list.append(in_text);
			if (last_list.parent().children().size() === 1)
				return last_list.after(new_list);

			var new_row = $.create("div");
			new_row.addClass("row");
			new_row.addClass("form-group");
			var new_fluid = $.create("div");
			new_row.append(new_fluid);
			new_fluid.append(new_list);
			contact_lists.append(new_row);
		});
		
		<?php endif ?>
	
	});
	
	</script>
</fieldset>