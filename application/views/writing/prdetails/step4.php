<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">					
					<h1>Step 4 - Review &amp; Submit</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
					</div>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<form class="tab-content required-form" method="post" action="writing/prdetails/save" id="prdetails1-form">				
				
				<div class="row-fluid">
					<div class="span8 information-panel">
                    			<section>	
                                    <h2>Company Information (<a href="writing/prdetails/step1">edit</a>)</h2>
                                    <table class="grid">
										<tbody>
											<tr>
												<td>Your Email Address</td>
												<td width="250"><?= $vd->esc($vd->fields['emailaddress']) ?></td>
											</tr>											
											<tr>
												<td>Company Name</td>
												<td><?= $vd->esc($vd->fields['companyname']) ?></td>
											</tr>
											<tr>
												<td>Company Contact Name</td>
												<td><?= $vd->esc($vd->fields['companycontact']);  ?></td>
											</tr>
											<tr>
												<td>Company Address</td>
												<td>
													<?= $vd->esc($vd->fields['companyname']);  ?><br />
                                                    <?php if(trim($vd->esc($vd->fields['address_apt_suite']))!=""): ?>
                                                    	<?= $vd->esc($vd->fields['address_apt_suite']);  ?> 
                                                    <?php endif ?> 
													<?php if(trim($vd->esc($vd->fields['address_street']))!=""): ?>
                                                    	<?= $vd->esc($vd->fields['address_street']);  ?><br />
                                                    <?php endif ?>
                                                    <?php if(trim($vd->esc($vd->fields['address_city']))!=""): ?>
                                                    	<?= $vd->esc($vd->fields['address_city']);  ?><br />
                                                    <?php endif ?>
                                                    <?php if(trim($vd->esc($vd->fields['address_state']))!=""): ?>
                                                    	<?= $vd->esc($vd->fields['address_state']);  ?> 
                                                    <?php endif ?> 
                                                    <?php if(trim($vd->esc($vd->fields['address_zip']))!=""): ?>
                                                    	<?= $vd->esc($vd->fields['address_zip']);  ?><br />
                                                    <?php endif ?>    
                                                    <?= $vd->countryName;  ?>
                                                   
                                                </td>
											</tr>
                                            <tr>
												<td>Phone</td>
												<td><?= $vd->esc ($vd->fields['address_phone']) ?></td>
											</tr>
                                              <tr>
												<td>Website</td>
												<td><?= $vd->esc ($vd->fields['companyweb']) ?></td>
											</tr>
											<tr>
												<td>Company Details</td>
												<td><?= nl2br($vd->esc ($vd->fields['companydetails'])) ?></td>
											</tr>
										</tbody>
									</table>						
								</section>
                                
                                <section>	
                                    <h2>Press Release Details (<a href="writing/prdetails/step2">edit</a>)</h2>
                                    <table class="grid">
										<tbody>
											<tr>
												<td>Category</td>
												<td width="250"><?= $vd->esc($vd->catName) ?></td>
											</tr>	
											<tr>
												<td>Location</td>
												<td width="250"><?= $vd->esc($vd->fields['location']) ?></td>
											</tr>
											<tr>
												<td>Angle</td>
												<td><?= $vd->esc($vd->angleTitle) ?></td>
											</tr>
											<tr>
												<td>Angle Details</td>
												<td><?= nl2br($vd->esc ($vd->fields['angledetails'])) ?></td>
											</tr>											
										</tbody>
									</table>						
								</section>
                                <section>	
                                    <h2>Additional Details (<a href="writing/prdetails/step3">edit</a>)</h2>
                                    <table class="grid">
										<tbody>
											<tr>
												<td>Keyword</td>
												<td width="250"><?= $vd->esc($vd->fields['primarykeyword']) ?></td>
											</tr>											
											<tr>
												<td>Tags</td>
												<td><?= $vd->esc($vd->fields['tags']) ?></td>
											</tr>											
                                            <tr>
												<td>Link in the Body of Press Release</td>
												<td>
                                                	<?php if(trim($vd->esc($vd->fields['link_1']))=="") : ?>
                                                    	(None added)
                                                    <?php endif ?>
													<?php if(trim($vd->esc($vd->fields['link_1']))!="") : ?>
                                                    <a href="<?= $vd->esc ($vd->fields['link_1']) ?>" target="_blank">
                                                    	<?= $vd->esc($vd->fields['link_text_1']) ?>
                                                    </a><br />
                                                    <?php endif ?> 
                                                                                               
                                                </td>
											</tr>
                                            <tr>
												<td>Additional Link At End of Press Release</td>
												<td>
													<?php if(trim($vd->esc($vd->fields['additional_link_1']))=="") : ?>
                                                    	(None added)
                                                    <?php endif ?>
													<?php if(trim($vd->esc($vd->fields['additional_link_1']))!="") : ?>
                                                    <a href="<?= $vd->esc ($vd->fields['additional_link_1']) ?>" 
                                                    	target="_blank">
                                                    	<?= $vd->esc($vd->fields['additional_link_text_1']) ?>
                                                    </a><br />
                                                    <?php endif ?>
                                                </td>
											</tr>
                                            <tr>
												<td>Additional Comments</td>
												<td>
														<?php if(trim($vd->esc($vd->fields['additional_comments']))=="") : ?>
                                                    			(None added)
                                                    	<?php else: ?>
                                                        	<?= nl2br($vd->esc ($vd->fields['additional_comments'])) ?>
														<?php endif ?>
                                                </td>
											</tr>                                            
										</tbody>
									</table>		
                                   
                                    <? if($vd->fields['logo'] || $vd->fields['image1'] || $vd->fields['image2'] || 
											$vd->fields['image3'] || $vd->videoIframe) : ?>
                                    <h2>Press Release Media</h2>
                                    <?php endif ?>
                                    <?php if($vd->fields['logo']): ?>
                                    	<strong>Logo:</strong> <br /><img src='<?= $vd->fields['logo'] ?>' alt="logo" />  
                                        <br /><br />
                                    <?php endif; ?>
                                    <?php if($vd->fields['image1'] || $vd->fields['image2'] || $vd->fields['image3'] ): ?>
										<strong>Additional Images: </strong><br />
										<?php if($vd->fields['image1']): ?>
                                            <img src='<?= $vd->fields['image1'] ?>' alt="related image" />  
                                        <?php endif; ?>
                                        <?php if($vd->fields['image2']): ?>
                                            <img src='<?= $vd->fields['image2'] ?>' alt="related image" 
                                            	style="padding-left:20px;" />  
                                        <?php endif; ?>
                                        <?php if($vd->fields['image3']): ?>
                                            <img src='<?= $vd->fields['image3'] ?>' alt="related image" 
                                            	style="padding-left:20px;" />  
                                        <?php endif; ?>
                                    <?php endif;?>    
                                    <?php if($vd->videoIframe!="") : ?>
                                           <br />  <?= $vd->videoIframe ?> <br />
                                    <?php endif; ?> 
                                    
                                    <ul>
                                    	<li>
                                            <div class="span6">
                                                <button type="button" name="backfromStep4" value="1" id="backfromStep4" 
                                                            class="span11 bt-silver">Back</button><br /><br /><br />
                                            </div>
                                            <div class="span6">
                                                <button type="submit" value="1" 
                                                            class="span11 bt-silver">Submit</button><br /><br /><br />
                                            </div>
                                        </li>
                                    </ul>
										
								</section>
					</div>	
                    
                    					
					<aside class="span4 aside aside-fluid">
					<?php if ($vd->esc($vd->details_change_comments)): ?>
						<div class="aside-properties padding-top" id="locked_aside">
							<?= $ci->load->view('writing/prdetails/partials/reply-to-admin') ?>
						</div>
					<?php else: ?>
						<div class="aside-properties padding-top" id="locked_aside">
							Please fill out the form which is required to send out your press release. 
							Make sure you give us as much detail about your press release as you can. 
							This will save time in the long run since our writers will be able to better 
							cater to your exact specifications and needs. (NOTE: This info will ONLY be 
							used for the press release distribution and we will never use or share this 
							information for anything other than your PR submission.) 
                            						
						</div>
					<?php endif ?>
					</aside>
                    <script>
					
					$(function() {						
						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);						
					});
					$("#backfromStep4").click(function() {
						location.href="writing/prdetails/step3";
					});	
					
					</script>
                    
				</div>
			</form>
		</div>
	</div>
</div>