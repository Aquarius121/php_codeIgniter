<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1><span class="status-info-muted">MDB</span> List Builder</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
						<?php if ($vd->chunkination->total()): ?>
						<a href="admin/contact/list/builder/download/<?= $vd->list->id ?>" class="bt-silver bt-publish">Download</a>
						<a href="#" id="transfer-builder-list" class="bt-silver bt-publish">Transfer</a>
						<script>

						$(function() {

							var transfer_modal_id = <?= json_encode($vd->transfer_modal_id) ?>;
							var transfer_modal = $(document.getElementById(transfer_modal_id));
							var list_id = <?= json_encode($vd->list->id) ?>;

							$("#transfer-builder-list").on("click", function() {
								transfer_modal.modal("show");
								return false;
							});

							$("#confirm-transfer-button").on("click", function() {
								var checked = transfer_modal.find("input.transfer-selected:checked");
								var company_id = checked.val();
								transfer_modal.modal("hide");
								var data = { company: company_id, list: list_id };
								$.post('admin/contact/list/builder/transfer', data, function(res) {
									if (res.redirect) window.location = res.redirect;
								});
							});

						});

						</script>
						<?php endif ?>
					</div>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		
		<div id="list-name-editable">
			<div class="normal">
				<h2>
					<span class="text"><?= $vd->esc($vd->list->name) ?></span>
					<a><i class="icon-edit"></i></a>
				</h2>
			</div>
			<div class="edit" style="display: none">
				<input type="text" value="<?= $vd->esc($vd->list->name) ?>" />
			</div>
		</div>
		
		<?= $this->load->view('admin/contact/list/partials/contact_listing') ?>	
		
		<script>
		
		$(function() {
			
			var container = $("#list-name-editable");
			var normal = container.find(".normal");
			var edit = container.find(".edit");
			var input = edit.find("input");
			var h2_text = normal.find("h2 .text");
			
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
				$.post("admin/contact/list/builder/rename/<?= $vd->list->id ?>", 
					data, do_after_save);
			};
			
			normal.find("a").on("click", do_edit);
			input.on("blur", do_save);
			input.on("keypress", function(ev) {
				if (ev.which == 13) do_save();
			});
			
		});
		
		</script>
		
	</div>
</div>