<!-- Now making the modal boxes for Pending tab action modal box. -->
<?php if (count($vd->prsPending)): ?>  
	<?php foreach ($vd->prsPending as $pr): ?>
		<?php if(count($pr->hist)>0) : ?>
            <div id="pendingLog<?php echo $pr->id;?>" class="modal hide fade modal-autoheight" 
            	tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    	<i class="icon-remove"></i>
                    </button>
                    <h3 id="modalLabel">Press Release Comments</h3>
                </div>    
                
                <div class="modal-body">
                    <ul>
                        <?php foreach ($pr->hist as $h): ?>
                            <?php if($h->process == "writer_request_details_revision"): ?>
                                <li>
                                    <section class="mb-writer-panel">
                                        <p class="mb-author mb-writer">
                                            Writer <span class="mb-date">Posted 
                                                        <?php $p_date = Date::out($h->process_date); ?>
                                                        <?= $p_date->format('m/j');	?>														
                                                   </span>
                                        </p>
                                        <p><?php echo nl2br($vd->esc($h->comments));?></p>
                                    </section>
                                </li> 
                            <?php endif;?> 
                            <?php if($h->process == "sent_back_to_writer"): ?>
                                <li>
                                     <section class="mb-customer-panel">
                                         <p class="mb-author mb-customer">
                                            <?php if($h->actor == 'admin') : ?>
                                                Admin 
                                            <?php elseif($h->actor == 'reseller') : ?>
                                                Reseller 
                                            <?php endif ?>    
                                            to Writer 
                                            <span class="mb-date">Posted 
                                                <?php $p_date = Date::out($h->process_date); ?>
                                                <?= $p_date->format('m/j');	?>		
                                            </span>
                                        </p>
                                        <p>
                                            <?= nl2br($vd->esc($h->comments)) ?>
                                        </p>
                                     </section>   
                                </li>
                            <?php endif;?>
                        
                            <?php if($h->process == "sent_to_customer_for_detail_change"): ?>
                                <li>
                                     <section class="mb-customer-panel">
                                         <p class="mb-author mb-customer">
                                            <?php if($h->actor == 'admin') : ?>
                                                Admin 
                                            <?php elseif($h->actor == 'reseller') : ?>
                                                Reseller 
                                            <?php endif ?>  
                                            to Customer 
                                            <span class="mb-date">Posted 
                                                <?php  $p_date = Date::out($h->process_date); ?>
                                                <?= $p_date->format('m/j');	?>	
                                             </span>
                                        </p>
                                        <p>
                                            <?= nl2br($vd->esc($h->comments)) ?>
                                        </p>
                                     </section>   
                                </li>
                            <?php endif;?>
                            
                        
                            <?php if($h->process == "customer_revise_details"): ?>
                                <li>                        
                                    <p class="mb-author">
                                        Customer Revised Details on                             
                                        <?php  $p_date = Date::out($h->process_date); ?>
                                        <?= $p_date->format('m/j');	?>
                                    </p>
                                    <p><?= nl2br($vd->esc($h->comments)) ?></p>
                                </li>
                            <?php endif;?>
                            
                        <!--<li>
                            <p class="mb-author">
                                Reseller to Customer <span class="mb-date">Posted 10/10/13</span>
                            </p>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ligula metus, euismod eu ligula at, pulvinar fermentum turpis. Sed iaculis enim sit amet orci scelerisque ultricies.
                            </p>
                        </li>-->
                        <?php endforeach ?>
                    </ul>
                </div>
                
                <div class="modal-footer">
                        <ul class="row-fluid">
                            <li>
                                <label for="reply_msg">Comments</label>
                            </li>
                            <li>
                                <textarea name="reply_msg_pending_<?= $pr->id ?>" id="reply_msg_pending_<?= $pr->id ?>" class="span12"></textarea>
                            </li>
                        </ul>
                    
                        <div class="row-fluid">
                            <div class="span4">
                                <!--<a href="#" class="btn-reslved"><i class="icon-ok"></i> Mark as Resolved</a>-->
                            </div>
                            <div class="span8">
                            <button class="bt-silver btn-modal btn-orange" name="pending_reply_to_writer_button"  value="0" onclick="this.value=1">Reply to Writer</button>
                            <button class="bt-silver btn-modal btn-blue" name="pending_reply_to_customer_button" value="0" onclick="this.value=1">Reply to Customer</button>
                            </div>
                        </div>
                    
                </div>
            </div>

		<?php endif;?>
 	<?php endforeach ?>
<?php endif;?> 
<!-- pending log modal boxes completed here -->