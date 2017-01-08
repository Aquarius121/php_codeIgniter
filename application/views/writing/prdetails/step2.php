<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">					
					<h1>Step 2 - Press Release Details</h1>
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
			<form class="tab-content required-form" method="post" action="writing/prdetails/step3"
					 id="prdetails1-form">				
				<div class="row-fluid">
					<div class="span8 information-panel">						
						<section class="form-section basic-information">
							<ul>
								<li class="select-right select-beat" id="select-category">
												<select class="show-menu-arrow span12 category" name="category"
												 data-required-name="category"
												 data-live-search="true">
										<option class="selectpicker-default" title="Select Category" value=""
											<?= value_if_test(!$vd->fields['beat'], 'selected') ?>>Select Category</option>
													 <?php foreach ($vd->beats as $group): ?>
													 <optgroup label="<?= $vd->esc($group->name) ?>">
														  <?php foreach ($group->beats as $beat): ?>
														  <option value="<?= $beat->id ?>"
																<?= value_if_test(($vd->fields['beat'] == $beat->id), 'selected') ?>>
																<?= $vd->esc($beat->name) ?>
														  </option>
														  <?php endforeach ?>
													 </optgroup>
													 <?php endforeach ?>
									</select>
									<script>
										$(function() {												
											$("#select-category select")
												.selectpicker({ size: 10 })
												.addClass("required");												
										});																				
									</script>
								</li>
								<li>
									<h2>PR Location</h2>
									<div class="fieldsHint">
										Where is the news coming from? This is usually the same as the company location. 
									</div>
									<div class="row-fluid">
										<input class="in-text span12" type="text" name="location" 
											value="<?= $vd->esc($vd->fields['location']) ?>" />
									</div>
								</li>
								<li>
									<h2>Desired Press Release "angle":</h2>
									<div class="fieldsHint">
										What type of press release would you like us to write.
										This is required info so if you don't have an idea then it's best
										to contact us so we can discuss some possibilites here. 
									</div>
									<div class="marbot-20 angle-radios">
										
										<label>
									  		<input type="radio" name="pr_angle" value="problem" id="pr_angle1"
											<?= value_if_test(($vd->fields['pr_angle']=="problem" || $vd->fields['pr_angle'] == ""), 'checked') ?>>
											Problem / Solution
											<div class="muted">Introduces a problem and presents the website or product as a solution</div>
										</label>
										
										<label>
											<input type="radio" name="pr_angle" value="discount" id="pr_angle2"
											<?= value_if_test(($vd->fields['pr_angle']=="discount"), 'checked') ?>>
											Discount Offer or Special Offer Announcement
										</label>
											
										<label>
											<input type="radio" name="pr_angle" value="website" id="pr_angle3"
											<?= value_if_test(($vd->fields['pr_angle']=="website"), 'checked') ?>>
											Website or product launch
										</label>
										
										<label>
											<input type="radio" name="pr_angle" value="announcement" id="pr_angle4"
											<?= value_if_test(($vd->fields['pr_angle']=="announcement"), 'checked') ?>>
											Special Company Announcement
											<div class="muted">Company Merge, Company Acquisition, Anniversary etc.</div>
										</label>
										
										<label>
											<input type="radio" name="pr_angle" value="other" id="pr_angle5"
											<?= value_if_test(($vd->fields['pr_angle']=="other"), 'checked') ?>>
											Other
										</label>
										
								  	</div>
																		
									<?php if($vd->fields['pr_angle']=="problem" || $vd->fields['pr_angle'] == "") : ?>
										 <div id="angleFieldCaption">
												<h2>Promotional Press Release Details</h2>
												<strong>What would you like to emphasize?</strong> <br />
												If there is anything specific you would like the writer to include please list it below. 
										 </div>
									<?php elseif($vd->fields['pr_angle']=="discount") : ?>	 
										 <div id="angleFieldCaption">
												<h2>Discount Offer Press Release Details</h2>
												<strong>Discount offer or coupon details?</strong> <br />
												Give us specific information about your product discount offer. If you have any specific promo codes or urls related to the offer please list it below.
										 </div>	
									<?php elseif($vd->fields['pr_angle']=="website") : ?>	 
										 <div id="angleFieldCaption">
												<h2>New Website or Product Launch Press Release Details</h2>
												<strong>New website or product details?</strong> <br />
												Give us any specific links to your new website and/or products.
										 </div>	
									<?php elseif($vd->fields['pr_angle']=="announcement") : ?>	 
										 <div id="angleFieldCaption">
												<h2>Special Company Announcement Details</h2>
												<strong>Company announcement details</strong> <br />
												Give us the specifics for your company announcement.
										 </div>
									<?php elseif($vd->fields['pr_angle']=="other") : ?>	 
										 <div id="angleFieldCaption">
												<h2>Other Angle Details</h2>
												Give us specific details about what you want written.
										 </div>
									<?php endif;?>
								</li>
										  
										 <li>
									<textarea class="in-text span12 required required-callback" 
													id="angledetails" name="angledetails"
										data-required-name="Angle Details"
													 data-required-callback="angle-detail-min-words"
													 ><?= $vd->esc($vd->fields['angledetails']) ?></textarea>	
										<script>
										$(function() {
											var angledetails = $("#angledetails");
											var angledetails_wordscount = $("#angledetails_wordscount");
											var min_word_count = 20;
	
											angledetails.limit_length(400,
												$("#angledetails_countdown_text"),
												$("#angledetails_countdown"));
	
											var count_words = function(value) {
												var pattern = /([a-z0-9]\S*(\s+[^a-z0-9]*|$))/ig;
												var match = value.match(pattern);
												var count = match ? match.length : 0;
												return count;
											};
	
											var show_word_count = function() {
												var count = count_words(angledetails.val());
												angledetails_wordscount.html(count);
											};
	
											angledetails.keyup(show_word_count);
											show_word_count();
	
											required_js.add_callback("angle-detail-min-words", function(value) {
												var response = { valid: false, text: "must have at least " + min_word_count
													 + " words" };
												var count = count_words(value);
												response.valid = count >= min_word_count;
												return response;
											});
	
										});
									</script>	 	
												
									<ul>
													<li>
														  <div class="span9 help-block" id="angledetails_words">
																<span id="angledetails_wordscount">
													<?php echo str_word_count($vd->esc($vd->fields['angledetails'])); ?>
																</span> Words
														  </div>
															<div class="span3 help-block" id="angledetails_countdown_text">
																<span id="angledetails_countdown">
													<?php echo 400-strlen($vd->esc($vd->fields['angledetails']));?>
												</span> Characters Left
															</div>
													 </li>													 
												</ul>	 
								</li>
										  <li>
									<div class="span6">
													 <button type="button" name="backfromStep2" id="backfromStep2" value="1" 
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
							Please fill in all details below. In the "other comments" 
							please enter in any other details you feel necessary to write 
							your press release to your specifications. The more info the better.									  						
						</div>
					<?php endif ?>
					</aside>
					<script>					
					$(function() {						
						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);						
					
						$("#backfromStep2").click(function() {
							location.href="writing/prdetails/step1";
						});		
						$("#pr_angle1").click(function() {												
							text="<h2>Promotional Press Release Details</h2><strong>What would you like to emphasize?</strong> <br />If there is anything specific you would like the writer to include please list it below. ";
							$("#angleFieldCaption").html( text );						
						});	
						$("#pr_angle2").click(function() {
							text="<h2>Discount Offer Press Release Details </h2><strong>Discount offer or coupon details?</strong><br />Give us specific information about your product discount offer. If you have any specific promo codes or urls related to the offer please list it below. ";
							$("#angleFieldCaption").html( text );						
						});		
						$("#pr_angle3").click(function() {
							text="<h2>New Website or Product Launch Press Release Details  </h2><strong>New website or product details?</strong><br /> Give us any specific links to your new website and/or products.";
							$("#angleFieldCaption").html( text );
						});	
						$("#pr_angle4").click(function() {
							text="<h2> Special Company Announcement Details  </h2><strong>Company announcement details</strong><br /> Give us the specifics for your company announcement.";
							$("#angleFieldCaption").html( text );
						});	
						$("#pr_angle5").click(function() {
							text="<h2>Other Angle Details</h2>Give us specific details about what you want written.";
							$("#angleFieldCaption").html( text );
						});
					});		
					</script>
						  
				</div>
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