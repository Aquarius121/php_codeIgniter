<!-- Now making the modal boxes for Rejected tab rejection logs. -->
<?php if (count($vd->prsRejected)): ?>  
	<?php foreach ($vd->prsRejected as $pr): ?>
		<?php if(count($pr->rejections)>0) : ?>
            <div id="log<?php echo $pr->id;?>" class="modal hide fade modal-autoheight" 
                tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="icon-remove"></i>
                    </button>
                    <h3 id="modalLabel">Rejection Log</h3>
            	</div>    
    
                <div class="modal-body" <?php if($pr->status == 'reseller_rejected' || $pr->status == 'sent_to_customer'): ?> 
                    style="min-height:290px !important;" <?php endif?>>
                    <ul>
                        <?php foreach ($pr->rejections as $rej): ?>
                        <?php if($rej->process == "reseller_rejected" || $rej->process == "sent_back_to_writer"): ?>
                            <li>
                            <section class="mb-editor-panel">
                                <p class="mb-author mb-editor">
                                    <?php if($rej->actor == 'reseller'): ?> 
                                    	Reseller 
                                    <?php elseif($rej->actor == 'admin'): ?>
                                    	Admin
                                    <?php endif ?>
                                    to Writer 
                                    <span class="mb-date">
                                        Posted 					
                                        <?php $pr_process=Date::out($rej->process_date); ?>
                                        <?=$pr_process->format('m/j');	?>
                                    </span>
                                </p>
                                <p>
                                    <?= nl2br($vd->esc($rej->comments)) ?>
                                </p>
                                </section>
                            </li>
                        <?php endif;?>
                        <?php if($rej->process=="customer_rejected"): ?>   
                            <li>
                                <section class="mb-customer-panel">
                                    <p class="mb-author  mb-customer">
                                        Customer 
                                        <span class="mb-date">
                                            Posted 
                                            <?php $pr_process=Date::out($rej->process_date); ?>
                                            <?=$pr_process->format('m/j');	?>
                                        </span>
                                    </p>
                                    <p>
                                        <?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
                                    </p>
                                </section>
                            </li>
                        <?php endif;?>
                        
                        <?php if($rej->process=="writer_request_details_revision"): ?>
                            <li>
                                <section class="mb-writer-panel">
                                    <p class="mb-author mb-writer">
                                        Writer 
                                        <span class="mb-date">
                                            Posted 
                                            <?php $pr_process=Date::out($rej->process_date); ?>
                                            <?=$pr_process->format('m/j');	?>
                                        </span>
                                    </p>
                                    <p>
                                        <?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
                                    </p>
                                </section>
                            </li> 
                        <?php endif;?> 
                        
                        
                        <?php if($rej->process == "sent_to_customer_for_detail_change"): ?>
                            <section class="mb-writer-panel">
                                <li>
                                    <p class="mb-author mb-customer">
                                        <?php if($rej->actor == 'reseller'): ?> 
                                            Reseller 
                                        <?php elseif($rej->actor == 'admin'): ?>
                                            Admin
                                        <?php endif ?>
                                         to Customer 
                                        <span class="mb-date">
                                            Posted 
                                            <?php $pr_process=Date::out($rej->process_date); ?>
                                            <?=$pr_process->format('m/j');	?>
                                        </span>
                                    </p>
                                    <p>
                                        <?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
                                    </p>
                                </li>
                            </section><br />
                        <?php endif;?>
                        
                        <?php if($rej->process == "written_sent_to_reseller"): ?>
                                <li>                        
                                    <p class="mb-author">
                                        Written/Edited by the writer on                             
                                        <?php  $p_date = Date::out($rej->process_date); ?>
                                        <?= $p_date->format('m/j');	?>
                                    </p>
                                </li>
						<?php endif;?>
                        
                        <?php if($rej->process == "customer_revise_details"): ?>
                                <li>                        
                                    <p class="mb-author">
                                        Customer revised details on                              
                                        <?php  $p_date = Date::out($rej->process_date); ?>
                                        <?= $p_date->format('m/j');	?>
                                    </p>
                                </li>
						<?php endif;?>
                            
						<?php if($rej->process == "sent_to_customer"): ?>
                                <li>                                    
									<?php if($rej->comments != "") : ?>
                                        <section class="mb-editor-panel">
                                            <p class="mb-author mb-editor">
                                                <?php if($rej->actor == 'reseller'): ?> 
                                                    Reseller 
                                                <?php elseif($rej->actor == 'admin'): ?>
                                                    Admin
                                                <?php endif ?>
                                                 to customer                        
                                                <span class="mb-date">
                                                    Posted 					
                                                    <?php $pr_process = Date::out($rej->process_date); ?>
                                                    <?=$pr_process->format('m/j');	?>
                                                </span>
                                            </p>
                                            <p>
                                            	<?= nl2br($vd->esc(stripslashes($rej->comments))) ?>
                                            </p>
                                        </section>
                                     <?php else : ?>
                                     	<p class="mb-author mb-editor">
											Sent to customer on 		
											<?php $pr_process = Date::out($rej->process_date); ?>
                                            <?=$pr_process->format('m/j');	?>
                                    	</p>
                                     <?php endif ?>
                                     
                                </li>
                            <?php endif;?>
                        <?php endforeach ?>
                    </ul>
                </div>
			<?php if($pr->status != 'reseller_rejected' && $pr->status != 'sent_to_customer'): ?>
                    <div class="modal-footer">
                        <ul class="row-fluid">
                            <li>
                                <label for="reply_msg">Comments</label>
                            </li>
                            <li>
                                <textarea name="reply_msg_rejected_<?= $pr->id ?>" id="reply_msg_rejected_<?= $pr->id ?>" class="span12"></textarea>
                            </li>
                        </ul>
                
                    <div class="row-fluid">
                        <div class="span4">
                        </div>				
                        <div class="span8">
                            <button class="bt-silver btn-modal btn-blue" name="rejected_send_to_customer_button" 
                                onclick="this.value=1">Send to Customer</button>
                            <button class="bt-silver btn-modal btn-orange" name="rejected_send_to_writer_button"  
                                value="0" onclick="this.value=1">Send to Writer</button>
                        </div>                
                    </div>
                
            </div>
            <?php else: ?>	
                    <div class="modal-footer"></div>
            <?php endif?>
			</div>

		<?php endif;?>
	<?php endforeach ?>
<?php endif;?> 
<!-- rejection log modal boxes completed here -->