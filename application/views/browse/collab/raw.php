<!doctype html>
<html lang="en">
	<head>
		
		<title>
			<?php if (isset($ci->title) && $ci->title): ?>
				<?= $vd->esc($ci->title) ?> |
			<?php endif ?>
			<?php foreach(array_reverse($vd->title) as $title): ?>
				<?= $vd->esc($title) ?> |
			<?php endforeach ?>
		</title>
		
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
		<base href="<?= $ci->env['base_url'] ?>" />
		
		<?php 

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/jquery.js');
		$loader->add('lib/jquery.create.js');
		$render_basic = $ci->is_development();
		echo $loader->render($render_basic);

		?>
		
	</head>
	
	<body class="collab">

		<?= $ci->load->view('browse/partials/top-panel') ?>

		<div class="collab-container">
			<div class="row-fluid">
				<div class="span8 relative">

					<div id="feedback">
					<?php $ci->process_feedback(); ?>
					<?php foreach ($ci->feedback as $feedback): ?>
					<div class="feedback"><?= $feedback ?></div>
					<?php endforeach ?>
					</div>

					<div id="collab-content-overlay" class="collab-overlay"></div>		
					<div id="cv-container" class="content-type-<?= $vd->m_content->type ?> wide-view">
						
						<article class="article">

							<header class="article-header">
								<h2><?= $vd->esc($vd->m_content->title) ?></h2>
							</header>

							<section class="article-content">
								<?php if ($vd->m_content->is_premium): ?>
									<?php $cover_image = Model_Image::find($vd->m_content->cover_image_id); ?>
									<?php if ($cover_image): ?>
										<?php $orig_variant = $cover_image->variant('original'); ?>
										<?php $ci_variant = $cover_image->variant('view-cover'); ?>
										<a href="<?= Stored_File::url_from_filename($orig_variant->filename) ?>"
											class="use-lightbox floated-featured-image content-media" target="_blank">
											<img src="<?= Stored_File::url_from_filename($ci_variant->filename) ?>" 
												alt="<?= $vd->esc($vd->m_content->title) ?>" class="add-border has-2x"
												data-url-2x="shared/resim/view-cover-2x/<?= $cover_image->id ?>"
												width="<?= $ci_variant->width ?>"
												height="<?= $ci_variant->height ?>" />
										</a>
									<?php endif ?>
								<?php endif ?>
								<?= $ci->load->view('browse/view/partials/article_info') ?>
								<p class="article-summary"><?= $vd->esc($vd->m_content->summary) ?></p>
								<div class="marbot-15 html-content">
									<?= $ci->load->view('browse/view/html-content-pr', array('raw' => true)) ?>
								</div>
							</section>
							
							<?php if ($vd->m_content->is_premium): ?>
							<div class="page-break-avoid">
								<?= $ci->load->view('browse/view/partials/related_resources') ?>
							</div>							
							<?php endif ?>

							<div class="page-break-avoid">
								<?= $ci->load->view('browse/view/partials/additional_images') ?>
							</div>
							
							<div class="page-break-avoid">
								<?= $ci->load->view('browse/view/partials/tags_categories') ?>
							</div>

						</article>
						
					</div>
					
				</div>

				<div class="span4">
					<form class="collab-side-panel alert-info alert required-form">
						<input type="hidden" name="suid" id="suid" value="<?= $vd->esc($vd->suid) ?>" />
						<input type="hidden" name="sessid" id="sessid" value="<?= $vd->esc($vd->sessid) ?>" />
						<div><strong class="smaller">Name:</strong></div>
						<div><input class="in-text span12 required" data-required-name="Name" 
							type="text" name="name" id="name" value="<?= $vd->esc($vd->su_name) ?>" /></div>
						<div><strong class="smaller">Email:</strong></div>
						<div><input class="in-text span12 required" data-required-name="Email" 
							type="email" name="email" id="email" value="<?= $vd->esc($vd->su_email) ?>" /></div>
						<div><button type="submit" class="btn btn-flat-blue" id="continue"><strong>Continue</strong></button></div>						
					</form>
					<div id="collab-su" class="hidden collab-users-list">
						<div class="collab-side-panel">
						</div>
					</div>
					<div id="collab-instruction" class="relative">
						<div class="collab-overlay"></div>						
						<div class="collab-side-panel alert alert-success">
							<h3 class="nomarbot">How do I approve this version?</h3>
							<p class="marbot">
								Use the button below to approve
								for release.
							</p>
							<p>
								<button type="button" class="btn btn-flat-green"
									id="approve-version-button"><strong>Approve</strong></button>
							</p>
						</div>	
						<div class="collab-side-panel alert alert-info" id="how-do-i-comment">							
							<h3 class="nomarbot">How do I comment?</h3>
							<p class="marbot-15">Select the text you want to comment on and then 
								click the add button.</p>
							<h3 class="nomarbot">How do I invite others?</h3>
							<p class="marbot-10">
								Share this private preview with others using the link below via email or direct message. 
							</p>
							<div class="input-append marbot-2">
								<input type="text" class="in-text nomarbot" id="collab-link" 
									value="<?= $ci->website_url() ?>view/collab/<?= $vd->sessid ?>" />
								<button type="button" class="btn btn-flat-default stack-top in-btn" id="collab-link-copy">Copy</button>
							</div>
							<script>
	
								$(function() {

									var link = $("#collab-link");
									var copy = $("#collab-link-copy");
									link.on("focus", function() {
										link.select();
									});

									var clippy = new Clipboard(copy[0], {
										text: function(trigger) {
											return link.val();
										}
									});

									clippy.on("success", function(e) {
										var previous_val = link.val();
										link.val("(copied)");
										link.addClass("status-info-muted");
										link.addClass("strong");
										link.addClass("ta-center");
										setTimeout(function() {
											link.val(previous_val);
											link.removeClass("status-info-muted");			
											link.removeClass("strong");
											link.removeClass("ta-center");
										}, 1000);
									});

								});

							</script>
						</div>					
					</div>
					<div id="collab-annotations" class="hidden collab-annotations-list">
						<div class="collab-side-panel">
						</div>
					</div>
				</div>

			</div>
		</div>

		<style>

		<?php foreach (Model_Content_Collab::$colors as $idx => $color): ?>

			.s-annotation <?= sprintf('.annotate-color-%d', $idx) ?>,
			.su-profile <?= sprintf('.annotate-color-%d', $idx) ?>,
			<?= sprintf('.annotate-color-%d', $idx) ?> .annotator-hl {
				background-color: rgba(<?= comma_separate($color->background) ?>) !important;
				color: rgba(<?= comma_separate($color->text) ?>) !important;
			}

			<?= sprintf('.annotate-color-%d', $idx) ?> .view-web-images .collab-selectable-hl {
				background-color: rgba(<?= comma_separate($color->background) ?>) !important;
				border: 5px solid transparent;
				border-radius: 5px;
				margin-left: -5px;
				margin-top: -5px;
				margin-bottom: 14px;
				margin-right: 14px;
			}

			<?= sprintf('.annotate-color-%d', $idx) ?> .annotator-viewer .annotator-annotation div.annotator-user::before,
			<?= sprintf('.annotate-color-%d', $idx) ?> .conv-entry-color {
				background-color: rgba(<?= comma_separate($color->background) ?>) !important;
			}

		<?php endforeach ?>

		</style>

		<script>
							
			$(function() {

				var $window = $(window);
				var conversationModal = $("#conversation-modal");
				var conversationContent = conversationModal.find(".modal-content");
				var conversationFooter = conversationModal.find(".modal-footer");
				var conversationAddButton = $("#conversation-add-button");
				var conversationAddComment = $("#conversation-add-comment");
				conversationContent.addClass("conversation-content");
				var approveButton = $("#approve-version-button");
				var instruction = $("#collab-instruction");
				var instructionOverlay = instruction.find(".collab-overlay");
				var contentOverlay = $("#collab-content-overlay");
				var form = $("#continue").parents("form");
				var name = $("#name");
				var email = $("#email");
				var suid = $("#suid");
				var sessid = $("#sessid");
				var vSessid = sessid.val();
				var vSuid = suid.val();
				var users = $("#collab-su");
				var usersContainer = users.children(".collab-side-panel");
				var annotations = $("#collab-annotations");
				var annotationsContainer = annotations.children(".collab-side-panel");
				var contentContainer = $("#cv-container");
				var colors = <?= json_encode(Model_Content_Collab::$colors) ?>;
				var activeUser = null;
				var isAnnotatorCreateMode = false;
				var isOwner = <?= json_encode(Auth::is_user_online() && 
					Auth::user()->id == $vd->owner->id) ?>;
				var conversationAnnotation = null;
				var conversationEntriesCount = 0;
				var conversationHasReplied = false;

				marked.setOptions({
					gfm: true,
					tables: false,
					breaks: true,
					pedantic: false,
					sanitize: true
				});

				var set_color_class = function(color) {
					for (var i = 0; i < colors.length; i++) {
						var colorClass = ("annotate-color-" + i);
						contentContainer.toggleClass(colorClass, color == i);
					}
				};

				var annotator_clear = function() {
					$(".collab-selectable-hl").removeClass("collab-selectable-hl");
					if (contentContainer.hasClass("annotator-enabled"))
						contentContainer.annotator("destroy");
					contentContainer.removeClass("annotator-enabled");
				};

				var annotator_create_mode = function() {

					annotator_clear();
					isAnnotatorCreateMode = true;
					contentContainer.addClass("annotator-enabled");
					contentContainer.annotator();
					contentContainer.data("annotator").addPlugin("Store", {
						// The endpoint of the store on your server.
						prefix: <?= json_encode(sprintf('view/collab/annotator/%s/%s', $vd->sessid, $vd->suid)) ?>,
						showViewPermissionsCheckbox: false,
						showEditPermissionsCheckbox: false,
						loadFromSearch: {
							suid: <?= json_encode($vd->suid) ?>,
							id: -1,
						}
					});

				};

				var annotator_view_mode = function(user, id) {

					annotator_clear();
					isAnnotatorCreateMode = false;
					contentContainer.addClass("annotator-enabled");
					contentContainer.annotator();

					if (user.suid != activeUser.suid) {
						contentContainer.data("annotator").addPlugin("Permissions", {
							user: activeUser,
							showViewPermissionsCheckbox: false,
							showEditPermissionsCheckbox: false,
							userString: function(user) { return user.name; },
							userId: function(user) { return user.suid; },
							permissions: {
								read:   [],
								update: [],
								delete: [],
								admin:  [],
							},
						});
					};

					contentContainer.data("annotator").addPlugin("Store", {
						// The endpoint of the store on your server.
						prefix: <?= json_encode(sprintf('view/collab/annotator/%s/%s', $vd->sessid, $vd->suid)) ?>,
						loadFromSearch: {
							id: id === undefined ? -1 : id,
							suid: user.suid,
						}
					});

				};

				window.required_js.on_submit = function() {

					name.prop("disabled", true);
					email.prop("disabled", true);
					form.removeClass("alert-info");
					form.addClass("alert-success");
					email.addClass("nomarbot");
					form.remove();

					var pData = {
						email: email.val(),
						name: name.val(),
						sessid: vSessid,
						suid: vSuid
					};

					$.post("view/collab/update_su", pData, function(res) {
						
						instructionOverlay.fadeOut(750);
						contentOverlay.fadeOut(750);
						setTimeout(function() {
							instructionOverlay.remove();
							contentOverlay.remove();
						}, 750);

						users.show();
						annotations.show();
						render_users_list();
						render_annotations_list();
						activeUser = res.user;
						activeUser.suid = vSuid;
						set_color_class(activeUser.color);
						annotator_create_mode();

					});

					return false;

				};

				var render_users_list = function() {

					var pData = {
						sessid: vSessid,
						suid: vSuid
					};

					$.post("view/collab/list_su", pData, function(res) {

						if (!res || !res.users) return;
						setTimeout(render_users_list, 30000);
						usersContainer.empty();

						for (var suid in res.users) {

							var user = res.users[suid];
							user.suid = suid;

							var div = $.create("div").addClass("su-profile");
							div.toggleClass("has-annotations", user.count > 0);
							var colorClass = ("annotate-color-" + (user.color % colors.length));
							var color = $.create("span").addClass(colorClass).addClass("su-color");
							var name = $.create("span").addClass("su-name").text(user.name);

							if (user.approved) {
								div.addClass("approved");
								var i = $.create("i")
									.addClass("fa fa-check")
									.addClass(colorClass);
								div.append(i);
							}

							if (user.suid == vSuid && user.approved) 
								approveButton.parents(".alert").remove();

							div.append(color);
							div.append(name);
							usersContainer.append(div);

							div.on("click", (function(user) {

								return function() {

									set_color_class(user.color);
									if (user.suid == activeUser.suid)
									     annotator_create_mode(activeUser);
									else annotator_view_mode(user, -1);

								};

							})(user));

						}

					});

				};

				var render_annotations_list = function() {

					var pData = {
						sessid: vSessid,
						suid: vSuid
					};

					$.post("view/collab/list_annotations", pData, function(res) {
						
						if (!res || !res.annotations) return;
						setTimeout(render_annotations_list, 30000);
						var sortedAnnotations = [];

						for (var iSu in res.annotations) {
							for (var i in res.annotations[iSu]) {
								var annotation = res.annotations[iSu][i];
								if (!annotation) continue;
								if (!annotation.date_created) continue;
								annotation.user.suid = iSu;
								annotation.suid = iSu;
								annotation.id = i;
								sortedAnnotations.push(annotation);
							}
						}

						sortedAnnotations.sort(function(b, a)  {
							if (a.resolved && !b.resolved) return -1;
							if (b.resolved && !a.resolved) return +1;
							var aDate = new Date(a.date_created);
							var bDate = new Date(b.date_created);
							if (aDate < bDate) return -1;
							if (aDate > bDate) return +1;
							return 0;
						});

						annotationsContainer.empty();

						for (var i in sortedAnnotations) {
							
							var annotation = sortedAnnotations[i];
							var div = $.create("div").addClass("s-annotation");
							var colorClass = ("annotate-color-" + (annotation.user.color % colors.length));
							var color = $.create("span").addClass(colorClass).addClass("s-color");
							var name = $.create("span").addClass("s-name").text(annotation.user.name);
							var annotationText = annotation.text.length > 100 
								? annotation.text.substring(0, 100) + " ..."
								: annotation.text;
							var comment = $.create("span").addClass("s-comment").text(annotationText);
							var footer = $.create("span").addClass("s-footer");
							
							if (annotation.suid != vSuid) {
								var footer_view = $.create("a").addClass("s-footer-view");
								footer_view.text("View");
								footer.append(footer_view);
							}

							var footer_replies = $.create("a").addClass("s-footer-replies");
							footer_replies.text((annotation.replies ? "Discussion" : "Discuss"));
							footer.append(footer_replies);

							if (annotation.replies) {
								var count = $(formatString("<span> (<em>{{0}}</em>)<\/span>",
									annotation.replies+1));
								footer_replies.after(count);
							}													
							
							footer_replies.on("click", (function(annotation) {
								return function() {
									conversationModal.modal("show");
									loadConversationForAnnotation(annotation);
									return false;
								};
							})(annotation));

							if (isOwner) {

								if (!annotation.resolved) {

									var footer_resolve = $.create("a").addClass("s-footer-resolve");
									footer_resolve.text("Resolve");
									footer.append(footer_resolve);
									footer_resolve.on("click", (function(annotation) {

										return function() {

											var uri = "view/collab/annotator/{{sessid}}/{{suid}}/resolve/{{id}}";
											var message = "Do you want to mark this comment as resolved?";

											bootbox.confirm(message, function(confirmed) {

												if (!confirmed) return;
												uri = formatString(uri, {
													sessid: vSessid,
													suid: annotation.suid,
													id: annotation.id, 
												});

												$.get(uri, function() {
													render_annotations_list();
												});

											});

											return false;

										};

									})(annotation));

								}

								var footer_delete = $.create("a").addClass("s-footer-delete");
								footer_delete.text("Delete");
								footer.append(footer_delete);
								footer_delete.on("click", (function(annotation) {

									return function() {

										var uri = "view/collab/annotator/{{sessid}}/{{suid}}/delete/{{id}}";
										var message = "Are you sure you want to delete the comment?";

										bootbox.confirm(message, function(confirmed) {

											if (!confirmed) return;
											uri = formatString(uri, {
												sessid: vSessid,
												suid: annotation.suid,
												id: annotation.id, 
											});

											$.get(uri, function() {
												render_annotations_list();
												annotator_create_mode();
											});

										});

										return false;

									};

								})(annotation));

							}

							div.append(color);
							div.append(name);
							div.append(comment);
							div.append(footer);

							if (annotation.resolved) {
								var resolved = $.create("span");
								resolved.addClass("s-resolved");
								resolved.text("RESOLVED");
								div.append(resolved);
							}

							annotationsContainer.append(div);	

							div.on("click", (function(annotation) {

								return function() {

									set_color_class(annotation.user.color);
									annotator_view_mode(annotation.user, annotation.id);

								};

							})(annotation));

						}

					});

				};

				var loadConversationForAnnotation = function(annotation, offset) {

					var data = {
						sessid: vSessid,
						suid: annotation.suid,
						id: annotation.id,
						offset: offset
					};

					if (!offset) {
						conversationContent.empty();
						conversationContent.addClass("loader");
						conversationHasReplied = false;
					}

					$.post("view/collab/conversation", data, function(res) {
						if (!res) return;
						conversationContent.removeClass("loader");
						if (!offset) conversationEntriesCount = 0;
						renderConversation(annotation, res);
						if (!offset) conversationPoll();
					});

				};

				var renderConversation = function(annotation, res) {

					conversationAnnotation = annotation;
					for (var i = 0; i < res.length; i++) {
						var entry = res[i];
						if (entry.suid == vSuid)
							conversationHasReplied = true;
						var container = $.create("div").addClass("conversation-entry");
						var colorClass = ("annotate-color-" + (entry.color % colors.length));
						container.addClass(colorClass);
						var color = $.create("span").addClass("conv-entry-color");
						var name = $.create("span").addClass("conv-entry-name");
						var message = $.create("div").addClass("conv-entry-message")
							.addClass("html-content"); // to support markdown 
						var date = $.create("span").addClass("conv-entry-date");
						message.html(marked(entry.message));
						name.text(entry.name);
						date.text(entry.date);
						container.append(color);
						container.append(name);
						container.append(date);
						container.append(message);
						conversationContent.append(container);
						conversationEntriesCount++;
					}

					// scroll to the bottom of the discussion
					conversationContent.scrollTop(conversationContent.height());

				};

				conversationAddButton.on("click", function() {

					var data = {
						sessid: vSessid,
						annotation_suid: conversationAnnotation.suid,
						annotation_id: conversationAnnotation.id,
						message: conversationAddComment.val(),
						suid: vSuid,
					};

					conversationAddComment.val("");
					conversationContent.empty();
					conversationContent.addClass("loader");

					$.post("view/collab/conversation/add", data, function() {
						loadConversationForAnnotation(conversationAnnotation);
					});

				});

				conversationAddComment.on("keypress", function(ev) {
					if (ev.which === 13 && !ev.shiftKey) { 
						conversationAddButton.click();
						ev.preventDefault();
					}
				});

				conversationModal.on("hide.bs.modal", function() {
					conversationAnnotation = null;
				});

				approveButton.on("click", function() {

					var uri = "view/collab/approve/{{sessid}}/{{suid}}";
					var message = "Would you like to approve this version?";

					bootbox.confirm(message, function(confirmed) {

						if (!confirmed) return;

						uri = formatString(uri, {
							sessid: vSessid,
							suid: vSuid,
						});

						$.get(uri, function() {
							approveButton.parents(".alert").slideUp();
							render_users_list();
						});

					});

				});

				var conversationPoll = function() {

					if (conversationAnnotation) {
						loadConversationForAnnotation(conversationAnnotation,
							conversationEntriesCount);
						var pollDelay = conversationHasReplied ? 10000 : 30000;
						setTimeout(conversationPoll, pollDelay);
					}

				};

				contentContainer.on("annotationsLoaded", function() {

					var annotated = contentContainer.find(".annotator-hl");
					annotated.each(function() {
						// find each collab-selectable and apply to parent
						$(this).parent(".collab-selectable").parent()
							.addClass("collab-selectable-hl");
					});

					var position = false;
					var annotated0 = annotated.eq(0);
					var selectable_hl = annotated0.parent().parent();
					if (selectable_hl.hasClass("collab-selectable-hl"))
					     position = selectable_hl.offset();
					else position = annotated0.offset();
					if (!position) return;

					var top = position.top;
					if (top+100 > $window.height() || 
						   $window.scrollTop() > top-100)
						$window.scrollTop(top-100);

				});

				contentContainer.on("mousedown", function() {
					if (!isAnnotatorCreateMode && activeUser) {
						set_color_class(activeUser.color);
						annotator_create_mode();
					}
				});

				contentContainer.on("annotationCreated", function() {
					setTimeout(render_annotations_list, 1000);
				});

				contentContainer.on("annotationUpdated", function() {
					setTimeout(render_annotations_list, 1000);
				});

				contentContainer.on("annotationDeleted", function() {
					setTimeout(render_annotations_list, 1000);
				});

				// --------------------

				$window.on("load", function() {
					contentContainer.find(".collab-selectable").each(function() {
						var span = $(this);
						var element = span.prev(".content-media");
						if (!element.exists()) return;
						var offset = element.position();
						var height = element.outerHeight();
						var width = element.outerWidth();
						span.css("top", offset.top + height - 24);
						span.css("left", offset.left);
						span.css("width", width);
					});
				});

				contentContainer.find(".content-media").each(function() {
					var element = $(this);
					var span = $.create("span")
						.addClass("collab-selectable")
						.text("[attached media]");
					element.after(span);
					element.addClass("has-collab-selectable");
					element.parent().addClass("relative");
				});

				// --------------------

				$window.scrollTop(0);

				<?php if ($this->input->get('noname')): ?>
				// skip the name request by submitting automatically
				setTimeout(function() { $("#continue").click(); }, 0);
				<?php endif ?>

			});

		</script>

		<link rel="stylesheet" href="<?= $vd->assets_base ?>lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800,300italic,400italic,600italic,700italic" />
		<link rel="stylesheet" type="text/css" href="<?= $vd->assets_base ?>lib/annotator/annotator.css" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/base.css?<?= $vd->version ?>" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/browse.css?<?= $vd->version ?>" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/raw.css?<?= $vd->version ?>" />
		
		<?php 

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/annotator/annotator.js');
		$loader->add('lib/bootstrap3/js/bootstrap.min.js');
		$loader->add('lib/bootbox.min.js');
		$loader->add('lib/marked.min.js');
		$loader->add('lib/clipboard.js');
		$loader->add('js/base.js');
		$loader->add('js/required.js');		
		$render_basic = $ci->is_development();
		echo $loader->render($render_basic);

		?>

		<div id="eob" class="no-print">
			<?= $ci->load->view('partials/track-google-analytics') ?>
			<?php foreach ($ci->eob as $eob) 
				echo $eob; ?>
		</div>
		
	</body>
</html>
