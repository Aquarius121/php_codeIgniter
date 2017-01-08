<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-switch.css');
	$loader->add('lib/bootstrap-highlight.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->contact): ?>
					<h2>Edit Contact (Subscriber)</h2>
				<?php else: ?>
					<h2>Add Contact</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form" method="post" action="manage/contact/contact/edit/save">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-8">
							<input type="hidden" name="email" value="<?= $vd->email ?>">
							<input type="hidden" name="from_url" value="<?= @$vd->from_url ?>">
							<input type="hidden" name="is_subscriber" value="1">
							
							<legend class="marbot-10"><?= @$vd->newsroom_name ?> Email Notifications</legend>
							<div class="row">
								<div class="col-lg-12 marbot-20">
									Manage Notifications for <?= @$vd->email ?>
								</div>
								
								<div class="col-lg-12 subscription-header marbot-20">
									<label class="checkbox-container inline">
										<input type="checkbox" data-switch-no-init  name="select_all" 
											class="selectable toggle-all" 
											id="select_all">
										<span class="checkbox"></span>
										<strong>Select All</strong>
									</label>
								</div>
							</div>
								
							
							<div class="row marbot-20">
								<div class="col-lg-6">
									<h5>Daily Updates</h5>
								</div>
								<div class="col-lg-3">
									<div class="pull-right">
										<h5>Real-time Updates</h5>									
									</div>
								</div>
							</div>

										
							<div class="row form-group">
								<div class="col-lg-7">
									<label class="checkbox-container inline marbot-15 ">
										<input type="checkbox" data-switch-no-init  value="1" name="is_include_prs" 
											class="selectable content_type_ch" 
											id="is_include_prs" data-switch-no-init
											data-instant-div="instant_notify_prs"
											<?= value_if_test(isset($vd->subscriber->is_include_prs) 
													&& @$vd->subscriber->is_include_prs == 0, '', 
													'checked = checked')?>>
										<span class="checkbox"></span>
										Press Releases
									</label>
								</div>
								<div class="col-lg-5 pad-10h" id="instant_notify_prs">
									<input id="switch-state" name="is_instant_notify_prs" type="checkbox"
										<?= value_if_test(@$vd->subscriber->is_instant_notify_prs, 'checked') ?> value="1">
								</div>
							</div>

							<div class="row form-group">
								
								<div class="col-lg-7">
									<label class="checkbox-container inline marbot-15 ">
										<input type="checkbox" data-switch-no-init  value="1" name="is_include_news" 
											class="selectable content_type_ch" 
											id="is_include_news"
											data-instant-div="instant_notify_news"
											<?= value_if_test(isset($vd->subscriber->is_include_news) 
													&& @$vd->subscriber->is_include_news == 0, '', 
													'checked = checked')?>>
										<span class="checkbox"></span>
										News
									</label>
								</div>
								<div class="col-lg-5 pad-10h" id="instant_notify_news">
									<div class="btn-group btn-toggle"> 
										<input id="switch-state" name="is_instant_notify_news" type="checkbox"
										<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 'checked') ?> value="1">

										<!--<button class="btn btn-xs btn-switch  btn-on 
											<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 
												'btn-primary active', 'btn-default') ?>" 
											data-field="is_instant_notify_news" value="1"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 
												'ON') ?>
										</button>
										<button class="btn btn-xs btn-switch  btn-off
											<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 
												'btn-default', 'btn-primary active') ?>" 
											data-field="is_instant_notify_news" value="0"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 
												'', 'OFF') ?>
										</button>
										<input type="hidden" name="is_instant_notify_news"
											 id="is_instant_notify_news"
											value="<?= value_if_test(@$vd->subscriber->is_instant_notify_news, 
												'1', '0') ?>">-->
									</div>
								</div>
							</div>

							<div class="row form-group">											
								<div class="col-lg-7">
									<label class="checkbox-container inline marbot-15 ">
										<input type="checkbox" data-switch-no-init  value="1" name="is_include_events" 
											class="selectable content_type_ch" 
											id="is_include_events"
											data-instant-div="instant_notify_events"
											<?= value_if_test(isset($vd->subscriber->is_include_events) 
													&& @$vd->subscriber->is_include_events == 0, '', 
													'checked = checked')?>>
										<span class="checkbox"></span>
										Events
									</label>
								</div>
								<div class="col-lg-5 pad-10h" id="instant_notify_events">
									<div class="btn-group btn-toggle"> 
										<input id="switch-state" name="is_instant_notify_events" type="checkbox"
										<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 'checked') ?> value="1">

										<!--<button class="btn btn-xs btn-switch  btn-on
											<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 
												'btn-primary active', 'btn-default') ?>" 
											data-field="is_instant_notify_events" value="1"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 
												'ON') ?>
										</button>
										<button class="btn btn-xs btn-switch  btn-off
											<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 
												'btn-default', 'btn-primary active') ?>" 
											data-field="is_instant_notify_events" value="0"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 
												'', 'OFF') ?>
										</button>
										<input type="hidden" name="is_instant_notify_events"
											 id="is_instant_notify_events"
											value="<?= value_if_test(@$vd->subscriber->is_instant_notify_events, 
												'1', '0') ?>">-->
									</div>
								</div>
							</div>
										
							<?php if (@$vd->nr_profile->soc_rss && @$vd->nr_profile->is_enable_blog_posts): ?>
							<div class="row form-group">
								
								<div class="col-lg-7">
									<label class="checkbox-container inline marbot-15 ">
										<input type="checkbox" data-switch-no-init  value="1" name="is_include_blog_posts" 
											class="selectable content_type_ch" 
											id="is_include_blog_posts"
											data-instant-div="instant_notify_blog_posts"
											<?= value_if_test(isset($vd->subscriber->is_include_blog_posts) 
													&& @$vd->subscriber->is_include_blog_posts == 0, '', 
													'checked = checked')?>>
										<span class="checkbox"></span>
										Blog Posts
									</label>
								</div>
								<div class="col-lg-5 pad-10h" id="instant_notify_blog_posts">
									<div class="btn-group btn-toggle"> 
										<input id="switch-state" name="is_instant_notify_blog_posts" type="checkbox"
										<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 'checked') ?> value="1">

										<!--<button class="btn btn-xs btn-switch  btn-on
											<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 
												'btn-primary active', 'btn-default') ?>" 
											data-field="is_instant_notify_blog_posts" value="1"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 
												'ON') ?>
										</button>
										<button class="btn btn-xs btn-switch  btn-off
											<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 
												'btn-default', 'btn-primary active') ?>" 
											data-field="is_instant_notify_blog_posts" value="0"
											type="button">
											<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 
												'', 'OFF') ?>
										</button>
										<input type="hidden" name="is_instant_notify_blog_posts"
											 id="is_instant_notify_blog_posts"
											value="<?= value_if_test(@$vd->subscriber->is_instant_notify_blog_posts, 
												'1', '0') ?>">-->
									</div>
								</div>
							</div>
							<?php endif ?>

							<?php foreach (@$vd->social_profiles as $social_profile): ?>
							<div class="row form-group">
								<div class="col-lg-7">
									<label class="checkbox-container inline marbot-15 ">
										<input type="checkbox" data-switch-no-init  value="1" name="is_include_<?= $social_profile ?>" 
											class="selectable content_type_ch" 
											id="is_include_<?= $social_profile ?>"
											data-instant-div="instant_notify_<?= $social_profile ?>"
											<?php $var_name = "is_include_".$social_profile ?>
											<?= value_if_test(isset($vd->subscriber->{$var_name}) 
													&& @$vd->subscriber->{$var_name}, 
													'checked = checked', '')?>>
										<span class="checkbox"></span>
										<?= value_if_test($social_profile == Model_PB_Social::TYPE_GPLUS, 
											'Google +', ucwords($social_profile)) ?>
									</label>
								</div>
								<div class="col-lg-5 pad-10h" id="instant_notify_<?= $social_profile ?>">
									<?php $instant_var_name =  "is_instant_notify_{$social_profile}" ?>

									<input id="switch-state" name="is_instant_notify_<?= $social_profile ?>" type="checkbox"
										<?= value_if_test(@$vd->subscriber->{$instant_var_name}, 'checked') ?> value="1">

									
								</div>
							</div>
							
							<?php endforeach ?>

							<div class="marbot-30"></div>
							<div class="row form-group">
								<div class="col-lg-4">
									<button class="btn btn-primary" type="sumbit"
										name="update_subscription"
										value="1">Update Preferences</button>
								</div>
							</div>
						
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<script>
$(function(){
	var all_checked = 0;
	$('.btn-toggle').click(function() {
	    $(this).find('.btn').toggleClass('active');
	    
	    if ($(this).find('.btn-primary').size() > 0) {	    	
	    	$(this).find('.btn').toggleClass('btn-primary');
	    	
	    	$(this).find('.btn').html('&nbsp; &nbsp; &nbsp;');
	    	
	    	if ($(this).find('.btn-primary').hasClass('btn-on'))
	    		$(this).find('.btn-primary').html('ON');
	    	if ($(this).find('.btn-primary').hasClass('btn-off'))
	    		$(this).find('.btn-primary').html('OFF');
	    }
	    
	    $(this).find('.btn').toggleClass('btn-default');
	       
	}); 

	$('.btn-switch').click(function() {
	    var field = $(this).data('field');
	    var v = $(this).val();
	    $("#"+field).val(v);       
	});

	
	var content_type_radios = $(".content_type_ch");	

	var handle_content_type_radio_change = function() {
		var _this = $(this);	
		toggle_instant_area(_this);
	};

	var toggle_instant_area = function(check_box){
		var instant_div = check_box.data('instant-div');
		var is_display = check_box.is(":checked");
		if (is_display)
			$("#"+instant_div).slideDown('fast');
		else
			$("#"+instant_div).slideUp('slow');
	};

	var validate_all_instant_areas = function(){
		content_type_radios.each(function() {
			toggle_instant_area($(this));
		});
	}

	$(".toggle-all").on("click", function(ev){
		all_checked = !all_checked;
		//ev.preventDefault();
		var first_check_state = content_type_radios.eq(0).is(":checked");
		content_type_radios.each(function() {
			$(this).prop('checked', all_checked);
		});

		validate_all_instant_areas();
		
	});
	
	content_type_radios.on("change", handle_content_type_radio_change);
	
	validate_all_instant_areas();
});


</script>
<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootstrap-switch.js');
	$loader->add('lib/bootstrap-highlight.js');
	$loader->add('lib/bootstrap-switch-main.js');

	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>