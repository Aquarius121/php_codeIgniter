<form method="post" id="selectable-form">
	
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span6">
						<h1><?= Model_Content::full_type($vd->type) ?> Manager</h1>
					</div>
					<?php if ($this->review_mode): ?>
					<div class="span6">
						<div class="pull-right">
							<button type="button" disabled class="btn btn-success" 
								id="approve-all">Bulk Approve</button>
							<button type="button" disabled class="btn btn-danger"
								id="reject-all">Bulk Reject</button>
						</div>
					</div>
					<?php endif ?>
				</div>
			</header>
		</div>
	</div>
				
	<div class="row-fluid">
		<div class="span12">
			<ul class="nav nav-tabs nav-activate" id="tabs">
				<li><a data-on="^admin/publish/<?= $vd->type ?>/all" 
					href="admin/publish/<?= $vd->type ?>/all<?= $vd->esc(gstring()) ?>">All</a></li>
				<?php if ($vd->type === Model_Content::TYPE_PR || $vd->type == Model_Content::TYPE_NEWS): ?>
				<li><a data-on="^admin/publish/<?= $vd->type ?>/under_review" 
					href="admin/publish/<?= $vd->type ?>/under_review<?= $vd->esc(gstring()) ?>">Under Review</a></li>
				<?php endif ?>
				<li><a data-on="^admin/publish/<?= $vd->type ?>/published" 
					href="admin/publish/<?= $vd->type ?>/published<?= $vd->esc(gstring()) ?>">Published</a></li>
				<li><a data-on="^admin/publish/<?= $vd->type ?>/scheduled" 
					href="admin/publish/<?= $vd->type ?>/scheduled<?= $vd->esc(gstring()) ?>">Scheduled</a></li>
				<li><a data-on="^admin/publish/<?= $vd->type ?>/draft" 
					href="admin/publish/<?= $vd->type ?>/draft<?= $vd->esc(gstring()) ?>">Draft</a></li>
			</ul>
		</div>
	</div>

	<?= $this->load->view('admin/partials/filters') ?>

	<div class="row-fluid">
		<div class="span12">
			<div class="content listing">
				
				<table class="grid">
					<thead>
						
						<tr>
							<?php if ($this->review_mode): ?>
							<th class="has-checkbox">
								<label class="checkbox-container inline">
									<input type="checkbox" id="all-checkbox" />
									<span class="checkbox"></span>
								</label>
							</th>
							<?php endif ?>
							<th class="left">Content Title</th>	
							<th>Details</th>
							<th>Owner</th>
						</tr>
						
					</thead>
					<tbody class="results publish-results <?= $vd->esc($vd->status) ?>" id="selectable-results">						
						<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>"
							class="<?= value_if_test($result->is_priority, 'priority') ?>
								    <?= value_if_test($result->date_hold, 'hold') ?>"
							<?php if ($this->review_mode): ?>
							data-preview="<?= $vd->esc($ci->load->view(
								'admin/publish/partials/hover_preview', 
								array('result' => $result))) ?>"
							<?php endif ?>
							class="result">
							<?php if ($this->review_mode): ?>
							<td class="has-checkbox">
								<label class="checkbox-container inline">
									<input type="checkbox" class="selectable" 
										name="selected[<?= $result->id ?>]" value="1" />
									<span class="checkbox"></span>
								</label>
							</td>
							<?php endif ?>
							<td class="left">
								<h3>

									<span class="distribution-labels">

										<?php if (isset($result->release_plus_set) && count($result->release_plus_set)): ?>
										<span class="label-class edit-distribution">
											<a title="Edit Distribution" href="#dist" class="tl distribution-hover">
												<strong>EDIT</strong>
											</a>
										</span>
										<?php else: ?>
										<span class="label-class edit-distribution">
											<a title="Add Distribution" href="#dist" class="tl distribution-hover">
												<strong>ADD</strong>
											</a>
										</span>
										<?php endif ?>

										<?php $title_cut = 54; ?>
										<?php if (isset($result->release_plus_set)): ?>
										<?php foreach ($result->release_plus_set as $release_plus): ?>
										<?php if ($release_plus->is_confirmed): ?>
										<?php $title_cut -= 3; ?>
										<span class="label-class">
											<a title="<?= $vd->esc($release_plus->name()) ?>"
												<?php if ($release_plus->provider == Model_Content_Release_Plus::PROVIDER_PRNEWSWIRE): ?>
													href="admin/publish/prn_docx/<?= $result->id ?>" target="_blank" 
												<?php else: ?>
													href="<?= current_url() ?>#"
												<?php endif ?>
												class="tl">
												<strong class="status-info"><?= $vd->esc($release_plus->code()) ?></strong>
											</a>
										</span>
										<?php endif ?>
										<?php endforeach ?>
										<?php endif ?>

									</span>

									<a class="view" href="<?= $ci->common()->url($result->url()) ?>" target="_blank">
										<?= $vd->esc($vd->cut($result->title, $title_cut)) ?>
									</a>			

								</h3>
								<ul>
									<?php if ($result->is_under_review): ?>
									<li><a class="admin-approve" href="admin/publish/approve/<?= $result->id ?>">Approve</a></li>
									<li><a class="admin-hold status-alternative-2" href="#"
										data-comments="<?= $vd->esc($result->hold_comments) ?>">Hold</a></li>
									<li><a class="admin-reject" href="admin/publish/reject/<?= $result->id ?>">Reject</a></li>
									<?php endif ?>
									<li><a href="admin/publish/edit/<?= $result->id ?>" target="_blank">Edit</a></li>
									<li><a href="admin/publish/delete/<?= $result->id ?>" target="_blank">Delete</a></li>
									<li><a href="#" class="transfer-link">Transfer</a></li>									
									<?php if ($result->type == Model_Content::TYPE_PR): ?>
										<li><a href="view/pdf/<?= $result->id ?>" target="_blank">PDF</a></li>
									<?php endif ?>
									<?php if ($result->is_published && $result->company_id): ?>										
										<li><a href="admin/publish/stats/<?= $result->id ?>" target="_blank">Stats</a></li>
									<?php endif ?>
									<li><a href="#" class="show-history">History</a></li>
								</ul>
							</td>
							<td>
								<?= $result->dt_publish->format('M j, Y') ?>&nbsp;
								<span class="muted"><?= $result->dt_publish->format('H:i') ?></span>
								<?php if ($result->is_backdated): ?>
									<span class="status-alternative smaller">&nbsp;(Backdated)</span>
								<?php endif ?>
								<div class="status-info-muted admin-list-status">

									<?php if ($result->is_published): ?>
									<span>Published</span>
									<?php elseif ($result->is_under_review): ?>
									<span>Under Review</span>
									<?php elseif ($result->is_draft): ?>
									<span>Draft</span>
									<?php else: ?>
									<span>Scheduled</span>
									<?php endif ?>

									<?php if ($result->distribution_bundle): ?>
									<span class="muted">(<?= $result->distribution_bundle->short() ?>)</span>
									<?php endif ?>

								</div>
								<?php if ($result->is_under_review): ?>
									<?php if ($result->dt_rejected): ?>
									<div class="status-false smaller <?= value_if_test($result->has_feedback, 'rejection-data-inline') ?>">
										<?php if ($result->has_feedback): ?>
											<span class="rejection-data-icon"><i class="icon-comments"></i></span>
										<?php endif ?>
										Rejected: &nbsp;<?= $result->dt_rejected->format('M j, Y H:i') ?>	
										<?php if ($result->has_feedback): ?>
										<div class="alert alert-danger rejection-data">
											<?php if (!empty($result->feedback->comments)): ?>
											<div class="rejection-comments">
												<strong>General comments.</strong>
												<br /><?= nl2br($vd->esc($result->feedback->comments)) ?>
											</div>
											<?php endif ?>
											<?php if (!empty($result->feedback->canned)): ?>
												<?php foreach ((array) $result->feedback->canned as $canned): ?>
													<div class="rejection-canned">
														<strong><?= $vd->esc($canned->title) ?></strong><br />
														<?= $vd->esc($vd->cut(HTML2Text::plain($canned->content), 200)) ?>
													</div>
												<?php endforeach ?>
											<?php endif ?>
										</div>
										<?php endif ?>
									</div>
									<?php endif ?>
									<?php if ($result->dt_hold): ?>
									<div class="status-alternative-2 smaller <?= value_if_test($result->hold_comments, 'hold-data-inline') ?>">
										<?php if ($result->hold_comments): ?>
											<span class="hold-data-icon"><i class="icon-comments"></i></span>
										<?php endif ?>
										Hold: &nbsp;<?= $result->dt_hold->format('M j, Y H:i') ?>	
										<?php if ($result->hold_comments): ?>
											<div class="alert alert-alternative-2 hold-data">
												<div class="hold-comments">
													<?= nl2br($vd->esc($result->hold_comments)) ?>
												</div>
											</div>
										<?php endif ?>			
									</div>
									<?php endif ?>
									<?php if ($result->dt_approved): ?>
									<div class="status-true smaller">
										Approved: &nbsp;<?= $result->dt_approved->format('M j, Y H:i') ?>
									</div>
									<?php endif ?>
								<?php endif ?>
							</td>
							<?= $ci->load->view('admin/partials/owner-column', 
								array('result' => $result)); ?>
						</tr>
						<?php endforeach ?>

					</tbody>
				</table>
				
				<div class="clearfix">
					<div class="pull-left grid-report ta-left">
						All times are in UTC.
					</div>
					<div class="pull-right grid-report">
						Displaying <?= count($vd->results) ?> 
						of <?= $vd->chunkination->total() ?> 
						<?= Model_Content::full_type_plural($vd->type) ?>
					</div>
				</div>
				
				<?= $vd->chunkination->render() ?>
			
			</div>
		</div>
	</div>

	<script>
				
	$(function() {

		var all_checkbox = $("#all-checkbox");
		if (!all_checkbox.size()) return;
		
		var results = $("#selectable-results");
		var approve_all = $("#approve-all");
		var reject_all = $("#reject-all");

		all_checkbox.on("change", function() {
			results.find("input.selectable").prop("checked", 
				all_checkbox.is(":checked"));
			approve_all.prop("disabled", !all_checkbox.is(":checked"));
			reject_all.prop("disabled", !all_checkbox.is(":checked"));
		});

		results.on("click", "tr", function(ev) {
			var ev_target = $(ev.target);
			if (ev_target.is("a")) return;
			if (ev_target.parents("a").size()) return;
			var cb = $(this).find("input.selectable");
			cb.prop("checked", !cb.is(":checked"));
			cb.trigger("change");
		});
		
		results.on("change", "input.selectable", function() {
			var has_checked = results.find("input.selectable:checked").size();
			approve_all.prop("disabled", !has_checked);
			reject_all.prop("disabled", !has_checked);
		});
		
		approve_all.on("click", function() {
			var form = $("#selectable-form");
			form.attr("action", "admin/publish/approve_all");
			form.submit();
		});
		
		reject_all.on("click", function() {
			var form = $("#selectable-form");
			form.attr("action", "admin/publish/reject_all");
			form.submit();
		});
		
		var hover_visible = false;
		var hover_container = $("#hover-container");
		var hover_content = $("#hover-content");
		
		var hover_hide = function() {
			hover_container.addClass("hidden");
		};
		
		results.on("mouseenter", "td.has-checkbox", function() {
			var _this = $(this);
			var offset = _this.offset();
			var _this_height = _this.outerHeight();
			hover_content.html(_this.parents("tr").data("preview"));
			hover_container.removeClass("hidden");
			var height = hover_content.outerHeight();
			var scrollTop = $(window).scrollTop();
			var top = (offset.top - scrollTop);
			top += _this_height / 2;
			top -= height / 2;
			var left = (offset.left + 58);
			if (top < 20) top = 20;
			var window_height = $(window).height();
			if (top + height > window_height - 20) 
				top = window_height - height - 20;
			if (top < 20) top = 20;
			hover_container.css("top", top);
			hover_container.css("left", left);
		});
		
		results.on("mouseleave", "td.has-checkbox", function() {
			hover_hide();
		});
		
		window.__modifier_callbacks.push(function() {
			hover_hide();
		});

	});

	$(function() {

		var content_id = null;
		var transfer_modal_id = <?= json_encode($vd->transfer_modal_id) ?>;
		var transfer_modal = $(document.getElementById(transfer_modal_id));
		var confirm_transfer_button = $("#confirm-transfer-button");
		var transfer_links = $(".transfer-link");		

		transfer_links.on("click", function() {
			content_id = $(this).parents("tr").data("id");
			transfer_modal.modal("show");
			return false;
		});

		confirm_transfer_button.on("click", function() {
			var checked = transfer_modal.find("input.transfer-selected:checked");
			var company_id = checked.val();
			transfer_modal.modal("hide");
			var data = { company: company_id, content: content_id };
			$.post('admin/publish/transfer_to_company', data, function(res) {
				if (res.redirect) return window.location = res.redirect;
				if (res.reload) return window.location = window.location;
			});
		});

	});

	$(function() {

		var hold_content_modal_id = <?= json_encode($vd->hold_content_modal_id) ?>;
		if (!hold_content_modal_id) return;

		var hold_content_modal = $(document.getElementById(hold_content_modal_id));
		var hold_content_id = $("#hold-content-id");
		var admin_hold = $(".admin-hold");

		admin_hold.on("click", function(ev) {
			ev.preventDefault();
			var _this = $(this);
			var content_id = _this.parents("tr").data("id");
			hold_content_id.val(content_id);
			hold_content_modal.modal("show");
			hold_content_modal.find("textarea")
				.val(_this.data("comments"));
		});
		
	});	

	$(function() {

		var edit_dist_modal_id = <?= json_encode($vd->edit_dist_modal_id) ?>;
		if (!edit_dist_modal_id) return;

		var edit_dist_modal = $(document.getElementById(edit_dist_modal_id));
		var edit_dist_modal_content = edit_dist_modal.find(".modal-content");

		var edit_distribution = $(".edit-distribution a");
		edit_distribution.on("click", function(ev) {
			ev.preventDefault();
			var result_row = $(this).parents("tr");
			var content_id = result_row.data("id");
			edit_dist_modal.modal("show");
			edit_dist_modal_content.empty();
			var data = { id: content_id, redirect: new String(window.location) };
			$.post("admin/publish/edit_distribution", data, function(res) {
				edit_dist_modal_content.html(res);
			});
		});

		var db_hover = $(".distribution-hover");
		var db_hover_window = null;
		var $body = $(document.body);
		var $window = $(window);

		db_hover.on("mouseenter", function() {
			var _this = $(this);
			if (db_hover_window) 
				db_hover_window.remove();
			db_hover_window = $.create("div");
			db_hover_window.addClass("distribution-hover-window");
			db_hover_window.addClass("distribution-details");
			db_hover_window.addClass("loader");
			var offset = _this.offset();
			db_hover_window.css("left", offset.left - 12 - $window.scrollLeft());
			db_hover_window.css("top", offset.top + 22 - $window.scrollTop());
			var data = { id: _this.parents("tr").data("id") };
			$body.append(db_hover_window);
			db_hover_window.load("admin/publish/distribution/details", data, function() {
				db_hover_window.removeClass("loader");
			});			
		});

		db_hover.on("mouseleave", function() {
			if (db_hover_window) 
				db_hover_window.remove();
		});

	});
	
	$(function() {

		var modalId = <?= json_encode($vd->history_modal_id) ?>;
		var modal = $(document.getElementById(modalId));

		var uri = "admin/publish/history/get_history";
		var history_links = $(".show-history");

		history_links.on("click", function(ev) {

			ev.preventDefault();
			var prID = $(this).parents("tr").data("id");
			var pdata = { cid: prID };

			$.ajax({
				type: "POST",
				url: uri,
				data: pdata, 
				success: function(data) {
					modal.find(".history-modal-content").html(data);
					modal.modal("show");
				}
			});

		});	
		
	});
	
	</script>

</form>

<div id="hover-container" class="hidden">
	<div id="hover-content"></div>
</div>

