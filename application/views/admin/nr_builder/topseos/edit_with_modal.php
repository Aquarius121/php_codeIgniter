<div id="feedback_area"></div>
<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post" id="company_edit_form">
	<div class="span12">		
		<div class="content content-no-tabs">
			
			<div class="span12">
				
				<section class="form-section user-details">
					<div class="row-fluid">
						<div class="span4 relative">							
							<input type="text" required name="name"
								class="span12 in-text has-placeholder"
								value="<?= $vd->comp->name ?>"
								placeholder="Company Name" />
							<strong class="placeholder">Company Name</strong>
						</div>

						<div class="span4 relative">
							<input type="email" name="email" id="email"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->email ?>"
								placeholder="Email Address" />
							<strong class="placeholder">Email Address</strong>
						</div>

						<div class="span4 relative">
							<input type="url" name="website" id="website"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->website ?>"
								placeholder="Website URL" />
							<strong class="placeholder">Website URL</strong>
						</div>
					</div>

					<div class="relative">
						<input type="url" name="logo_image_path" id="logo_image_path"
							class="span6 in-text has-placeholder" 
							value="<?= $vd->c_data->logo_image_path ?>"
							placeholder="Logo Image Path" />
						<strong class="placeholder">Logo Image Path</strong>
						<a href="<?= $vd->c_data->logo_image_path ?>" target="_blank" 
								id="logo_link">
							<img src="<?= $vd->c_data->logo_image_path ?>" 
								style="height:40px;" id="logo_preview" 
								alt="<?= value_if_test($vd->c_data->logo_image_path, 
									'Logo', 'No Logo') ?>">
						</a>
						<script>
						$(function(){
							var logo_image_path = $("#logo_image_path");
							logo_image_path.on('change', function() {
								if (logo_image_path.val() != "")
								{
									$("#logo_preview").attr('src', logo_image_path.val());
									$("#logo_preview").attr('alt', 'Logo');
									$("#logo_link").attr('href', logo_image_path.val());
								}
								else
								{
									$("#logo_preview").attr('src', '');
									$("#logo_link").attr('href', '');
									$("#logo_preview").attr('alt', 'No Logo');
								}
							});
						});
						
							
						</script>
					</div>

					<h4>Company Address</h4>
					
					<div class="row-fluid">
						<div class="span4 relative">
							<input type="text" name="address" id="address"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->address ?>"
								placeholder="Address" />
							<strong class="placeholder">Address</strong>
						</div>

					
						<div class="span4 relative">
							<input type="text" name="city"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->city ?>"
								placeholder="City" />
							<strong class="placeholder">City</strong>
						</div>

						<div class="span4 relative">
							<input type="text" name="state"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->state ?>"
								placeholder="State" />
							<strong class="placeholder">State</strong>
						</div>						
					</div>

					<div class="row-fluid">
						<div class="span4 relative">
							<input type="text" name="zip"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->zip ?>"
								placeholder="Zip" />
							<strong class="placeholder">Zip</strong>
						</div>

						<div class="span4 relative" id="select-country">
							<select class="show-menu-arrow span12 has-placeholder" name="country_id">
								<option class="selectpicker-default" title="Select Country" value=""
									<?= value_if_test(!@$vd->c_data->country_id, 'selected') ?>
									>None</option>
								<?php foreach ($vd->countries as $country): ?>
								<option value="<?= $country->id ?>"
									<?= value_if_test((@$vd->c_data->country_id == $country->id && 
										!$country->is_common), 'selected') ?>>
									<?= $vd->esc($country->name) ?>
								</option>
								<?php endforeach ?>
							</select>
							<strong class="placeholder">Country</strong>
							<script>
								
							$(function() {
								
								$("#select-country select")
									.on_load_select({ size: 10 });
								
							});
							
							</script>
							
						</div>	
						<div class="span4 relative">
							<input type="text" name="phone" id="phone"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->phone ?>"
								placeholder="Phone" />
							<strong class="placeholder">Phone</strong>
						</div>	
					</div>

					

					<h4>Social Accounts</h4>

					<div class="row-fluid">
						<div class="span4 relative">
							<input type="text" name="soc_fb" id="soc_fb"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_fb ?>"
								placeholder="Facebook Username or Page" />
							<strong class="placeholder">Facebook Username or Page</strong>
						</div>

						<div class="span4 relative">
							<input type="text" name="soc_twitter" id="soc_twitter"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_twitter ?>"
								placeholder="Twitter Username" />
							<strong class="placeholder">Twitter Username</strong>
						</div>

						<div class="span4 relative">
							<input type="text" name="soc_gplus" id="soc_gplus"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_gplus ?>"
								placeholder="Google Plus ID" />
							<strong class="placeholder">Google Plus ID</strong>
						</div>

					</div>

					<div class="row-fluid">
						<div class="span4 relative">
							<input type="text" name="soc_youtube" id="soc_youtube"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_youtube ?>"
								placeholder="Youtube Username" />
							<strong class="placeholder">Youtube Username</strong>
						</div>

						<div class="span4 relative">
							<input type="text" name="soc_linkedin" id="soc_linkedin"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_linkedin ?>"
								placeholder="Linkedin Profile" />
							<strong class="placeholder">Linkedin Profile</strong>
						</div>

						<div class="span4 relative">
							<input type="text" name="soc_pinterest" id="soc_pinterest"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_pinterest ?>"
								placeholder="Pinterest Profile" />
							<strong class="placeholder">Pinterest Profile</strong>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="blog_url" id="blog_url"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->blog_url ?>"
								placeholder="Blog URL" />
							<strong class="placeholder">Blog URL</strong>
						</div>

						<div class="span6 relative">
							<input type="text" name="blog_rss" id="blog_rss"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->blog_rss ?>"
								placeholder="Blog RSS URL" />
							<strong class="placeholder">Blog RSS URL</strong>
						</div>
					</div>					

					<div class="relative">
						<textarea name="short_description" id="short_description"
							class="span8 in-text has-placeholder user-notes" 
							placeholder="Company Summary"><?= 
								$vd->c_data->short_description ?></textarea>
						<strong class="placeholder">Company Summary</strong>
					</div>
					
					<strong>About Company</strong>
					<div class="relative cke-container">
						<textarea name="about_company" id="about_company"
							class="span8 in-text has-placeholder user-notes" 
							placeholder="About Company"><?= 
								$vd->esc($vd->c_data->about_company) ?></textarea>
					</div>

					<script>
						/*$(function(){
							window.init_editor($("#about_company"), { height: 400 }, 
							function() {
									
							});
						});
						*/
					</script>

					<div class="row-fluid">
						<div class="span2">
							<button type="submit" name="save" value="1"
										class="span12 bt-orange pull-right">Save</button>
						</div>
					</div>
					


				</section>
			</div>
			<script>
			$(function(){

				$("#company_edit_form").submit(function() {

					var url = "admin/nr_builder/topseos/edit_save/<?= $vd->comp->id ?>";
					$.ajax({
							type: "POST",
							url: url,
							data: $("#company_edit_form").serialize(), 
							success: function(data)
							{
								location.hash = "#feedback_area";
								load_feedback(data);
								check_social_fields(data);
								update_parent_row(data);
							}
						});

					return false; // avoid to execute the actual submit of the form.
				});

				update_parent_row = function(data){
					var comp_id = "<?= $vd->comp->id ?>";
					var website_td = $("#td_website_"+comp_id);
					var website = $("#website").val();
					var email_td = $("#td_email_"+comp_id);
					var email = $("#email").val();
					var logo_td = $("#td_logo_"+comp_id);
					var logo = $("#logo_image_path").val();

					var about_td = $("#td_about_"+comp_id);
					var short_description = $("#short_description").val();
					var about_company = $("#about_company").val();
					var address_td = $("#td_address_"+comp_id);
					var address = $("#address").val();
					var phone_td = $("#td_phone_"+comp_id);
					var phone = $("#phone").val();

					var soc_fb_td = $("#td_soc_fb_"+comp_id);
					var soc_fb = $("#soc_fb").val();
					var soc_twitter_td = $("#td_soc_twitter_"+comp_id);
					var soc_twitter = $("#soc_twitter").val();
					var soc_linkedin_td = $("#td_soc_linkedin_"+comp_id);
					var soc_linkedin = $("#soc_linkedin").val();
					var soc_gplus_td = $("#td_soc_gplus_"+comp_id);
					var soc_gplus = $("#soc_gplus").val();
					var soc_youtube_td = $("#td_soc_youtube_"+comp_id);
					var soc_youtube = $("#soc_youtube").val();
					var soc_pinterest_td = $("#td_soc_pinterest_"+comp_id);
					var soc_pinterest = $("#soc_pinterest").val();
					var blog_url_td = $("#td_blog_url_"+comp_id);
					var blog_url = $("#blog_url").val();
					var blog_rss_td = $("#td_blog_rss_"+comp_id);
					var blog_rss = $("#blog_rss").val();
					var li_build_nr = $("#li_build_nr_"+comp_id);
					var td_checkbox = $("#td_checkbox_"+comp_id);
					
					var soc_counter = 0;
					var html = "";

					if (website != "")
					{
						website_td.removeClass("fail");
						website_td.addClass("success");
						html = "<a title='" + website + "' href='" + website;
						html += "' target='_blank' class='tl'><i class='icon-ok'></i></a>";
					}
					else
					{
						website_td.removeClass("success");
						website_td.addClass("fail");
						html = "<a title='No URL found' class='tl'><i class='icon-remove'></i></a>";
					}					
					website_td.html(html);

					if (email != "")
					{
						email_td.removeClass("fail");
						email_td.addClass("success");
						html = "<a title='"+email+"' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						email_td.removeClass("success");
						email_td.addClass("fail");
						html = "<a title='No email found' class='tl'><i class='icon-remove'></i></a>";
					}
					
					email_td.html(html);

					if (logo != "")
					{
						logo_td.removeClass("fail");
						logo_td.addClass("success");
						html = "<a title='" + logo + "' href='" + logo;
						html += "' target='_blank' class='tl'><i class='icon-ok'></i></a>";
					}
					else
					{
						logo_td.removeClass("success");
						logo_td.addClass("fail");
						html = "<a title='No logo found' class='tl'><i class='icon-remove'></i></a>";
					}
					
					logo_td.html(html);

					if (short_description != "" || about_company != "")
					{
						if (short_description == "")
							short_description = about_company;
						about_td.removeClass("fail");
						about_td.addClass("success");
						html = "<a title='" + short_description + "'";
						html += " class='tl'><i class='icon-ok'></i></a>";
					}
					else
					{
						about_td.removeClass("success");
						about_td.addClass("fail");
						html = "<a title='No description found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					about_td.html(html);

					if (address != "")
					{
						address_td.removeClass("fail");
						address_td.addClass("success");
						html = "<a title='"+address+"' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						address_td.removeClass("success");
						address_td.addClass("fail");
						html = "<a title='No address found' class='tl'><i class='icon-remove'></i></a>";
					}
					
					address_td.html(html);

					if (phone != "")
					{
						phone_td.removeClass("fail");
						phone_td.addClass("success");
						html = "<a title='"+phone+"' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						phone_td.removeClass("success");
						phone_td.addClass("fail");
						html = "<a title='No phone found' class='tl'><i class='icon-remove'></i></a>";
					}
					
					phone_td.html(html);

					if (soc_fb != "")
					{
						soc_counter++;
						soc_fb_td.removeClass("fail");
						soc_fb_td.addClass("success");
						html = "<a title='"+soc_fb+"' href='http://facebook.com/" + soc_fb;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_fb_td.removeClass("success");
						soc_fb_td.addClass("fail");
						html = "<a title='Facebook not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_fb_td.html(html);

					if (soc_twitter != "")
					{
						soc_counter++;
						soc_twitter_td.removeClass("fail");
						soc_twitter_td.addClass("success");
						html = "<a title='"+soc_twitter+"' href='http://twitter.com/" + soc_twitter;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_twitter_td.removeClass("success");
						soc_twitter_td.addClass("fail");
						html = "<a title='Twitter not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_twitter_td.html(html);

					if (soc_linkedin != "")
					{
						soc_linkedin_td.removeClass("fail");
						soc_linkedin_td.addClass("success");
						html = "<a title='"+soc_linkedin+"' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_linkedin_td.removeClass("success");
						soc_linkedin_td.addClass("fail");
						html = "<a title='Linkedin not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_linkedin_td.html(html);

					if (soc_gplus != "")
					{
						soc_counter++;
						soc_gplus_td.removeClass("fail");
						soc_gplus_td.addClass("success");
						html = "<a title='"+soc_gplus+"' href='http://plus.google.com/" + soc_gplus;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_gplus_td.removeClass("success");
						soc_gplus_td.addClass("fail");
						html = "<a title='Google+ not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_gplus_td.html(html);

					if (soc_youtube != "")
					{
						soc_counter++;
						soc_youtube_td.removeClass("fail");
						soc_youtube_td.addClass("success");
						html = "<a title='"+soc_youtube+"' href='http://youtube.com/" + soc_youtube;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_youtube_td.removeClass("success");
						soc_youtube_td.addClass("fail");
						html = "<a title='Youtube not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_youtube_td.html(html);

					if (soc_pinterest != "")
					{
						soc_counter++;
						soc_pinterest_td.removeClass("fail");
						soc_pinterest_td.addClass("success");
						html = "<a title='"+soc_pinterest+"' href='http://pinterest.com/"+soc_pinterest;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						soc_pinterest_td.removeClass("success");
						soc_pinterest_td.addClass("fail");
						html = "<a title='Pinterest not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					soc_pinterest_td.html(html);

					if (blog_url != "")
					{
						blog_url_td.removeClass("fail");
						blog_url_td.addClass("success");
						html = "<a title='"+blog_url+"' href='"+blog_url;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						blog_url_td.removeClass("success");
						blog_url_td.addClass("fail");
						html = "<a title='Blog URL not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					blog_url_td.html(html);

					if (blog_rss != "")
					{
						blog_rss_td.removeClass("fail");
						blog_rss_td.addClass("success");
						html = "<a title='"+blog_rss+"' href='"+blog_rss;
						html += "' target='_blank' class='tl'>";
						html += "<i class='icon-ok'></i></a>";
					}
					else
					{
						blog_rss_td.removeClass("success");
						blog_rss_td.addClass("fail");
						html = "<a title='Blog RSS URL not found' class='tl'>";
						html += "<i class='icon-remove'></i></a>";
					}
					
					blog_rss_td.html(html);

					if (website && email && logo_image_path && soc_counter >= 2)
					{
						html = "<a href='admin/nr_builder/topseos/build/"+comp_id+"'>Build NR</a>";
						li_build_nr.html(html);
						html = "<label class='checkbox-container inline'>";
						html +="<input type='checkbox' class='selectable' ";
						html += "name='selected[" + comp_id + "]' ";
						html += "val" + "ue='" + comp_id + "' />";
						html += "<span class='checkbox'></span></label>";
						td_checkbox.html(html);
					}
					else
					{
						li_build_nr.html('');
						td_checkbox.html('');
					}


				}
				
				check_social_fields = function(data){
					if (data['response']['invalid_socials'].length == 0)
						return;					
					
					for (c=0; c<data['response']['invalid_socials'].length; c++)
					{
						if (data['response']['invalid_socials'][c] == 'soc_fb')
							$("#soc_fb").val('');

						if (data['response']['invalid_socials'][c] == 'soc_twitter')
							$("#soc_twitter").val('');

						if (data['response']['invalid_socials'][c] == 'soc_gplus')
							$("#soc_gplus").val('');

						if (data['response']['invalid_socials'][c] == 'soc_youtube')
							$("#soc_youtube").val('');

						if (data['response']['invalid_socials'][c] == 'soc_pinterest')
							$("#soc_pinterest").val('');

						if (data['response']['invalid_socials'][c] == 'blog_rss')
							$("#blog_rss").val('');
					}
					
						
				}

				load_feedback = function(data){
					msg = "<div class='feedback'>";
					if (data['response']['status'])
					{
						msg += "<div class='alert alert-success'>";
						msg += "<strong>Saved! </strong>Saved successfully</div>";
					}
						
					else
					{
						msg += "<div class='alert alert-error'>";
						msg += "<strong>Error! </strong>Save failed</div>";
					}

					if (data['response']['warning_msg'])
					{
						msg += "<div class='alert alert-warning'>";
						msg += "<strong>Warning(s)! </strong>";
						msg += data['response']['warning_msg'] + "</div>";
					}

					msg += "</div>";
					
					$('#feedback_area').html(msg);
					location.hash = "#feedback_area";
					
				}
			});
			
			</script>
			
		</div>
	</div>
</form>

<?php 

	$render_basic = $ci->is_development();

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datepicker.css');	
	echo $loader->render($render_basic);

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-datepicker.js');
	$loader->add('lib/bootbox.min.js');
	$loader->add('js/required.js');
	echo $loader->render($render_basic);

?>