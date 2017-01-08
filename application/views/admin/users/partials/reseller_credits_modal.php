<form method="post">
    <div id="credits_modal" class="modal hide fade modal-autoheight" tabindex="-1" 
        role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="icon-remove"></i>
            </button>
            <h3 id="modalLabel">Add PR Writing Credits</h3>
        </div>
        
        <div class="modal-body" style="max-height:300px !important;">
            <div class="span6">            			
                <div class="span3">
                    <p>
                        <label>Number of Credits to Add: </label>
                        <input type="text" required name="num_credits"  /> <br />
                    </p> 
                </div>                                       
                <div class="span2"><br />
                    <button type="submit" name="add_credits" value="1" 
                        class="bt-orange pull-right">Add Credits</button>
                </div>
                    
            </div>
        </div>
    </div>    
</form>