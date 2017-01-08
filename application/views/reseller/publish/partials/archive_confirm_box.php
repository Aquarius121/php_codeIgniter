<div id="archive_confirm_modal" class="modal hide fade modal-autoheight" tabindex="-1" role="dialog"
	 aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        	<i class="icon-remove"></i>
		</button>
		<h3 id="modalLabel">Confirm</h3>
	</div>	
    <div class="modal-footer ta-center">		
        <ul class="row-fluid ta-center">
            <li>
                <label for="reply_msg" style="text-transform:none !important;">Are you sure you want to archive this PR writing task?</label>                
            </li>
          	<div class="span12">&nbsp;</div>		
            <div class="span2"></div>
            <div class="span3">
               <button type="button" value="1" class="span11 bt-silver btn-blue" name="bt_yes" id="bt_yes">
               		Yes
               </button>
            </div>      
            <div class="span3">
               <button type="button" value="1" class="span11 bt-silver btn-orange" name="bt_no" id="bt_no">
               		No
               </button>   
            </div>
          </ul>
	</div>
</div>
<script>
defer(function() {

	$('#bt_yes').click(function(){		
		archive_pr_writing();
		var ch= $('#' + arch_chbox_id);
		ch.attr("checked", false);
	});
	$('#bt_no').click(function(){		
		var modal = $("#archive_confirm_modal");
		modal.modal("hide");
		var ch= $('#' + arch_chbox_id);
		ch.attr("checked", false);
	});
});
</script>