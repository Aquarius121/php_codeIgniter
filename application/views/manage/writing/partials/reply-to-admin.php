<?php if (!empty($vd->wr_raw_data->editor_comments)): ?>
<div class="alert alert-block hard-edges nopad">
	<div class="marbot-10"><strong>Reply to Editor Comments</strong></div>
	<textarea placeholder="Reply to Comment" name="reply_to_admin" class="form-control marbot-20" rows="4"></textarea>
	<button class="btn btn-primary" value="1" name="is_continue" type="submit">Reply & Save</button>
</div>
<?php endif ?>