<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Edit</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post">
	<div class="span12">		
		<div class="content content-no-tabs">
			
			<div class="span8 information-panel">
				
				<section class="form-section user-details">
					<div class="row-fluid">
						<div class="span12 relative">							
							<input type="text" required name="name"
								class="span12 in-text has-placeholder"
								value="<?= $vd->comp->name ?>"
								placeholder="Company Name" />
							<strong class="placeholder">Company Name</strong>
						</div>		
					</div>
					<div class="relative">
						<input type="email" name="email"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->email ?>"
							placeholder="Email Address" />
						<strong class="placeholder">Email Address</strong>
					</div>

					<div class="relative">
						<input type="url" name="website"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->website ?>"
							placeholder="Website URL" />
						<strong class="placeholder">Website URL</strong>
					</div>


					<div class="relative">
						<textarea name="short_description"
							class="span12 in-text has-placeholder user-notes" 
							placeholder="Additional Notes" /><?= 
								$vd->c_data->short_description ?></textarea>
						<strong class="placeholder">Company Summary</strong>
					</div>

					<strong>About Company</strong>
					<div class="relative marbot-20 cke-container">
						<textarea name="about_company" id="about_company"
							class="span12 in-text has-placeholder user-notes" 
							placeholder="Additional Notes" /><?= 
								$vd->esc($vd->c_data->about_company) ?></textarea>
					</div>

					<script>
						$(function(){
							window.init_editor($("#about_company"), { height: 400 }, function() {
									
							});
						});
					</script>

					<div class="relative">
						<input type="url" name="logo_image_path" id="logo_image_path"
							class="span8 in-text has-placeholder" 
							value="<?= $vd->c_data->logo_image_path ?>"
							placeholder="Logo Image Path" />
						<strong class="placeholder">Logo Image Path</strong>
						<a href="<?= $vd->c_data->logo_image_path ?>" target="_blank" id="logo_link">
							<img src="<?= $vd->c_data->logo_image_path ?>" style="height:40px;"
								id="logo_preview" alt="<?= value_if_test($vd->c_data->logo_image_path, 
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
							})
							
						</script>
					</div>

					<h2>Company Address</h2>
					
					<div class="relative">
						<input type="text" name="address"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->address ?>"
							placeholder="Address" />
						<strong class="placeholder">Address</strong>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="city"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->city ?>"
								placeholder="City" />
							<strong class="placeholder">City</strong>
						</div>

						<div class="span6 relative">
							<input type="text" name="state"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->state ?>"
								placeholder="State" />
							<strong class="placeholder">State</strong>
						</div>						
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="zip"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->zip ?>"
								placeholder="Zip" />
							<strong class="placeholder">Zip</strong>
						</div>

						<div class="span6 relative" id="select-country">
							<select class="show-menu-arrow span12 has-placeholder" name="country_id">
								<option class="selectpicker-default" title="Select Country" value=""
									<?= value_if_test(!@$vd->c_data->country_id, 'selected') ?>>None</option>
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
					</div>

					<div class="relative">
						<input type="text" name="phone"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->phone ?>"
							placeholder="Phone" />
						<strong class="placeholder">Phone</strong>
					</div>

					<h2>Social Accounts</h2>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="soc_fb"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_fb ?>"
								placeholder="Facebook Username or Page" />
							<strong class="placeholder">Facebook Username or Page</strong>
						</div>

						<div class="span6 relative">
							<input type="text" name="soc_twitter"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_twitter ?>"
								placeholder="Twitter Username" />
							<strong class="placeholder">Twitter Username</strong>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="soc_gplus"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_gplus ?>"
								placeholder="Google Plus ID" />
							<strong class="placeholder">Google Plus ID</strong>
						</div>

						<div class="span6 relative">
							<input type="text" name="soc_youtube"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_youtube ?>"
								placeholder="Youtube Username" />
							<strong class="placeholder">Youtube Username</strong>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span6 relative">
							<input type="text" name="soc_linkedin"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_linkedin ?>"
								placeholder="Linkedin Profile" />
							<strong class="placeholder">Linkedin Profile</strong>
						</div>

						<div class="span6 relative">
							<input type="text" name="soc_pinterest"
								class="span12 in-text has-placeholder" 
								value="<?= $vd->c_data->soc_pinterest ?>"
								placeholder="Pinterest Profile" />
							<strong class="placeholder">Pinterest Profile</strong>
						</div>
					</div>

					<h2>Blog URL</h2>
					<div class="relative">
						<input type="text" name="blog_url"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->blog_url ?>"
							placeholder="Blog URL" />
						<strong class="placeholder">Blog URL</strong>
					</div>

					<div class="relative">
						<input type="text" name="blog_rss"
							class="span12 in-text has-placeholder" 
							value="<?= $vd->c_data->blog_rss ?>"
							placeholder="Blog RSS URL" />
						<strong class="placeholder">Blog RSS URL</strong>
					</div>

					<div class="row-fluid">
						<div class="span4">
							<button type="submit" name="save" value="1" 
										class="span12 bt-orange pull-right">Save</button>
						</div>
					</div>
				</section>
			</div>			
			

			<script>
			
			$(function() {
				
				var options = { offset: { top: 20 } };
				$.lockfixed("#locked_aside", options);
				
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