<div class="container-fluid">
<form class="tab-content required-form" method="post" action="manage/newsroom/social/save">

	<div class="row">
		<header class="form-col-1 col-lg-12">
			<div class="row">
				<div class="col-lg-6">
					<h2>Social Settings</h2>
				</div>
				<div class="col-lg-6">
					<button type="submit" name="publish" value="1" 
						class="btn btn-primary pull-right"
						style="margin-right: 0">Save Settings</button>		
				</div>
			</div>
		</header>
	</div>
	
	<div class="row">
		<div class="col-lg-12 form-col-1">
			<div class="panel panel-default company-social">
				<div class="panel-body">
						
					<fieldset class="marbot">
						<div class="row form-group">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" value="1" name="is_enable_social_wire" class="selectable is-enable-social-wire" 
										id="is_enable_social_wire"
										<?= value_if_test(@$vd->profile->is_enable_social_wire 
											|| !@$vd->profile, 'checked')?>>
									<span class="checkbox"></span>
									Enable Social Wire within newsroom.
									<p class="help-block pad-30h hidden-x1s">
										Enabling our Social Wire feature will beautifully
										display the latest 10 items for each social media
										property you enter.
										<a href="#" class="help-block-link" data-title="What is Social Wire?" 
											data-id="what_social_wire">
											Example
										</a>
									</p>

								</label>
							</div>
						</div>

						<div class="row form-group marbot-20">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" value="1" name="is_twitter_english_feeds" class="selectable" 
										id="is_twitter_english_feeds"
										<?= value_if_test(@$vd->profile->is_twitter_english_feeds 
											|| !@$vd->profile, 'checked')?>>
									<span class="checkbox"></span>
									Include only English feeds from Twitter
								</label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Social Media Profiles</legend>

						
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 soc-facebook-text" type="text" 
									name="soc_facebook" placeholder="Facebook" 
									value="<?= $vd->esc(@$vd->profile->soc_facebook) ?>" />
								<p class="help-block">Facebook Username or Page</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_facebook_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_facebook_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_facebook_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 soc-twitter-text" type="text" 
									name="soc_twitter" placeholder="Twitter" 
									value="<?= $vd->esc(@$vd->profile->soc_twitter) ?>" />
								<p class="help-block">Twitter Username</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_twitter_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_twitter_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_twitter_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 gplus-profile-id" type="text" 
									name="soc_gplus" placeholder="Google Plus" 
									value="<?= $vd->esc(@$vd->profile->soc_gplus) ?>" />
								<p class="help-block">Google Plus ID</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_gplus_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_gplus_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_gplus_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 youtube-profile-id" type="text" 
									name="soc_youtube" placeholder="YouTube Username" 
									value="<?= $vd->esc(@$vd->profile->soc_youtube) ?>" />
								<p class="help-block">YouTube Username</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_youtube_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_youtube_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_youtube_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 pinterest-profile-id" type="text" 
									name="soc_pinterest" placeholder="Pinterest Profile" 
									value="<?= $vd->esc(@$vd->profile->soc_pinterest) ?>" />
								<p class="help-block">Pinterest Profile</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_pinterest_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_pinterest_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_pinterest_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>

							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 vimeo-profile-id" type="text" 
									name="soc_vimeo" placeholder="Vimeo Profile" 
									value="<?= $vd->esc(@$vd->profile->soc_vimeo) ?>" />
								<p class="help-block">Vimeo Profile  (example: newswire, channels/12345)</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_vimeo_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_vimeo_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_vimeo_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
						</div>

						<div class="row form-group marbot-20">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 instagram-profile-id" type="text" 
									name="soc_instagram" placeholder="Instagram Profile" 
									value="<?= $vd->esc(@$vd->profile->soc_instagram) ?>" />
								<p class="help-block">Instagram Profile</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_instagram_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_instagram_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_instagram_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>

							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 soc-linkedin-text" type="text" 
									name="soc_linkedin" placeholder="Linkedin" 
									value="<?= $vd->esc(@$vd->profile->soc_linkedin) ?>" />
								<p class="help-block">LinkedIn Profile (example: company/368804)</p>
								<label class="checkbox-container small-checkbox">
									<input type="checkbox" value="1" name="is_inc_linkedin_in_soc_wire" 
										class="selectable inc-in-social-wire" id="is_inc_linkedin_in_soc_wire"
										<?= value_if_test($vd->social_wire_settings->is_inc_linkedin_in_soc_wire, 'checked')?>>
									<span class="checkbox"></span>
									Include in Social Wire
								</label>
							</div>
						</div>
					</fieldset>


					<fieldset class="social-auth-fieldset">
						<legend>Social Authorization
							<a data-toggle="tooltip" class="tl" href="#" 
								title="Social authorization is needed for Social Wire and publishing your content to social media">
								<i class="fa fa-fw fa-question-circle"></i>
							</a>
						</legend>
						<div class="header-help-block marbot-30">
							Authorization is required for Social Wire integration and for content publishing. 
						</div>
						<div class="social-auth-services">
							<div class="social-auth-service">
								<img class="sauth-icon" src="<?= $vd->assets_base ?>im/sauth-facebook.png" />
								<h4>Facebook Authorization</h4>
								<div class="marbot-10">
									<div class="facebook-auth-text 
										<?= value_if_test(!$vd->is_facebook_auth, 'dnone') ?>">
										<span class="facebook-auth-title">
											Enabled for
										</span>
										<span class="facebook-auth-name">
											<strong><?= $vd->facebook_name ?></strong>.
										</span>
									</div>
									<div class="facebook-no-auth-text
										<?= value_if_test($vd->is_facebook_auth, 'dnone') ?>">
										<span class="facebook-auth-title">
											Facebook auth not found.
										</span>
									</div>
								</div>
								<div class="social-auth-button">
									<button class="btn btn-success btn-auth btn-facebook-auth  btn-rounded-right
										<?= value_if_test($vd->is_facebook_auth, 'dnone') ?>"
										data-media="<?= Model_PB_Social::TYPE_FACEBOOK ?>"
										type="button">Start Authorization</button>
									<button class="btn btn-info btn-settings btn-facebook-settings  btn-rounded-right
										<?= value_if_test(!$vd->is_facebook_auth, 'dnone') ?>" 
										data-media='<?= Model_PB_Social::TYPE_FACEBOOK ?>'
										type="button">Settings</button>												
									<input type="hidden" id="facebook_auth" 
										name="facebook_auth"
										value="<?= $vd->is_facebook_auth ?>">
								</div>
							</div>
							<div class="social-auth-service">
								<img class="sauth-icon" src="<?= $vd->assets_base ?>im/sauth-twitter.png" />
								<h4>Twitter Authorization</h4>
								<div class="marbot-10">
									<div class="twitter-auth-text 
										<?= value_if_test(!$vd->is_twitter_auth, 'dnone') ?>">
										<span class="twitter-auth-title">
											Enabled for
										</span>
										<span class="twitter-auth-name">
											<strong><?= $vd->twitter_name ?></strong>.
										</span>
									</div>
									<div class="twitter-no-auth-text
										<?= value_if_test($vd->is_twitter_auth, 'dnone') ?>">
										<span class="twitter-auth-title">
											Twitter auth not found.
										</span>
									</div>
								</div>
								<div class="social-auth-button">
									<input type="hidden" id="twitter_auth" name="twitter_auth" 
										class="required-callback"
										data-required-name="Twitter Authorization" 
										data-required-callback="is-twitter-auth"
										value="<?= $vd->is_twitter_auth ?>">
									<button class="btn btn-info btn-settings btn-twitter-settings  
										<?= value_if_test(!$vd->is_twitter_auth, 'dnone') ?>" 
										data-media='<?= Model_PB_Social::TYPE_TWITTER ?>'
										type="button">Settings</button>
									<button class="btn btn-success btn-auth btn-twitter-auth 
										<?= value_if_test($vd->is_twitter_auth, 'dnone') ?>"
										data-media='<?= Model_PB_Social::TYPE_TWITTER ?>'
										type="button">Start Authorization</button>
								</div>
							</div>
							<div class="social-auth-service">
								<img class="sauth-icon" src="<?= $vd->assets_base ?>im/sauth-linkedin.png" />
								<h4>Linkedin Authorization</h4>
								<div class="marbot-10">
									<div class="linkedin-auth-text 
										<?= value_if_test(!$vd->is_linkedin_auth, 'dnone') ?>">
										<span class="linkedin-auth-title">
											Enabled for
										</span>
										<span class="linkedin-auth-name">
											<strong><?= $vd->profile->soc_linkedin ?></strong>.
										</span>
									</div>
									<div class="linkedin-no-auth-text
										<?= value_if_test($vd->is_linkedin_auth, 'dnone') ?>">
										<span class="linkedin-auth-title">
											Linkedin auth not found.
										</span>
									</div>
								</div>
								<div class="social-auth-button">
									<input type="hidden" id="linkedin_auth" 
										class="required-callback"
										name="linkedin_auth" value="<?= @$vd->is_linkedin_auth ?>"
										data-required-name="Linkedin Authorization" 
										data-required-callback="is-linkedin-auth">

									<button class="btn btn-info btn-settings btn-linkedin-settings  btn-rounded-right
										<?= value_if_test(!@$vd->is_linkedin_auth, 'dnone') ?>" 
										data-media='<?= Model_PB_Social::TYPE_LINKEDIN ?>'
										type="button">Settings</button>
									<button class="btn btn-success btn-auth btn-linkedin-auth  btn-rounded-right
										<?= value_if_test($vd->is_linkedin_auth, 'dnone') ?>"
										data-media="<?= Model_PB_Social::TYPE_LINKEDIN ?>"
										type="button">Start Authorization</button>
								</div>
							</div>
						</div>
					</fieldset>
					
				</div>
			</div>
		</div>

		<script>

		$(function() {

			var auth_window;
			var is_enable_social_wire = $(".is-enable-social-wire");
			var soc_twitter = $(".soc-twitter");
			var soc_facebook = $(".soc-facebook");
			var soc_linkedin = $(".soc-linkedin");

			$(".btn-auth").on("click", function() {

				var _this = $(this);
				var media = _this.data('media');
				var parent_div = _this.parent().parent();
				var input_text = $("input[type=text].soc-"+media+"-text");
				var input_text_val = input_text.val();

				if (input_text_val == "" && media == "linkedin") {
					bootbox.alert('You need to enter linkedin company profile before you start authorization')
					return false;
				}

				var url = "manage/newsroom/social/start_auth/" + media + "?social_id=" + input_text_val;
				auth_window = window.open(url, 'Auth','toolbar=0,status=0,width=626,height=436');				
				setTimeout(function() {
					poll_auth(media);
				}, 3000);

			});

			var timeout_id = null;
			var poll_auth = function(media) {

				var url = "manage/newsroom/social/auth_poll/" + media;	

				$.get(url, function(res) {
					if (res.is_auth == 1) {

						var auth_btn = $(".btn-" + media + "-auth");
						var settings_btn = $(".btn-" + media + "-settings");
						var media_auth = $("#" + media + "_auth");
						var auth_text = $("." + media + "-auth-text");
						var no_auth_text = $("." + media + "-no-auth-text");
						var auth_name = $("." + media + "-auth-name");

						auth_text.removeClass("dnone");
						auth_name.html(res.social_name);
						no_auth_text.addClass("dnone");

						$(".required-error").remove();
						
						auth_btn.addClass("dnone");
						settings_btn.removeClass("dnone");
						media_auth.val(1);
						
						setTimeout(function() {
							settings_btn.trigger("click");
							window.clearTimeout(timeout_id);							
							auth_window.close();
						}, 3000);

					} else {
						timeout_id = setTimeout(function() {
							poll_auth(media);
						}, 1000);
					}
				});

			};

			$(".btn-settings").on("click", function() {

				var _this = $(this);
				var media = _this.data('media');

				var content_url = "manage/newsroom/social/settings_modal/" + media;
				var modal = $("#<?= $vd->settings_modal_id ?>");
				
				var modal_content = modal.find(".modal-content");
				
				var loading_div = $.create("div");
				var loading_img = $.create("img");
				loading_img.attr("src", "<?= $vd->assets_base ?>im/loader-line.gif");
				loading_div.addClass("ta-center");
				loading_div.append(loading_img);
				modal_content.html(loading_div);			
				
				var modal_title = modal.find('.modal-header h3');
				modal_title.html(media + ' auth settings');

				modal.modal('show');

				modal_content.load(content_url, function() {
					modal.modal('show');
				});
				
			});

			required_js.add_callback("is-twitter-auth", function(value) {
				var enable_soc_wire = is_enable_social_wire.is(":checked");
				var is_inc_twitter_in_soc_wire = $("#is_inc_twitter_in_soc_wire");
				var inc_twitter_in_soc_wire = is_inc_twitter_in_soc_wire.is(":checked");
				var response = { valid: false, 
					html: "You need to authorize in order to show Twitter feeds in your Social Wire" };
				response.valid = (!enable_soc_wire || !inc_twitter_in_soc_wire || value == 1) ? true : false;
				return response;
			});

			required_js.add_callback("is-linkedin-auth", function(value) {
				var enable_soc_wire = is_enable_social_wire.is(":checked");
				var is_inc_linkedin_in_soc_wire = $("#is_inc_linkedin_in_soc_wire");
				var inc_linkedin_in_soc_wire = is_inc_linkedin_in_soc_wire.is(":checked");
				var response = { valid: false, 
					html: "You need to authorize in order to show Linkedin feeds in your Social Wire" };
				response.valid = (!enable_soc_wire || !inc_linkedin_in_soc_wire || value == 1) ? true : false;
				return response;
			});


			$(document).on("click", ".auth-remove-btn", function(ev) {
				
				var _this = $(this);
				var media = _this.data("media");
				var url = "manage/newsroom/social/delete_auth/" + media;
				_this.attr("disabled", true);

				$.get(url, function(res) {

					if (res.is_deleted) {

						var auth_field = $("#" + media + "_auth");
						var auth_text = $("." + media + "-auth-text");
						var no_auth_text = $("." + media + "-no-auth-text");
						auth_field.val(0);
						auth_text.addClass("dnone");
						no_auth_text.removeClass("dnone");

						var auth_btn = $(".btn-" + media + "-auth");
						var settings_btn = $(".btn-" + media + "-settings");

						settings_btn.addClass('dnone');
						auth_btn.removeClass('dnone');

						var modal = $("#<?= $vd->settings_modal_id ?>");
						modal.modal('hide');
						
						
						// Now adding the feedback 
						// that auth is removed
						var feedback_row = $.create("div");
						feedback_row.addClass("alert alert-success alert-dismissible auth-remove-alert");
						var close_btn = $.create("button");
						close_btn.addClass("close");
						close_btn.attr("type", "button");
						close_btn.attr("data-dismiss", "alert");
						close_btn.attr("aria-label", "Close");
						var span = $.create("span");
						span.attr("aria-hidden", "true");
						span.html("&times;");
						close_btn.append(span);
						feedback_row.append(close_btn);
						var msg = $.create("span");
						msg.html('Authorization removed');
						feedback_row.append(msg);

						var settings_btn = $(".btn-" + media + "-settings");
						settings_btn.parent().parent().parent().prepend(feedback_row);

						setTimeout(function() {
							$(".auth-remove-alert").slideUp();
						}, 5000)
						
					}

				}); 

			});

			var toggle_include_checkboxes = function() {

				var social_wire_state = is_enable_social_wire.is(":checked");

				$(".inc-in-social-wire").each(function() {
					var _this = $(this);
					_this.prop("disabled", !social_wire_state);
				});

			};

			is_enable_social_wire.on("change", toggle_include_checkboxes);
			toggle_include_checkboxes();
			
		});

		</script>
		
		<div id="what_social_wire" class="hidden">
			<img class="social-help-img" src="assets/im/what_is_social_wire.jpg" alt="What is Social Wire?" />
		</div>

		<div id="what_social_widget" class="hidden social-setting-help">
			<img class="social-help-img" src="assets/im/what_is_social_widget.jpg" alt="What is Social Widget?" />
		</div>	
			
		<script>
		
		$(function() {
			
			if (is_desktop()) {
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