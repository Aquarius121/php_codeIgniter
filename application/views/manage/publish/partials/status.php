<legend>
	Status: 
	<?php if (!$vd->m_content): ?>
	<span class="text-muted">Not Saved</span>
	<?php elseif ($vd->m_content->is_published): ?>
	<span class="text-muted">Published</span>
	<?php elseif ($vd->m_content->is_under_review): ?>
	<span class="text-muted">Under Review</span>
	<?php elseif ($vd->m_content->is_draft): ?>
	<span class="text-muted">Not Published (Draft)</span>
	<?php else: ?>
	<span class="text-muted">Scheduled</span>
	<?php endif ?>
</legend>

<?php if (Auth::is_admin_online() && $vd->m_content && !$vd->duplicate): ?>
<div class="row form-group">
	<div class="col-lg-12">
		<div class="status-reset">
			<a href="#" data-id="<?= $vd->m_content->id ?>">Reset Status</a>
			<script>
			
			$(function() {
				
				var message = "This action will reset the status for the \
					content and return it to a draft. ";
				
				var status_reset_link = $(".status-reset a");
				status_reset_link.on("click", function(ev) {
					ev.preventDefault();
					bootbox.confirm(message, function(res) {
						if (!res) return;
						var data = {};
						data.confirmed = 1;
						data.id = status_reset_link.data("id");
						$.post("manage/publish/common/reset", data, function() {
							window.location = window.location;
						});
					});
				});
				
			});
			
			</script>
		</div>
	</div>
</div>
<?php endif ?>
