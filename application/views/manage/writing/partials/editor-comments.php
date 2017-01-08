<?php if (!empty($vd->wr_raw_data->editor_comments)): ?>
<div class="alert alert-warning hard-edges">
	<div class="text-error"><strong>Editor Comments</strong></div>
	<p><?= $vd->esc($vd->wr_raw_data->editor_comments) ?></p>

	<div class="marbot-20">
		<small class="header-help-block">
			Please review editor comments 
			to fix or add any details to 
			your press release writing details.
		</small>
	</div>
	<div>
		<small class="header-help-block">
			You may also place a comment/question 
			to the editor at the end of the form 
			for further clarification.	
		</small>
	</div>
	
</div>
<?php endif ?>