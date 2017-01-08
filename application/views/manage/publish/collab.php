<div class="container-fluid collab-wizard">
	<div class="row">

		<div class="col-md-5 col-sm-12">
			<div class="panel">
				<div class="panel-body">
					<h3 class="panel-h3">Share, collaborate and get feedback (<span class="status-beta">BETA</span>)</h3>
					<p>
						Share the link below with your team and stakeholders. 
						Easily review, comment and collaborate your press release before its distributed online. 
						Simply add emails of your team members and invite.
					</p>
					<div class="input-group">
						<input type="text" class="form-control" id="collab-link" 
							value="<?= $ci->website_url($vd->m_collab->url()) ?>" />
						<span class="input-group-btn">
							<button type="button" class="btn btn-default" id="collab-link-copy">Copy</button>
						</span>
					</div>
				</div>
			</div>
			<div class="panel">
				<div class="panel-body">
					<h3 class="panel-h3">Selected Content</h3>
					<div class="marbot-5">
						<span class="label-class status-info"><?= $vd->esc(Model_Content::full_type($vd->m_content->type)) ?></span>
						<span><?= $vd->esc($vd->m_content->title) ?></span>
					</div>
					<div class="status-muted">Updated: <?= Date::out($vd->m_content->date_updated)->format('M d, Y H:i') ?></div>
				</div>
			</div>
			<?php if (count($vd->previous)): ?>
				<div class="panel">
					<div class="panel-body">
						<div class="collab-revisions">
							<h3>Version History</h3>
							<div class="clearfix marbot">
								<?php foreach ($vd->previous as $k => $prev): ?>
									<a class="collab-revision <?= value_if_test(!$k, 'latest') ?>"
										href="view/collab/<?= $prev->id ?>" target="_blank">
										<div class="revision-trash" data-id="<?= $prev->id ?>">
											<i class="fa fa-trash"></i>
										</div>
										<div class="revision-name">
											<strong>
												<?php if ($k === 0): ?>
												<strong class="status-true revision-active">ACTIVE</strong>
												&nbsp;V<?= (int) $prev->version ?>
												<?php else: ?>
													Version <?= (int) $prev->version ?>
												<?php endif ?>												
											</strong> 
										</div>
										<div class="revision-date">
											<?= Date::out($prev->date_created)->format('Y-m-d H:i') ?>
										</div>
									</a>
								<?php endforeach ?>
							</div>
							<div>
								<a class="btn btn-primary" href="manage/publish/collab/<?= $vd->m_content->id ?>/1">New Version</a>
							</div>
						</div>
					</div>
				</div>
			<?php endif ?>
		</div>

		<div class="col-md-7 col-sm-12">
			<div class="panel">
				<form class="panel-body" action="manage/publish/collab/send" method="post">
					<h3 class="panel-h3">Invite team members.</h3>
					<input type="hidden" name="sessid" value="<?= $vd->esc($vd->m_collab->id) ?>" />
					<?php $cCount = 0; ?>
					<?php foreach ($vd->users as $user): ?>
						<?php $cCount++; ?>
						<div class="clearfix">
							<div class="collab-name">
								<input class="form-control status-info-muted" type="text"
									value="<?= $vd->esc($user->name) ?>" disabled />
							</div>
							<div class="collab-email">
								<input class="form-control status-info-muted" type="email"
									value="<?= $vd->esc($user->email) ?>" disabled />
							</div>
						</div>
					<?php endforeach ?>
					<?php foreach ($vd->invite_suggests as $user): ?>
						<?php $cCount++; ?>
						<div class="clearfix">
							<div class="collab-name">
								<input class="form-control" type="text"
									name="name[]" placeholder="Name"
									value="<?= $vd->esc($user->name) ?>" />
							</div>
							<div class="collab-email">
								<input class="form-control" type="email"
									name="email[]" placeholder="Email"
									value="<?= $vd->esc($user->email) ?>" />
							</div>
						</div>
					<?php endforeach ?>
					<?php for ($i = 0; $i < 2 || $cCount < 8; $i++, $cCount++): ?>
						<div class="clearfix">
							<div class="collab-name">
								<input class="form-control" type="text"
									name="name[]" placeholder="Name" />
							</div>
							<div class="collab-email">
								<input class="form-control" type="email"
									name="email[]" placeholder="Email" />
							</div>
						</div>
					<?php endfor ?>
					<textarea name="message" class="form-control marbot" rows="8" placeholder="Message"><?= 
						$ci->load->view('manage/publish/partials/collab-message') ?></textarea>
					<button type="submit" class="btn btn-success">
						Send Invitations
					</button>
				</form>
			</div>
		</div>

	</div>
</div>

<?php 

$loader = new Assets\JS_Loader(
	$ci->conf('assets_base'), 
	$ci->conf('assets_base_dir'));
$loader->add('lib/bootbox.min.js');
$loader->add('lib/clipboard.js');
$render_basic = $ci->is_development();
echo $loader->render($render_basic);

?>

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

	var messageConfirm = "Are you sure you want to remove the selected revision\
		<br>and delete all comment history?";
	var messageSuccess = "The revision has been deleted.";
				
	var trash = $(".revision-trash");
	trash.on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		ev.preventDefault();
		bootbox.confirm(messageConfirm, function(res) {
			if (!res) return;
			var data = {};
			data.confirmed = 1;
			data.id = id;
			$.post("manage/publish/collab/delete", data, function() {
				bootbox.alert(messageSuccess, function() {
					_this.parents(".collab-revision").remove();
				});
			});
		});
	});

});

</script>