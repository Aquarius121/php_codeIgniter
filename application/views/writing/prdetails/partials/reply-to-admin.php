<?php if($vd->esc($vd->details_change_comments)): ?>
	<h4 style="color:red;">Editor Comments</h4>
	<br />
	<div class="marbot-20">
		<?php echo nl2br($vd->esc($vd->details_change_comments)); ?>
	</div>

	<div class="marbot-20">
		<small class="header-help-block">
			Please review editor comments to fix 
			or add any details to your press 
			release writing 
			details.
		</small>
	</div>
	<div>
		<small class="header-help-block">
			You may also place a comment/question 
			to the editor at the end of the form 
			for further clarification.	
		</small>
	</div>

	<div class="pad-10v row-fluid">
		<textarea class="reply-comments" name="reply_to_comments" placeholder="Reply to Comments"></textarea>		
	</div>
	<div>
		<button class="bt-orange marbot-10" name="" value="1" type="submit">Reply & Save</button>
	</div>
<?php endif ?>