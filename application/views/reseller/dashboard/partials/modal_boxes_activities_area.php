<!-- Now making the modal boxes for activites tab. -->
<?php if (count($vd->activities)): $cnt=0;?>  
	<?php foreach ($vd->activities as $pr): $cnt++;?>
		<?php if (substr(trim($pr['caption']),0,11) == "Rejected by") : ?>
				
                <div id="activity_detail<?php echo $cnt;?>" class="modal hide fade modal-autoheight" 
                	tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        	<i class="icon-remove"></i></button>
                            
						<h3 id="modalLabel"><?= $pr['caption'] ?></h3>
                        
					</div>    
    
                    <div class="modal-body" style="max-height:300px !important;">
                        <ul>
                            
                            <li>
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p>
                                    <strong>Code: </strong> <?= $pr['code'] ?>
                                    </p>                    
                                </section><br />
								
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p>
                                    <strong>Rejection Date: </strong> <?= $pr['dt'] ?>
                                    </p>                    
                                </section><br />
                                
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p>
                                    <strong>Reason: </strong> <?= $vd->esc($pr['rejectionReason']) ?>
                                    </p>                    
                                </section>                
                            </li>             
                        </ul>
                    </div>
    
					<div class="modal-footer"></div>
				</div>
                
		<?php elseif ($pr['caption'] == "New Order Placed") : ?>
               
                <div id="activity_detail<?php echo $cnt;?>" class="modal hide fade modal-autoheight" 
                	tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    
                    <div class="modal-header">
                        
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        	<i class="icon-remove"></i></button>
                        
                        <h3 id="modalLabel">New Order Details</h3>
                    </div>    
                    
                    <div class="modal-body" style="max-height:300px !important;">
                        <ul>
                            
                            <li>
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Code: </strong> <?= $pr['code'] ?></p>                    
                                </section><br />
                                
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Customer Name: </strong> <?= $pr['custName'] ?></p>
                                </section><br />                                
                                
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Email: </strong> <?= $pr['custEmail'] ?></p>                    
                                </section><br />
                                
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Order Date: </strong> <?= $pr['dt'] ?></p>
                                </section>
                            </li>             
                        </ul>
                    </div>
                    
                    <div class="modal-footer"></div>
                </div>

		<?php elseif ($pr['caption'] == "Assigned to Writer") : ?>
               
                <div id="activity_detail<?php echo $cnt;?>" class="modal hide fade modal-autoheight" 
                	tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    
                    <div class="modal-header">
                    
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        	<i class="icon-remove"></i></button>
                            
                        <h3 id="modalLabel">Assigned to Writer</h3>                    
                    </div>    
                    
                    <div class="modal-body" style="max-height:300px !important;">
                        <ul>                            
                            <li>
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Code: </strong> <?= $pr['code'] ?></p>                    
                                </section><br />
                                
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Assigned to: </strong> <?= $pr['writerName'] ?></p>                                </section><br />
                                    
                                <section class="mb-customer-panel" style="padding:10px !important;">					
                                    <p><strong>Assigned On: </strong> <?= $pr['dt'] ?></p>                    
                                </section>
                                
                            </li>             
                        </ul>
                    </div>
                    
                    <div class="modal-footer"></div>
                </div>


			<?php endif;?>
	 <?php endforeach ?>
<?php endif;?> 