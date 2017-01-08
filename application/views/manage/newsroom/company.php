<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6">
				<h2>Company Profile</h2>
			</div>
		</div>
	</header>

	<form class="tab-content required-form" method="post" action="manage/newsroom/company/save">
	
	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset>
						<legend>Basic Information</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 required" type="text" 
									name="company_name" placeholder="Company Name"
									data-required-name="Company Name"
									value="<?= $vd->esc(@$vd->name) ?>" />
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 url" name="website" 
									placeholder="Company Website" type="url"
									value="<?= $vd->esc(@$vd->profile->website) ?>" />
							</div>

							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" name="email" 
									placeholder="Company Email" type="email"
									value="<?= $vd->esc(@$vd->profile->email) ?>" />
							</div>
						</div>
								
					</fieldset>

					<fieldset>
						<legend>Company Address</legend>
							
						<div class="row form-group">
							<div class="col-lg-8">
								<input class="form-control in-text col-lg-12" name="address_street" 
								placeholder="Street Address" type="text"
								value="<?= $vd->esc(@$vd->profile->address_street) ?>" />
							</div>
							<div class="col-lg-4">
								<input class="form-control in-text col-lg-12"  name="address_apt_suite"
									type="text" placeholder="Apt / Suite" 
									value="<?= $vd->esc(@$vd->profile->address_apt_suite) ?>" />
							</div>
						</div>
								
						<div class="row form-group">
							<div class="col-lg-4">
								<input class="form-control in-text col-lg-12" type="text" 
									name="address_city" placeholder="City"
									value="<?= $vd->esc(@$vd->profile->address_city) ?>" />
							</div>
							<div class="col-lg-4">
								<input class="form-control in-text col-lg-12" type="text" 
									name="address_state" placeholder="State / Region"
									value="<?= $vd->esc(@$vd->profile->address_state) ?>" />
							</div>
							<div class="col-lg-4">
								<input class="form-control in-text col-lg-12" type="text" 
									name="address_zip" placeholder="Zip Code"
									value="<?= $vd->esc(@$vd->profile->address_zip) ?>" />
							</div>
						</div>
								
						<div class="row form-group">
							<div class="col-lg-6">
								<select class="form-control selectpicker show-menu-arrow col-lg-12" name="address_country_id">
									<option class="selectpicker-default" title="Select Country" value=""
										<?= value_if_test(!@$vd->profile->address_country_id, 'selected') ?>>None</option>
									<?php foreach ($common_countries as $country): ?>
									<option value="<?= $country->id ?>"
										<?= value_if_test((@$vd->profile->address_country_id == $country->id), 'selected') ?>>
										<?= $vd->esc($country->name) ?>
									</option>
									<?php endforeach ?>
									<option data-divider="true"></option>
									<?php foreach ($countries as $country): ?>
									<option value="<?= $country->id ?>"
										<?= value_if_test((@$vd->profile->address_country_id == $country->id && 
											!$country->is_common), 'selected') ?>>
										<?= $vd->esc($country->name) ?>
									</option>
									<?php endforeach ?>
								</select>
								<script>

								defer(function() {
									
									$("#select-country select")
									 	.on_load_select({ size: 10 });
									
								});
								
								</script>
							</div>

							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="text" 
									name="phone" placeholder="Phone Number"
									value="<?= $vd->esc(@$vd->profile->phone) ?>" />
							</div>
						</div>

					</fieldset>
						
					<fieldset>
						<legend>Company Summary</legend>
						<div class="row from-group">
							<div class="col-lg-12">
								<textarea class="form-control in-text col-lg-12 required-callback" name="summary" 
									id="summary" placeholder="Short Company Description" data-required-name="Summary"
									data-required-callback="summary-min-words" rows="4"><?= 
									$vd->esc(@$vd->profile->summary) 
								?></textarea>
								<p class="help-block" id="summary_countdown_text">
									<span id="summary_countdown">250</span> Characters Left. 
									This will be visible on the newsroom sidebar.</p>
								<script>
						
								defer(function() {
									
									$("#summary").limit_length(250, 
										$("#summary_countdown_text"), 
										$("#summary_countdown"));
									
									required_js.add_callback("summary-min-words", function(value) {
										var response = { valid: false, text: "must have at least 10 words" };
										response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){10,}/i.test(value);
										return response;
									});
									
								});
								
								</script>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Company Description</legend>
						<div class="row form-group">
							<div class="col-lg-12 cke-container">
								<textarea class="form-control in-text col-lg-12" name="description" 
									placeholder="Company Description" id="description"><?= 
									$vd->esc(@$vd->profile->description) 
								?></textarea>
								<p class="help-block">
									Add a description of your company.
								</p>

								<script>
								$(function() {
									window.init_editor($("#description"), { height: 400 });
								});
								</script>

							</div>
						</div>
					</fieldset>

					<fieldset class="social-services">
						<legend>Include content from RSS Feeds</legend>
						<div class="row form-group marbot-5">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" value="1" name="is_enable_blog_posts"
										class="selectable" id="is_enable_blog_posts"
										<?= value_if_test(@$vd->profile->is_enable_blog_posts, 'checked')?>>
									<span class="checkbox"></span>
									Enable RSS feed import for <em class="normal-em status-black">BLOG</em> posts
									<a href="#" data-id="what_blog_posts" class="help-block-link"
										data-title="Blog Posts">
										More Info.
									</a>
								</label>
							</div>
						</div>
							
						<div class="row form-group marbot-15">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 rss-profile-id url" type="url" 
									name="soc_rss" placeholder="RSS Feed URL" id="blog-rss-url"
									value="<?= $vd->esc(@$vd->profile->soc_rss) ?>" />
							</div>
						</div>
							
						<script>

						defer(function() {

							var blog_url = $("#blog-rss-url");
							var blog_is_enable = $("#is_enable_blog_posts");

							blog_is_enable.on("change", function() {
								blog_url.prop("disabled", 
									!blog_is_enable.is(":checked"));
							});

							blog_is_enable.trigger("change");

						});

						</script>
							
						<div class="row form-group marbot-5">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" value="1" name="is_enable_rss_news"
										class="selectable" id="is_enable_rss_news"
										<?= value_if_test(@$vd->profile->is_enable_rss_news, 'checked')?>>
									<span class="checkbox"></span>
									Enable RSS feed import for <em class="normal-em status-black">NEWS</em> posts
									<a href="#" data-id="what_rss_news" class="help-block-link"
										data-title="News content from an RSS feed">
										More Info.
									</a>
								</label>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 rss-profile-id url" type="url" 
									name="rss_news_url" placeholder="RSS Feed URL" id="news-rss-url"
									value="<?= $vd->esc(@$vd->profile->rss_news_url) ?>" />
							</div>
						</div>

						<script>

						defer(function() {

							var news_url = $("#news-rss-url");
							var news_is_enable = $("#is_enable_rss_news");

							news_is_enable.on("change", function() {
								news_url.prop("disabled", 
									!news_is_enable.is(":checked"));
							});

							news_is_enable.trigger("change");

						});

						</script>
						
					</fieldset>
					
				</div>
			</div>
		</div>
		
					
		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default" id="locked_aside">
				<div class="panel-body">

					<h4>About the Company</h4>
						
					<div class="row form-group">
						<div class="col-lg-12" id="select-type">
							<select class="form-control selectpicker show-menu-arrow col-lg-12" name="type">
								<option class="selectpicker-default" title="Select Type" value=""
									<?= value_if_test(!@$vd->profile->type, 'selected') ?>>None</option>
								<option value="private"
									<?= value_if_test(@$vd->profile->type == 'private', 'selected') ?>>
									Privately Held</option>
								<option value="public"
									<?= value_if_test(@$vd->profile->type == 'public', 'selected') ?>>
									Publicly Held</option>
							</select>

							<script>
							defer(function() {
								
								$("#select-type select").on_load_select();
								
							});
							
							</script>

						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-12" id="select-industry">
							<select class="form-control selectpicker show-menu-arrow col-lg-12 category" name="beat_id"
								data-live-search="true">
								<option class="selectpicker-default" title="Select Industry" value=""
									<?= value_if_test(!@$vd->profile->beat_id, 'selected') ?>>None</option>
								<?php foreach ($vd->beats as $group): ?>
								<optgroup label="<?= $vd->esc($group->name) ?>">
									<?php foreach ($group->beats as $beat): ?>
									<option value="<?= $beat->id ?>"
										<?= value_if_test((@$vd->profile->beat_id == $beat->id), 'selected') ?>>
										<?= $vd->esc($beat->name) ?>
									</option>
									<?php endforeach ?>
								</optgroup>
								<?php endforeach ?>
							</select>

							<script>
							defer(function() {
								
								$("#select-industry select")
									.on_load_select({ size: 10 });
								
							});
							
							</script>
								
						</div>
					</div>

					<div class="row form-group">
						<div class="col-lg-12" id="select-year">
							<select class="form-control selectpicker show-menu-arrow col-lg-12" name="year">
								<option class="selectpicker-default" title="Year Founded" value=""
									<?= value_if_test(!@$vd->profile->year, 'selected') ?>>None</option>
								<?php for ($i = (int) date('Y'); $i >= 1800; $i--): ?>
								<option value="<?= $i ?>"
									<?= value_if_test((@$vd->profile->year == $i), 'selected') ?>>
									<?= $i ?>
								</option>
								<?php endfor ?>
							</select>

							<script>
							defer(function() {
								
								$("#select-year select")
									.on_load_select({ size: 10 });
								
							});
							
							</script>
								
						</div>
					</div>

					<hr class="aside-hr">

					<h4>Company Timezone</h4>

					<div class="row form-group">
						<div class="col-lg-12" id="select-timezone">
							<select class="form-control selectpicker col-lg-12 show-menu-arrow" name="timezone">
								<?php $timezone_selected = false; ?>
								<option class="selectpicker-default" title="Select Timezone" value=""
									<?= value_if_test(!$ci->newsroom->timezone, 'selected') ?>>Default</option>
								<optgroup label="Common Timezones">
									<?php foreach ($vd->common_timezones as $value => $timezone): ?>
									<option value="<?= $vd->esc($timezone) ?>"
										<?= value_if_test(!$timezone_selected && 
											$ci->newsroom->timezone == $timezone, 'selected') ?>>
										<?php if ($ci->newsroom->timezone == $timezone) 
											$timezone_selected = true; ?>
										<?= $vd->esc($value) ?>
									</option>
									<?php endforeach ?>
								</optgroup>
								<optgroup label="Local Timezones">
									<?php foreach ($vd->timezones as $timezone): ?>
									<option value="<?= $vd->esc($timezone) ?>"
										<?= value_if_test(!$timezone_selected && 
											$ci->newsroom->timezone == $timezone, 'selected') ?>>
										<?php if ($ci->newsroom->timezone == $timezone) 
											$timezone_selected = true; ?>
										<?php 
										
										$timezone = str_replace('_', ' ', $timezone);
										$timezone = str_replace('/', ' - ', $timezone);
										echo $vd->esc($timezone);
										
										?>
									</option>
									<?php endforeach ?>	
								</optgroup>
							</select>

							<script>

							defer(function() {
								
								var select = $("#select-timezone select");
								select.on_load_select({ size: 10 });

							});
							
							</script>
							<p class="help-block">Timezone is used within the control panel for date selection and display. 
								Published content will also show the timezone.</p>
						</div>
					</div>
								
					<div class="row form-group">
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
							<button type="submit" name="is_preview" value="1" 
								class="btn btn-default col-lg-12">Preview Newsroom</button>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 nomar">
							<button type="submit" name="publish" value="1" 
								class="col-lg-12 btn btn-primary nomar pull-right">Save</button>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div id="what_blog_posts" class="hidden">
			<p>
				This feature allows blog posts to be automatically posted to your newsroom.
			</p>
			<p>
				You will need the RSS feed URL provided by your blogging software. 
				We will check your feed for new content every hour. 
			</p>
			<p>
				The feed must be valid RSS 2.0 and include (at minimum) a title, link, description 
				and publication date (pubDate) for each piece of news content. You can also include an image
				and our system will include it. For more information about
				creating RSS feeds please see the following links:
			</p>
			<p>
				<a href="https://www.mnot.net/rss/tutorial/">https://www.mnot.net/rss/tutorial/</a><br />
				<a href="http://www.w3schools.com/webservices/rss_intro.asp">http://www.w3schools.com/webservices/rss_intro.asp</a><br />
				<a href="http://en.wikipedia.org/wiki/RSS">http://en.wikipedia.org/wiki/RSS</a>
			</p>
		</div>

		<div id="what_rss_news" class="hidden">
			<p>
				This feature allows news content to be automatically posted to your newsroom.
				We will check your feed for new content every hour. 
			</p>
			<p>
				The feed must be valid RSS 2.0 and include (at minimum) a title, link, description 
				and publication date (pubDate) for each piece of news content. You can also include an image
				and our system will include it. For more information about
				creating RSS feeds please see the following links:
			</p>
			<p>
				<a href="https://www.mnot.net/rss/tutorial/">https://www.mnot.net/rss/tutorial/</a><br />
				<a href="http://www.w3schools.com/webservices/rss_intro.asp">http://www.w3schools.com/webservices/rss_intro.asp</a><br />
				<a href="http://en.wikipedia.org/wiki/RSS">http://en.wikipedia.org/wiki/RSS</a>
			</p>
		</div>

		
		<script>
		
		$(function() {

			if (is_desktop())
			{
				var options = { offset: { top: 100 } };
				$.lockfixed("#locked_aside", options);
			}
			
		});

		
		</script>

					
	</div>
	</form>
</div>

<script>
$(function() {
	$("a.help-block-link").on("click", function(ev) {
		ev.preventDefault();
		var id = $(this).data("id");
		var title = $(this).data("title");
		var modal = $("#<?= $vd->info_modal_id ?>");
		var modal_content = modal.find(".modal-content");
		var show_html = $(document.getElementById(id)).html();
		var modal_title = modal.find('.modal-header h3');
		modal_title.html(title);
		modal_content.html(show_html);	
		modal.modal("show");
	});
});
</script>