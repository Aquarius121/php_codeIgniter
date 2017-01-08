<form action="<?= @$vd->conversation_form_action ?>" method="post">
	<input type="hidden" name="pr_id_for_action" id="pr_id_for_action" value="<?= $vd->pr->id ?>" />  
	<ul class="row-fluid">
		<li><label for="reply_msg">Comments</label></li>
		<li>
			<textarea name="reply_msg_rejected_<?= $vd->pr->id ?>" 
				id="reply_msg_rejected_<?= $vd->pr->id ?>" class="span12"></textarea>
		</li>
	</ul>
	<div class="row-fluid">
		<div class="span4"></div>
		<div class="span8">
			<button class="bt-silver btn-modal btn-blue"
				name="rejected_send_to_customer_button" 
				type="submit" value="1">Send to Customer</button>
			<button class="bt-silver btn-modal btn-orange"
				name="rejected_send_to_writer_button"  
				value="1" type="submit">Send to Writer</button>
		</div>
	</div>
</form>