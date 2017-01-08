<?php if ($vars->result->is_ready_to_build && ! @$vars->result->inews_user_email): ?>
	<label class="checkbox-container inline">
		<input type="checkbox" class="selectable" 
			name="selected[<?= $vars->result->id ?>]" 
			value="<?= $vars->result->source_company_id ?>" />
		<span class="checkbox"></span>
	</label>
<?php else: ?>
	<label class="checkbox-container inline">
		<input type="checkbox" disabled />
		<span class="checkbox"></span>
	</label>
<?php endif ?>

<?php if ($vars->result->inews_user_email): ?>
	<i class="status-false">Dup Email</i>
<?php endif ?>

<script>
$(function() {

	$('#all-checkbox').click(function(event) {
		if(this.checked) {
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