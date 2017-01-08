<form action="<?= @$vd->conversation_form_action ?>" method="post">

    <input type="hidden" name="pr_id_for_action" id="pr_id_for_action" value="<?= $vd->pr->id ?>" />  
    <ul class="row-fluid">
        <li>
            <label for="reply_msg">Comments</label>
        </li>
        <li>
            <textarea name="reply_msg_pending_<?= $vd->pr->id ?>" 
                id="reply_msg_pending_<?= $vd->pr->id ?>" class="span12"></textarea>
        </li>
    </ul>
    
    <div class="row-fluid">
        <div class="span4">
            <!--<a href="#" class="btn-reslved"><i class="icon-ok"></i> Mark as Resolved</a>-->
        </div>
        <div class="span8">
        <button class="bt-silver btn-modal btn-orange" name="pending_reply_to_writer_button"  
        	value="0" onclick="this.value=1">Reply to Writer</button>
        <button class="bt-silver btn-modal btn-blue" name="pending_reply_to_customer_button"
        	value="0" onclick="this.value=1">Reply to Customer</button>
        </div>
    </div>
    	
</form>