<ul class="breadcrumb">
	<li><a href="manage/contact">Media Outreach</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/contact/list">Lists</a> <span class="divider">&raquo;</span></li>
	<li class="active">
		<?php if (@$vd->list->is_pitch_wizard_list): ?>
			Pitch Order <?= @$vd->pw_order->id ?> | 
				<?= $vd->esc(@$vd->pw_order->city) ?>, <?= @$vd->state_abbr ?> | 
				<?= $vd->esc(@$vd->pw_order->keyword) ?>
		<?php else : ?>
			<?= $vd->esc($vd->list->name) ?>
		<?php endif ?>
	</li>
</ul>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 page-title">
				<h2>Edit Contact List</h2>
			</div>

			<div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
				<ul class="list-inline actions">
				<?php if (!$vd->list->is_pitch_wizard_list && !$vd->list->is_nr_subscriber_list): ?>
					<li>
						<a href="manage/contact/list/download/<?= $vd->list->id ?>"
						class="btn btn-default">
							Download
						</a>
					</li>
					<li>
						<a href="manage/contact/list/duplicate/<?= $vd->list->id ?>"
						class="btn btn-info">
							Duplicate
						</a>
					</li>
					<?php if ($vd->has_list_history): ?>
						<li>
							<a href="#" class="list-history btn btn-default pad-20h" data-id="<?= $vd->list->id ?>">
								History
							</a>
						</li>
					<?php endif ?>
					<li>
						<a href="manage/contact/contact/edit/from/<?= $vd->list->id ?>"
							class="btn btn-primary">
							Add Contact
						</a>
					</li>
				<?php endif ?>
				</ul>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">

					<div class="row marbot-20">
						<div class="col-lg-4" id="list-name-editable">
							<div class="normal">
								<legend class="list-title">
									<?php if (@$vd->list->is_pitch_wizard_list): ?>
										<span class="text">
											Pitch Order <?= @$vd->pw_order->id ?> | 
												<?= $vd->esc(@$vd->pw_order->city) ?>, <?= @$vd->state_abbr ?> | 
												<?= $vd->esc(@$vd->pw_order->keyword) ?>
										</span>
									<?php elseif(@$vd->list->is_nr_subscriber_list): ?>
										<span class="text"><?= $vd->esc($vd->list->name) ?></span>
									<?php else: ?>
										<span class="text"><?= $vd->esc($vd->list->name) ?></span>
										<a><i class="fa fa-fw fa-edit"></i></a>
									<?php endif ?>
								</legend>
							</div>
							<div class="edit" style="display: none">
								<input type="text" class="form-control" value="<?= $vd->esc($vd->list->name) ?>" />
							</div>
						</div>
					</div>

					<?= $this->load->view('manage/contact/partials/contact_listing') ?>		

				</div>

			</div>

			<script>
			
			$(function() {

				var container = $("#list-name-editable");
				var normal = container.find(".normal");
				var edit = container.find(".edit");
				var input = edit.find("input");
				var h2_text = normal.find("legend.list-title .text");
				
				var do_edit = function() {
					normal.hide();
					edit.show();
					input.focus();
				};
				
				var do_after_save = function() {
					h2_text.text(input.val());
					input.attr("disabled", false);
					normal.show();
					edit.hide();
				};
				
				var do_save = function() {
					var value = $.trim(input.val());
					if (!value) return after_save(input.val(h2_text.text()));
					input.attr("disabled", true);
					var data = { name: value };
					$.post("manage/contact/list/rename/<?= $vd->list->id ?>", 
						data, do_after_save);
				};
				
				normal.find("a").on("click", do_edit);
				input.on("blur", do_save);
				input.on("keypress", function(ev) {
					if (ev.which == 13) do_save();
				});

				var hid = $.create("input");
				var search_form = $(".navbar-search");
				hid.attr('type', 'hidden');
				hid.attr('name', 'contact_list_id');
				hid.attr('value', '<?= $vd->list->id ?>');
				search_form.append(hid);

				$('#search-box').attr("placeholder", "Search Contact List");
				
			});
			
			</script>

		</div>
	</div>

<script>
$(function() {

	$(document).on("click", ".list-history", function(ev) {

		ev.preventDefault();
		var id = $(this).data("id");

		var content_url = "manage/contact/list/load_history_modal/" + id;
		var modal = $("#<?= $vd->history_modal_id ?>");
		
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});

	});

});
</script>
