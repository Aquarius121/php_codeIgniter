<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">										
					<h1>Step 1 - Company Information</h1>
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
			<form class="tab-content required-form" method="post" action="writing/prdetails/step2" 
            		id="prdetails1-form">				
				
				<div class="row-fluid">
					<div class="span8 information-panel">						
						<section>                        	
							<h2>Your Contact Details</h2>
							<div class="row-fluid">
								<div class="span12 muted marbot">
									This information will be used to contact you about your order. 
									It will not be displayed on the press release.
									Please ensure the email address is correct.
								</div>
							</div>
							<div class="row-fluid">
								<div class="span6">
									<input class="in-text span12 required" type="text" name="customer_name" 
										id="customer_name" placeholder="Customer Name" required
										value="<?= $vd->esc($vd->m_order_code->customer_name) ?>" />                                   
								</div>
								<div class="span6">
									<input class="in-text span12 required" type="text" name="customer_email" 
										id="customer_email" placeholder="Customer Email" required
										value="<?= $vd->esc($vd->m_order_code->customer_email) ?>" />                                   
								</div>
							</div>
						</section>
                  <section class="form-section basic-information">                        	
							<ul>
								
                                <h2>Basic Information</h2>
                                <li>
                                	<div class="row-fluid">
										<div class="span12">
                                            <input class="in-text span12 required" type="text" name="companyname" 
                                            id="companyname" placeholder="Company Name"
                                            value="<?= $vd->esc($vd->fields['companyname']) ?>" data-required-name="Company Name" />
                                        </div>
                                    </div>
                                    <div class="row-fluid">
										<div class="span6">
                              				<input class="in-text span12" type="text" name="companycontact" 
                                            id="companycontact" placeholder="Company Contact Name"
                                            value="<?= $vd->esc($vd->fields['companycontact']) ?>" />                                   
                                        </div>
                                   
                                    
										<div class="span6">
                                        	<input class="in-text span12 required" type="url" name="companyweb" 
                                            id="companyweb" placeholder="Company Website"
                                            value="<?= $vd->esc($vd->fields['companyweb']) ?>" data-required-name="Company Website" />
                                        </div>
                                    </div>   
                                            
                                    <div class="row-fluid">
                                    	<div class="span12">
                                    		<input class="in-text span12 required" type="email" name="emailaddress" 
			                                    id="emailaddress" placeholder="Company Contact Email Address"
			                                    value="<?= $vd->esc($vd->fields['emailaddress']) ?>" 
			                                    data-required-name="Email Address" />
                                    	</div>	
                                    </div>                           
                                                                              
                                </li>
                              </ul>
                           </section>   
                           <section class="form-section company-address">
							<h2>Company Address</h2>
							<ul>
								<li>
									<div class="row-fluid">
										<div class="span8">
											<input class="in-text span12" name="address_street" 
											placeholder="Street Address" type="text"
											value="<?= $vd->esc($vd->fields['address_street']) ?>" />
										</div>
										<div class="span4">
											<input class="in-text span12"  name="address_apt_suite"
												type="text" placeholder="Apt / Suite" 
												value="<?= $vd->esc($vd->fields['address_apt_suite']) ?>" />
										</div>
									</div>
								</li>
								<li>
									<div class="row-fluid">
										<div class="span4">
											<input class="in-text span12" type="text" 
												name="address_city" placeholder="City"
												value="<?= $vd->esc($vd->fields['address_city']) ?>" />
										</div>
										<div class="span4">
											<input class="in-text span12" type="text" 
												name="address_state" placeholder="State / Region"
												value="<?= $vd->esc($vd->fields['address_state']) ?>" />
										</div>
										<div class="span4">
											<input class="in-text span12" type="text" 
												name="address_zip" placeholder="Zip Code"
												value="<?= $vd->esc($vd->fields['address_zip']) ?>" />
										</div>
									</div>
								</li>
								<li id="select-country">
									<div class="row-fluid">
										<div class="span6" id="select-country">
											<select class="show-menu-arrow span12" name="address_country_id" data-required-name="Country">
												<option class="selectpicker-default" title="Select Country" value=""
													<?= value_if_test(!$vd->fields['address_country_id'], 'selected') ?>>Select Country</option>
												<?php foreach ($vd->countries as $country): ?>
												<option value="<?= $country->id ?>"
													<?= value_if_test(($vd->fields['address_country_id'] == $country->id), 'selected') ?>>
													<?= $vd->esc($country->name) ?>
												</option>
												<?php endforeach ?>
											</select>
											<script>

											$(function() {
												
												$("#select-country select")
													.selectpicker({ size: 10 })
													.addClass("required");
												
											});
											
											</script>
										</div>
										<div class="span6">
											<input class="in-text span12" type="text" 
												name="address_phone" placeholder="Phone Number"
												value="<?= $vd->esc($vd->fields['address_phone']) ?>" />
										</div>
									</div>
								</li>
							</ul>
						</section>
                        <section>
                        <ul>
                            <li>
									<h2>Your Company Details</h2>
                                    <div class="fieldsHint">
                                    	Write a brief summary of what your business is about.
                                    </div>
                                    <textarea class="in-text span12 required required-callback"  
                                    	id="companydetails" 
                                    	name="companydetails"
										placeholder="Your Company Details" 
                                        data-required-name="Company Details" 
                                        data-required-callback="comp-detail-min-words"
                                        ><?= $vd->esc($vd->fields['companydetails']) ?></textarea>		
                                    <ul>
                                    	<li>
                                            <div class="span9 help-block" id="companydetails_words">
                                                <span id="companydetails_wordscount"></span> Words
                                            </div>
                                             <div class="span3 help-block" id="companydetails_countdown_text">
                                                <span id="companydetails_countdown"></span> Characters Left
                                             </div>
                                        </li>
                                    </ul>            
									<script>
									
									$(function() {

										var companydetails = $("#companydetails");
										var companydetails_wordscount = $("#companydetails_wordscount");
										var min_word_count = 20;

										companydetails.limit_length(400,
											$("#companydetails_countdown_text"),
											$("#companydetails_countdown"));

										var count_words = function(value) {
											var pattern = /([a-z0-9]+([^\s]*[\s]+[^a-z0-9]*|$))/ig;
											var match = value.match(pattern);
											var count = match ? match.length : 0;
											return count;
										};

										var show_word_count = function() {
											var count = count_words(companydetails.val());
											companydetails_wordscount.html(count);
										};

										companydetails.keyup(show_word_count);
										show_word_count();

										required_js.add_callback("comp-detail-min-words", function(value) {
											var response = { valid: false, text: "must have at least " 
												+ min_word_count 
												+ " words" };
											var count = count_words(value);
											response.valid = count >= min_word_count;
											return response;
										});

									});
									</script>							
								</li>
                                
                                <li>
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
							<div class="marbot-20">
								Please fill out the form which is required to send out your press release. 
								Make sure you give us as much detail about your press release as you can.
							</div>

							<div class="marbot-20">
								This will save time in the long run since our writers will be able to better 
								cater to your exact specifications and needs. 
							</div>

							<div class="marbot-20">
								(NOTE: This info will ONLY be 
								used for the press release distribution and we will never use or share this 
								information for anything other than your PR submission.) 
							</div>

						</div>
					<?php endif ?>
					</aside>
					<script>
					
					$(function() {
						
						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);
						
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
