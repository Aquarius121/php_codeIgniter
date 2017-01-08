<?php if(count($vd->pr->hist)>0) : ?>
    <ul>
        <?php foreach ($vd->pr->hist as $h): ?>
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
                <?php if (!empty($h->comments)): ?>
                    <section class="mb-editor-panel">
                        <p class="mb-author mb-editor">
                            Customer Replied 
                            <span class="mb-date">On
                                <?php  $p_date = Date::out($h->process_date); ?>
                                <?= $p_date->format('m/j'); ?>
                            </span>
                        </p>
                        <p><?= nl2br($vd->esc($h->comments)) ?></p>
                    </section>
                <?php else: ?>
                    <p class="mb-author mb-editor">
                        Customer Revised Details on
                        <?php  $p_date = Date::out($h->process_date); ?>
                        <?= $p_date->format('m/j'); ?>
                    </p>
                    <p><?= nl2br($vd->esc($h->comments)) ?></p>
                <?php endif ?>                    
                </li>
            <?php endif;?>
        <?php endforeach ?>
    </ul>
<?php endif;?>
