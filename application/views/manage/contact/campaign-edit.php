<ul class="breadcrumb">
	<li><a href="manage/contact">Media Outreach</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/contact/campaign">Email Campaigns</a> <span class="divider">&raquo;</span></li>
	<?php if (@$vd->campaign): ?>
	<li class="active"><?= $vd->esc($vd->cut($vd->campaign->name, 50)) ?></li>
	<?php else: ?>
	<li class="active">New Campaign</li>
	<?php endif ?>
</ul>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->campaign): ?>
					<h2>Edit Email Campaign</h2>
				<?php else: ?>
					<h2>Add Email Campaign</h2>
				<?php endif ?>
			</div>
		</div>	
	</header>
	
	<form class="tab-content required-form" method="post" action="manage/contact/campaign/edit/save" 
		id="edit-campaign-form">
		
		<div class="row content">
			<div class="col-lg-8 col-md-7 information-panel form-col-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
								<input type="hidden" id="campaign-id" name="campaign_id" value="<?= @$vd->campaign->id ?>" />

								<fieldset class="form-section basic-information">
									<legend>Basic Information</legend>
									<div class="row form-group">
										<div class="col-md-12">
											<input class="form-control in-text required" type="text" 
												name="name" placeholder="Campaign Name"
												data-required-name="Campaign Name"
												value="<?= $vd->esc(@$vd->campaign->name) ?>" />
											<p class="help-block">
												Only you will see this. 
												None of your recipients will see the campaign name.
											</p>
										</div>
									</div>
								</fieldset>
							
								<fieldset class="form-section select-content">
									<legend>
										Select Content
										<?php if (!Auth::user()->is_free_user()): ?>
											<p class="help-inline">(Optional)</p>
										<?php endif ?>
									</legend>
							
									<div class="row form-group">
										<?php $related = @$vd->from_m_content; ?>
										<?php if (!$related) $related = @$vd->campaign->m_content; ?>
										<input type="hidden" id="content-id" name="content_id" 
											<?php if (Auth::user()->is_free_user()): ?>
											class="required" data-required-name="Content" 
											<?php endif ?>
											value="<?= @$related->id ?>" />

										<div id="select-content-switch" class="<?= value_if_test($related, 'selected') ?>">
											<div id="active-content">
												<div class="col-lg-12">	

													<div class="<?= value_if_test(!@$vd->pw_order, 'input-group') ?>">
														<input type="text" class="form-control in-text" 
															disabled id="content-title"
															value="<?= $vd->esc(@$related->title) ?> (<?= 
															$vd->esc(Model_Content::short_type(@$related->type)) ?>)" />

														<?php if (!$vd->pw_order): ?>
															<div class="input-group-btn">
																<button type="button" class="btn btn-success nomar">Change</button>
															</div>
														<?php endif ?>

													</div>

												</div>
												
												<div class="col-md-12 marbot-10"></div>												
												<div class="col-md-12" id="content-render-variant-selector">
													<label class="radio-container">
														<input type="radio" name="content-render-variant" 
															class="content-render-variant content-render-full" value="1" 
															<?= value_if_test($vd->campaign && 
																(is_null($vd->campaign->template_id) ||
																 intval($vd->campaign->template_id) === 1), 'checked') ?> />
														<span class="radio"></span>
														<span>Generate the email from <strong>full content</strong> (recommended). </span>
													</label>
													<label class="radio-container block">
														<input type="radio" name="content-render-variant" 
															class="content-render-variant content-render-summary" value="0"
															<?= value_if_test($vd->campaign && is_numeric($vd->campaign->template_id) && 
																intval($vd->campaign->template_id) === 0, 'checked') ?> />
														<span class="radio"></span>
														<span>Generate the email from content <strong>summary</strong>. </span>
													</label>

													<?php if ($vd->pitch_templates): ?>
														<?php foreach($vd->pitch_templates as $tpl_id => $template): ?>
															<label class="radio-container block">
																<input type="radio" name="content-render-variant" 
																	class="content-render-variant content-render-pitch" value="<?= $vd->esc($tpl_id) ?>"
																	<?= value_if_test($vd->campaign && $vd->campaign->template_id === $tpl_id, 'checked') ?> />
																<span class="radio"></span>
																
																<a data-toggle="tooltip" class="tl radio-hover-tooltip" 
																	title="<?= $template->hover_text ?>">
																	<span class="label label-info">Template</span>
																	<span><?= $template->title ?></span>
																</a>
															</label>
														<?php endforeach ?>
													<?php endif ?>

												</div>
												<?php if (Auth::is_admin_online()): ?>
												<div class="col-md-12">
													<div class="marbot-15" style="margin-top: 5px; 
														border-top: 1px dashed #ddd; width: 60%"></div>
												</div>

												<div class="col-md-12">
													<label class="checkbox-container">
														<input type="checkbox" name="allow_non_published_content" value="1"
															<?= value_if_test(@$vd->campaign->allow_non_published_content, 'checked') ?> />
														<span class="checkbox"></span>
														<span>Permit send before content is published.</span>
													</label>
												</div>
												<?php endif ?>
											</div>
											<div class="content-search col-md-12">
												<ul id="content-results" class="marbot nopad"></ul>
											</div>
											<div class="content-search col-md-12">
												<button id="load-more" type="button" class="btn btn-sm marbot btn-default">
													Load More
												</button>
												<img src="<?= $vd->assets_base ?>im/loader-line.gif" />
											</div>
										</div>
										<script>

										$(function() {

											var select_content_switch = $("#select-content-switch");
											var active_content = $("#active-content");
											var content_results = $("#content-results");
											var content_title = $("#content-title");
											var content_id = $("#content-id");
											var change_button = active_content.find("button");
											var load_more_button = $("#load-more");
											var subject_field = $("#subject");
											var content_variant_selector = $("#content-render-variant-selector");

											var cached_data_content = <?= json_encode($this->vd->default_content) ?>;
											var cached_data_content_summary = <?= json_encode($this->vd->default_content_summary) ?>;
											var pitch_templates = <?= json_encode($this->vd->pitch_templates) ?>;

											var campaign_subject = <?= json_encode(value_or(@$this->vd->m_content->title, '')) ?>;
											var campaign_location = <?= json_encode(value_or(@$this->vd->m_content->location, '')) ?>;
											var selected_content = <?= json_encode(value_or(@$this->vd->campaign->template_id, 0)) ?>;

											var post_data = {};
											post_data.limit = 5;
											post_data.offset = 0;

											var perform_render = function(results) {

												load_more_button.removeClass("loader");
												if (results.data.length == 0 || 
												    results.data.length % post_data.limit != 0)
													load_more_button.addClass("disabled");

												for (var idx in results.data) {

													var result = results.data[idx];

													var row = $.create("li");
													row.addClass("row not-row");
													var button = $.create("button");

													var type_span = $.create("span");
													type_span.addClass("pull-right");
													type_span.text(" (" + result.type + ")");

													row.data("content-id", result.id);
													row.data("location", result.location);
													row.data("subject", result.subject);
													row.data("content", result.content);
													row.data("content-summary", result.content_summary);

													// we esc() on the server side before send
													row.html(result.title);
													row.append(type_span);
													button.attr("type", "button");
													button.addClass("btn btn-xs btn-success");
													button.text("Select");
													row.prepend(button);
													content_results.append(row);

												}

												if (!content_results.children().size())
													content_results.text("None Available");

											};

											var perform_load = function(value) {

												load_more_button.addClass("loader");
												$.post("manage/contact/campaign/find_content", 
													post_data, perform_render);
												post_data.offset += post_data.limit;

											};

											content_results.on("click", "li", function() {

												// we need to differentiate between selected
												// content and default content to get source
												content_is_selected_from_results = true;

												var _this = $(this);
												_this.find("button").remove();
												content_id.val(_this.data("content-id"));
												content_title.val(_this.text());

												// save the selected summary/content so we can switch
												cached_data_content_summary = _this.data("content-summary");
												cached_data_content = _this.data("content");

												// set campaign default content
												campaign_subject = _this.data("subject");
												campaign_location = _this.data("location");
												subject_field.val(_this.data("subject"));
												var is_full = parseInt(content_variant_selector.find("input:checked").val()) === 1;
												var editor = CKEDITOR.instances.content;
												editor.setData(is_full 
														? cached_data_content 
														: cached_data_content_summary);

												content_variant_selector.find("input[type=radio][value='0']").prop("checked", true);
												select_content_switch.addClass("selected");
												content_results.empty();

											});

											change_button.on("click", function() {

												content_id.val("");
												select_content_switch.removeClass("selected");
												load_more_button.removeClass("disabled");
												content_results.empty();
												post_data.offset = 0;
												perform_load();

											});

											content_variant_selector.on("change", "input", function(ev) {
												
												var message = "This will <strong>replace<\/strong> \
													the existing campaign content. <br>\
													Are you sure you wish to continue?";

												bootbox.confirm(message, function(confirmed) {

													var is_full = parseInt(content_variant_selector.find("input:checked").val()) === 1;
													var pitch_template_id = content_variant_selector.find(".content-render-pitch:checked").val();
													var editor = CKEDITOR.instances.content;

													if (!confirmed) {
														// undo the change of the radio buttons
														var variant_radio_selector = "input[type=radio][value='" + selected_content + "']";
														content_variant_selector.find(variant_radio_selector).prop("checked", true);
														return;
													}

													selected_content = content_variant_selector.find("input:checked").val();

													if (pitch_template_id && pitch_templates[pitch_template_id]) {

														// select pitch template
														var template = pitch_templates[pitch_template_id];
														subject_field.val(template.headline.format({ 
															title: campaign_subject, 
															location: campaign_location
														}));

														editor.setData(template.content);
														return;

													}

													subject_field.val(campaign_subject);
													editor.setData(is_full 
														? cached_data_content 
														: cached_data_content_summary);
												});
												
											});

											load_more_button.on("click", perform_load);
											perform_load();

										});

										</script>
									</div>
								</fieldset>
							</div>
						</div>

						<?php if (isset($vd->pw_order)): ?>
						<?php if (@$vd->pitch_requires_review): ?>
							<div class="alert alert-success with-btn-right">
								Pitch is ready for review and needs your attention.
								<div class="pull-right">
									<button class="btn btn-success btn-xs" value="1" name="is_accept_pitch"
										id="is_accept_pitch" type="submit">Accept</button>
									<button class="btn btn-danger btn-xs" value="1" name="is_reject_pitch"
										type="button" id="is_reject_pitch">Reject</button>
									<button class="btn btn-info btn-xs " value="1" name="is_edit_pitch"
										id="is_edit_pitch" type="button">Edit</button>
								</div>
							</div>
							<section class="form-section hidden" id="rejection_reason_div">
								<fieldset>
									<legend>Rejection Reason</legend>
									<div class="row form-group">
										<div class="col-md-12">
											<textarea name="rejection_reason" id="rejection_reason"
												class="form-control" placeholder="Rejection Reason" rows="5" 
												data-required-name="Rejection Comments"></textarea>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<button name="reject_button" class="btn btn-danger"
												id="reject_button" type="submit" value="1"
												data-required-name="Rejection Comments">Reject</button>
										</div>
									</div>
								</fieldset>
							</section>
						<?php endif ?>
						<?php endif ?>

						<fieldset class="form-section <?= value_if_test (@$vd->pitch_requires_review, 'hidden')?>"
							id="edit-pitch-section">
							<legend>Email Content</legend>
							<div class="row form-group">
								<div class="col-md-12">
									<input class="form-control in-text required" type="text" 
										name="subject" placeholder="Subject Line" id="subject"
										data-required-name="Subject"
										value="<?= @$vd->from_m_content ? 
											$vd->esc(@$vd->from_m_content->title) :
											$vd->esc(@$vd->campaign->subject) 
										?>" />
								</div>
							</div>
							<div class="row form-group marbot-20">
								<div class="col-md-12">
									<div id="marker-buttons" class="btn-group">
										<?php foreach ($markers as $marker => $label): ?>
											<button class="btn btn-xs btn-marker" 
												value="((<?= $vd->esc($marker) ?>))" type="button">
												<?= $vd->esc($label) ?>
											</button>
										<?php endforeach ?>
									</div>
									<textarea class="in-text in-content required" id="content"
										data-required-name="Content Body" name="content" 
										data-link-default-url="((tracking-link))"
										placeholder="Email Body"><?= 
											$ci->load->view('manage/partials/email-template-css') . 
											($vd->from_m_content ? $vd->esc($vd->default_content) : 
												$ci->load->view('partials/html-content', 
													array('content' => @$vd->campaign->content)))
									?></textarea>
								</div>
							
								<script>

								defer(function() {

									window.init_editor($("#content"), { 
										filebrowserUploadUrl: <?= json_encode($ci->newsroom->url('manage/contact/campaign/upload_image')) ?>,
										extraAllowedContent: 'style a img p b span div i blockquote q(ei-*)',
										extraPlugins: 'filebrowser,image',
										height: 400
									});

								})

									
								
								$(function() { 

									$("#marker-buttons .btn-marker").on("click", function() {
										var editor = CKEDITOR.instances["content"];
										var create = CKEDITOR.plugins.placeholder.createPlaceholder;
										var text = $(this).val();
										create(editor, undefined, text);
									});

									$("#is_edit_pitch").on("click", function() {
										$("#edit-pitch-section").removeClass("hidden");
										$("#pitch-text").addClass("hidden");
										$("#rejection_reason_div").addClass("hidden");
										$("#rejection_reason").removeClass("required");
										$("#pitch_edit_buttons").removeClass("hidden");
									});

									$("#is_reject_pitch").on("click", function() {
										$("#rejection_reason_div").removeClass("hidden");
										$("#rejection_reason").addClass("required");
										$("#edit-pitch-section").addClass("hidden");
										$("#pitch-text").removeClass("hidden");
										$("#pitch_edit_buttons").addClass("hidden");
									});
									
								});

								</script>
							</div>
							<?php if (isset($vd->pw_order)): ?>
							<div class="col-md-12 marbot-20 hidden" id="pitch_edit_buttons">
								<div class="col-md-5 col-md-offset-3">
									<button type="submit" name="reject_after_editing" value="1" 
										class="btn btn-default pull-right">Send To Editorial Review</button>
								</div>
								<div class="col-lg-4">
									<button type="submit" name="approve_after_editing" value="1" 
											class="btn btn-success pull-right">Approve After Editing</button>
								</div>
							</div>
							<?php endif ?>
						</fieldset>

						<?php if (@$vd->pitch_requires_review): ?>
						<div class="marbot-15"></div>
						<fieldset class="form-section border-dashed" id="pitch-text">
							<div class="row form-group">
								<div class="col-lg-12">
									<h3>Subject Line</h3>
									<?= $vd->esc(@$vd->campaign->subject) ?>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-lg-12">
									<h3>Pitch</h3>
								</div>
								<div class="col-lg-12">
									<?= @$vd->campaign->content ?>
								</div>
							</div>

							<input type="hidden" name="subject" id="pitch_subject" disabled="disabled" 
								value="<?= $vd->esc(@$vd->campaign->subject) ?>" />
							<input type="hidden" name="content" id="pitch_content" disabled="disabled" 
								value="<?= $vd->esc(@$vd->campaign->content) ?>" />
						</fieldset>
						<?php endif ?>

						<fieldset class="form-section">
							<legend>Contact Details</legend>																		
							<?php if (Auth::is_admin_online()): ?>
							<div class="header-help-block marbot">Your contact details for return messages.</div>	
							<div class="row">
								<div class="col-md-12">
									<label class="checkbox-container smaller small-checkbox">
										<input type="checkbox" name="sender_use_from" value="1"
											<?= value_if_test(@$vd->campaign->sender_use_from, 'checked') ?> />
										<span class="checkbox"></span>
										<span class="status-false">Use these contact details for the <span class="status-black">From</span> header.</span>
									</label>
								</div>
							</div>
							<?php else: ?>
							<div class="header-help-block">Your contact details for return messages.</div>	
							<?php endif ?>
							<div class="row form-group">
								<div class="col-md-6">
									<input class="form-control in-text required" type="text" 
										name="sender_name" placeholder="Sender Name"
										<?php if ($vd->campaign && $vd->campaign->sender_name): ?>
										value="<?= $vd->esc($vd->campaign->sender_name) ?>" 
										<?php else: ?>
										value="<?= Auth::user()->name() ?>" 
										<?php endif ?>
										data-required-name="Sender Name" />
										<p class="help-block">
											Full contact name.
										</p>
								</div>
								<div class="col-md-6">
									<input class="form-control in-text required" type="text" 
										name="sender_email" placeholder="Sender Email"
										<?php if ($vd->campaign && $vd->campaign->sender_email): ?>
										value="<?= $vd->esc($vd->campaign->sender_email) ?>" 
										<?php else: ?>
										value="<?= Auth::user()->email  ?>" 
										<?php endif ?>
										data-required-name="Sender Email" />
										<p class="help-block">
											Email address for replies.
										</p>
								</div>
							</div>		
						</fieldset>
						
						<?= $ci->load->view('manage/contact/partials/contact_lists', null, true) ?>
						
					</div>
				</div>
			</div>

			<div class="col-lg-4 col-md-5 form-col-2">
				<div id="locked_aside">
				<div class="panel panel-default cart">
						<div class="panel-body">
							<div class="col-lg-12">
								<div class="row form-group nomarbot">
									<fieldset class="ap-block ap-status">
										<legend class="nomarbot">
											Status: <span class="text-muted">
											<?php if (!@$vd->campaign): ?>
											<span>Not Saved</span>
											<?php elseif ($vd->campaign->is_sent): ?>
											<span>Sent</span>
											<?php elseif ($vd->campaign->is_draft): ?>
											<span>Saved (Draft)</span>
											<?php elseif ($vd->campaign->is_send_active): ?>
											<span>Sending</span>
											<?php else: ?>
											<span>Scheduled</span>
											<?php endif ?>
											</span>
										</legend>
									</fieldset>
									<fieldset class="ap-block nomarbot">
										<div class="row form-group">
											<div class="col-md-12">
												<input class="form-control in-text datepicker" id="send-date" type="text" 
													data-date-format="yyyy-mm-dd hh:ii" name="date_send" 
													value="<?= @$vd->campaign->date_send_str ?>"
													placeholder="Send Date" />
												<script>
												
												$(function() {
													
													var nowTemp = new Date();
													var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 
														nowTemp.getDate(), 0, 0, 0, 0);
													
													var send_date = $("#send-date")
													
													send_date.datetimepicker({
														startDate: now,
														autoclose: true,
														todayBtn: true,
														minView: 1,
													});
													
													send_date.on("changeDate", function(ev) {
														ev.date.setMinutes(0);
													});
													
												});
												
												</script>	
												<?php if ($this->newsroom->timezone): ?>
												<p class="smaller text-muted date-timezone-subtext">
													<?= $vd->esc(TimeZone::common_name($this->newsroom->timezone)) ?>
													(<a target="_blank" href="manage/newsroom/company">edit</a>)
												</p>
												<?php endif ?>
											</div>
										</div>
										<?php if (Auth::is_admin_online()): ?>
											<div class="row form-group">
												<div class="col-md-12">
													<label class="checkbox-container">
														<input type="checkbox" name="bypass_spam_check" value="1"
															<?= value_if_test($vd->campaign && $vd->campaign->bypass_spam_check, "checked") ?> />
														<span class="checkbox"></span>
														<span>
															Bypass spam check
															<?php if ($vd->campaign): ?>
																<span class="spam-report-link">
																<?php if ($vd->campaign->spam_score >= $ci->conf('spam_score_threshold')): ?>
																	&nbsp;&nbsp;<strong class="status-false">(<?= number_format($vd->campaign->spam_score, 1) ?>)</strong>
																<?php else: ?>
																	&nbsp;&nbsp;<strong class="status-true">(<?= number_format($vd->campaign->spam_score, 1) ?>)</strong>
																<?php endif ?>
																</span>
															<?php endif ?>
														</span>
													</label>
												</div>
											</div>
										<?php endif ?>
										<div class="row form-group">
											<div class="col-md-12">
												<?php if (@$vd->campaign->is_sent): ?>
												<div class="col-md-6">
													<button type="submit" name="publish" value="1" 
														class="btn btn-primary">Save</button>
												</div>
												<div class="col-md-6">
													<button type="submit" name="resend" value="1" 
														class="btn btn-success pull-right">Resend</button>
												</div>
												<?php else: ?>
												<div class="col-md-6 col-sm-6 col-xs-6 nopad">
													<button type="submit" name="is_draft" value="1" 
														class="btn btn-default col-md-11">Save Draft</button>
												</div>
												<div class="col-md-6 col-sm-6 col-xs-6 nopad">
													<button type="submit" name="publish" value="1" 
														class="btn btn-primary col-md-11 pull-right nomar">Send</button>
												</div>
												<?php endif ?>
											</div>
										</li>
									</fieldset>
								</div>
							</div>
						</div>
					</div>
					<div class="alert alert-info marbot-30">
						<strong>Attention!</strong>
						You should test the email with the form 
						shown below	to ensure it is correct.
					</div>

					<div class="panel panel-default cart">
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12 nomarbot">
									<fieldset class="ap-block ap-test-email nomarbot">
										<legend>Test Email</legend>
										<div class="row form-group">
											<div class="col-lg-12">
												<input type="text" placeholder="First Name" 
													name="test_first_name" class="form-control in-text" />
											</div>
										</div>
										<div class="row form-group">
											<div class="col-lg-12">
												<input type="text" placeholder="Last Name" 
													name="test_last_name" class="form-control in-text" />
											</div>
										</div>
										<div class="row form-group">
											<div class="col-lg-12">
												<input type="email" placeholder="Email Address" 
													name="test_email" class="form-control in-text">
											</div>
										</div>

										<div class="row form-group nomarbot">
											<div class="col-lg-12">
												<button type="submit" name="test" 
													value="1" class="btn btn-default col-lg-7 pull-right nomar">
														Save and Send
												</button>
											</div>
										</div>
									</fieldset>
								</div>
							</div>
						</div>					

						<script>
						
						$(function() {

							if (is_desktop()) {
								var options = { offset: { top: -20 } };
								$.lockfixed("#locked_aside", options);
							}

							<?php if ($vd->campaign): ?>

							var ssmodal = $("#spam-report-modal");
							var sslink = $(".spam-report-link");
							sslink.on("click", function(ev) {
								ev.preventDefault();
								ssmodal.modal("show");
								var content = ssmodal.find(".modal-content");
								var uri = <?= json_encode($ci->website_url(sprintf('admin/contact/campaign/spam_report/%d', $vd->campaign->id))) ?>;
								$.ajax(uri, {
									xhrFields: { withCredentials: true },
									success: function(res) {
										content.html(res);
									}
								});
							});

							<?php endif ?>

						});
						
						</script>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>