<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">					
					<h1>Step 3 - Additional Details</h1>
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
			<form class="tab-content required-form" method="post" action="writing/prdetails/step4" 
                id="prdetails1-form"  enctype="multipart/form-data">				
				
				<div class="row-fluid">
					<div class="span8 information-panel">
						
						<!--<h2>Transaction Code</h2>-->

						<section class="form-section basic-information">
							<ul>
							  <li>
                                	<h2>Keyword </h2>
                                    This Keyword Phrase will be used in the Headline of your press release. Enter the keyword which you would like the writer to optimize your release for. Choose this carefully since you will most likely get more targeted traffic based on this keyword phrase. 
                                    <input class="in-text span12 required" type="text" name="primarykeyword" 
										id="primarykeyword" placeholder="Primary Keyword"
										value="<?= $vd->esc($vd->fields['primarykeyword']) ?>"  data-required-name="Primary Keyword" />                                         
                               </li>
                               <li>
									<textarea class="in-text span12 required" id="tags" name="tags"
										placeholder="Enter at least 3 Tags" data-required-name="Tags"><?= $vd->esc($vd->fields['tags']) ?></textarea>
                                    	
								</li>
                                <li>
                                	<h2>Company Logo (optional) </h2>Upload your company logo you wish to include in your press release. <br /><input type="file" name="company_logo" id="company_logo" />
                                   	<span id="logo_finger_span">
									<?php if($vd->fields['logo']): ?>
		                               		<img src='<?= $vd->fields['logo'] ?>' alt="logo" />                                    		<a href="javascript: void(0)" id="remove_logo_link">Remove</a>
									<?php endif; ?>  
                                    </span>
                                </li>  
                                <li>
                                	<h2>Additional Images (optional) </h2>If you have additional images you would like to include in your release please include them below.  <br />
                                    <input type="file" name="image1" id="image1" />
									<span id="image1_finger_span">
										<?php if($vd->fields['image1']): ?>
                                            <img src='<?= $vd->fields['image1'] ?>' alt="related image" />    
                                            <a href="javascript: void(0)" id="remove_image1">Remove</a>
                                        <?php endif; ?>
                                    </span>    
                                    <br />
                                    <input type="file" name="image2" id="image2" />
                                    <span id="image2_finger_span">
										<?php if($vd->fields['image2']): ?>
                                            <img src='<?= $vd->fields['image2'] ?>' alt="related image" />   
                                            <a href="javascript: void(0)" id="remove_image2">Remove</a> 
                                        <?php endif; ?>
                                    </span>    
                                    <br />
                                    <input type="file" name="image3" id="image3" />
                                    <span id="image3_finger_span">
										<?php if($vd->fields['image3']): ?>
                                            <img src='<?= $vd->fields['image3'] ?>' alt="related image" />  
                                            <a href="javascript: void(0)" id="remove_image3">Remove</a>  
                                        <?php endif; ?>
                                    </span>    
									

                                </li>    
                                <li>
                                	<h2>Link in the Body of Press Release (optional)</h2>
                                	You may enter a link in the press release. 
                                	<br />

                                    <input type="url" class="in-text span6" name="link_1" 
										id="link_1" placeholder="URL"
										value="<?= $vd->esc($vd->fields['link_1']) ?>"  />
									<input type="text" class="in-text span6" name="link_text_1" 
										id="link_text_1" placeholder="Anchor text"
										value="<?= $vd->esc($vd->fields['link_text_1']) ?>"  /><br />
                                </li> 
                                <li>
                                	<h2>Additional Link At End of Press Release (optional)</h2>
                                	This is the link that is added at the end of your press release under "Additional Resources".  <br />

                                    <input type="url" class="in-text span6" name="additional_link_1" 
										id="additional_link_1" placeholder="URL"
										value="<?= $vd->esc($vd->fields['additional_link_1']) ?>"  />
									<input type="text" class="in-text span6" name="additional_link_text_1" 
										id="additional_link_text_1" placeholder="Anchor text"
										value="<?= $vd->esc($vd->fields['additional_link_text_1']) ?>"  /><br />
                                </li> 
                                 <li>
                                	<h2>Youtube Video (Optional) </h2>If you have a youtube video you 
                                		would like to embed in the release please enter URL below.
										(e,g http://www.youtube.com/watch?v=xUjHtm3vcw8&feature=g-vrec) . 
                                    <input class="in-text span12" type="text" name="youtube_video" 
										id="youtube_video" placeholder="URL"
										value="<?= $vd->esc($vd->fields['youtube_video']) ?>"  />                                         
                               </li>
                               <li>
									<textarea class="in-text span12" id="additional_comments" name="additional_comments" placeholder="Additional Comments"><?= $vd->esc($vd->fields['additional_comments']) ?></textarea>
                                    	
								</li>            
                                <li>
									<div class="span6">
                                        <button type="button" name="backfromStep3" value="1" id="backfromStep3" 
													class="span11 bt-silver">Back</button><br /><br /><br />
                                    </div>
                                    <div class="span6">
                                        <button type="submit" value="1" 
													class="span11 bt-silver">Continue</button><br /><br /><br />
                                    </div>
								</li>
							</ul>
						</section>
					</div>
                    <aside class="span4 aside aside-fluid">
                    <?php if ($vd->esc($vd->details_change_comments)): ?>
						<div class="aside-properties padding-top" id="locked_aside">
							<?= $ci->load->view('writing/prdetails/partials/editor-comments') ?>
						</div>
					<?php else: ?>
						<div class="aside-properties padding-top" id="locked_aside">
							Lorem ipsum dolor sit amet, consectetur adipisicing elit, 
							sed do eiusmod tempor incididunt ut labore et dolore magna 
							aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
							ullamco laboris nisi ut aliquip ex ea commodo consequat. 
							Duis aute irure dolor in reprehenderit in voluptate velit 
							esse cillum dolore eu fugiat nulla pariatur. Excepteur sint 
							occaecat cupidatat non proident, sunt in culpa qui officia 
							deserunt mollit anim id est laborum.
						</div>
					<?php endif ?>                            
					</aside>						
                    <script>
					$(function() {
						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);
						
						$("#backfromStep3").click(function() {
							location.href="writing/prdetails/step2";
						});
						$("#remove_logo_link").click(function() {
							$("#logo_finger_span").html("");
							$("#remove_logo").val("1");
						});	
						$("#remove_image1").click(function() {
							$("#image1_finger_span").html("");
							$("#remove_image1").val("1");
						});	
						$("#remove_image2").click(function() {
							$("#image2_finger_span").html("");
							$("#remove_image2").val("1");
						});	
						$("#remove_image3").click(function() {
							$("#image3_finger_span").html("");
							$("#remove_image3").val("1");
						});
					});
					</script>
				</div>
                <input type="hidden" name="remove_logo" id="remove_logo" value="0" />
                <input type="hidden" name="remove_image1" id="remove_image1" value="0" />
                <input type="hidden" name="remove_image2" id="remove_image2" value="0" />
                <input type="hidden" name="remove_image3" id="remove_image3" value="0" />
			</form>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>