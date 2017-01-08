<div class="md-contact-full-profile">
	<div class="clearfix">
		<div class="profile-half">
			<div class="md-full-profile-name clearfix md-full-profile-section">
				<?php if ($result->picture): ?>
				<img src="<?= Stored_File::url_from_filename($result->picture->thumb) ?>"
					alt="<?= $result->first_name ?> <?= $result->last_name ?>"
					class="contact-picture-thumb" />
				<?php else: ?>
				<img src="<?= $vd->assets_base ?>im/media_database_thumb.png"
					class="contact-picture-thumb" />
				<?php endif ?>
				<?php if ($result->first_name || $result->last_name): ?>
					<div class="status-info">
						<strong><?= $vd->esc($result->first_name) ?>
							<?= $vd->esc($result->last_name) ?></strong>
					</div>
					<div class="muted text-muted"><?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
						$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?></div>
				<?php else: ?>
					<div>
						<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
							$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
					</div>
				<?php endif ?>
				<?php if (!empty($result->profile_data->phone)): ?>
				<div class="contact-phone-number">
					<?php $phone_obs = Media_Database_Contact_Access::phone_obfuscator(); ?>
					<?= $phone_obs->obfuscate((new Phone_Number($result->profile_data->phone))->formatted()); ?>
				</div>
				<?php elseif (!empty($result->phone)): ?>
				<div class="contact-phone-number">
					<?php $phone_obs = Media_Database_Contact_Access::phone_obfuscator(); ?>
					<?= $phone_obs->obfuscate((new Phone_Number($result->phone))->formatted()); ?>
				</div>
				<?php endif ?>
			</div>
			<?php if (!empty($result->profile_data->companies)
				&& count($result->profile_data->companies)): ?>
			<div class="md-full-profile-companies md-full-profile-section">
				<div><strong class="md-contact-profile-header">Companies</strong></div>
				<?php foreach ($result->profile_data->companies as $k => $company): ?>
					<?php if ($k >= 3) break; ?>
					<div>
						<?= $vd->esc($company) ?> (<span class="status-info-muted"><?= $vd->esc($result->profile_data->roles[$k]) ?></span>)
					</div>
				<?php endforeach ?>
				<?php if (count($result->profile_data->companies) > 3): ?>
					<div class="status-alternative">
						... and <?= count($result->profile_data->companies) - 3 ?> more. 
					</div>
				<?php endif ?>
			</div>
			<?php else: ?>
			<div class="md-full-profile-companies md-full-profile-section">
				<div><strong class="md-contact-profile-header">Companies</strong></div>
				<div><?= $vd->esc($result->company_name) ?></div>
			</div>
			<?php endif ?>
			<?php if ($result->beat_1 || $result->beat_2 || $result->beat_3): ?>
			<div class="md-full-beats md-full-profile-section">
				<div><strong class="md-contact-profile-header">Beats</strong></div>
				<div class="beats-list">
					<?php if ($result->beat_1): ?>
					<div><?= $vd->esc($result->beat_1->name) ?></div>
					<?php endif ?>
					<?php if ($result->beat_2): ?>
					<div><?= $vd->esc($result->beat_2->name) ?></div>
					<?php endif ?>
					<?php if ($result->beat_3): ?>
					<div><?= $vd->esc($result->beat_3->name) ?></div>
					<?php endif ?>
				</div>
			</div>
			<?php endif ?>	
			<?php if (!empty($result->profile_data->profile)): ?>
			<div class="md-full-profile-text md-full-profile-section">
				<div><strong class="md-contact-profile-header">Profile</strong></div>
				<?php if (strlen($result->profile_data->profile) > 500): ?>
					<div class="profile-text-short">
						<?= $vd->esc($vd->cut($result->profile_data->profile, 500)) ?>
						<a href="#" class="profile-text-short-more">more</a>.
					</div>
					<div class="profile-text-long">
						<?= $vd->esc($result->profile_data->profile) ?>
					</div>
				<?php else: ?>
					<?= $vd->esc($result->profile_data->profile) ?>
				<?php endif ?>
			</div>
			<?php endif ?>
			<?php if (!empty($result->profile_data->address)
				&& count($result->profile_data->address) > 1): ?>
			<div class="md-full-profile-address md-full-profile-section">
				<div><strong class="md-contact-profile-header">Address</strong></div>
				<?php $len_so_far = 0; ?>
				<?php foreach ($result->profile_data->address as $k => $line): ?>
					<?php if (isset($result->profile_data->address[$k+1]) &&
						$len_so_far + strlen($line) + strlen($result->profile_data->address[$k+1]) < 30): ?>
						<?= $vd->esc($line) ?>, 
						<?php $len_so_far += strlen($line); ?>						
					<?php else: ?>
						<?= $vd->esc($line) ?><br />
						<?php $len_so_far = 0; ?>
					<?php endif ?>
				<?php endforeach ?>
			</div>
			<?php else: ?>
			<div class="md-full-profile-address md-full-profile-section">
				<div><strong class="md-contact-profile-header">Address</strong></div>
				<?php if ($result->locality): ?>
					<div><?= $vd->esc($result->locality->name) ?></div>
				<?php endif ?>
				<?php if ($result->region): ?>
					<div><?= $vd->esc($result->region->name) ?></div>
				<?php endif ?>
				<?php if ($result->country): ?>
					<div><?= $vd->esc($result->country->name) ?></div>
				<?php endif ?>
			</div>
			<?php endif ?>
			<?php if (!empty($result->profile_data->languages)
				&& count($result->profile_data->languages)): ?>
			<div class="md-full-profile-languages md-full-profile-section">
				<div><strong class="md-contact-profile-header">Languages</strong></div>
				<?= $vd->esc(comma_separate($result->profile_data->languages, true)) ?>
			</div>
			<?php endif ?>
		</div>
		<div class="profile-half">
			<?php if (!empty($result->profile_data->twitter) || !empty($result->profile_data->linkedin)): ?>
				<div class="md-full-profile-social md-full-profile-section row-fluid">
					<?php if (!empty($result->profile_data->twitter)): ?>
						<div class="span6 col-lg-6">
							<div><strong class="md-contact-profile-header">Twitter</strong></div>
							<i class="fa fa-fw fa-twitter-square"></i> <a target="_blank" href="<?= 
								$vd->esc(Social_Twitter_Profile::url($result->profile_data->twitter))
								?>">@<?= $vd->esc($result->profile_data->twitter) ?>
							</a>
						</div>
					<?php endif ?>
					<?php if (!empty($result->profile_data->linkedin)): ?>
						<div class="span6 col-lg-6">
							<div><strong class="md-contact-profile-header">LinkedIn</strong></div>
							<i class="fa fa-fw fa-linkedin-square"></i> <a target="_blank" href="<?= 
								$vd->esc(Social_Linkedin_Profile::url($result->profile_data->linkedin))
								?>"><?= $vd->esc($result->first_name) ?>
								<?= $vd->esc($result->last_name) ?>
							</a>
						</div>
					<?php endif ?>
				</div>
			<?php elseif (!empty($result->twitter)): ?>	
				<div class="md-full-profile-social md-full-profile-section row-fluid">
					<div class="span6 col-lg-6">
						<div><strong class="md-contact-profile-header">Twitter</strong></div>
						<i class="fa fa-fw fa-twitter-square"></i> <a target="_blank" href="<?= 
							$vd->esc(Social_Twitter_Profile::url($result->twitter))
							?>">@<?= $vd->esc($result->twitter) ?>
						</a>
					</div>
				</div>
			<?php endif ?>
			<?php if (!empty($result->profile_data->twitter)): ?>
			<div class="md-full-profile-tweets md-full-profile-section" id="md-full-profile-tweets"></div>
			<?php elseif (!empty($result->twitter)): ?>	
			<div class="md-full-profile-tweets md-full-profile-section" id="md-full-profile-tweets"></div>	
			<?php endif ?>
		</div>
	</div>
	<div class="profile-lower clearfix">
		<div class="profile-lower-half">
			<div class="md-full-profile-section">
				<div class="marbot"><strong class="md-contact-profile-header">Personal Notes</strong></div>
				<textarea class="form-control in-text" id="md-full-profile-notes"><?= $vd->esc($result->notes) ?></textarea>
			</div>
		</div>
		<div class="profile-lower-half">
			<div class="md-full-profile-section">
				<div class="marbot"><strong class="md-contact-profile-header">Campaign History</strong></div>
				<?php if ($result->campaign_history): ?>
				<?php foreach ($result->campaign_history as $campaign): ?>
				<div><?= Date::out($campaign->date_send)->format('Y-m-d'); ?>
					<span class="muted text-muted"><?= Date::out($campaign->date_send)->format('H:i'); ?></span>
					<a href="<?= $campaign->newsroom->url("manage/contact/campaign/edit/{$campaign->id}") ?>"
						class="status-info-muted"><?= $vd->esc($campaign->name) ?></a></div>
				<?php endforeach ?>
				<?php else: ?>
					<div class="muted text-muted">No Campaigns</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>

<script>
	
$(function() {

	var contact_id = <?= json_encode($result->id) ?>;
	var contact_profile_id = <?= json_encode($result->profile->id) ?>;
	var first_half = $(".profile-half:first");
	var second_half = $(".profile-half:last");
	var tweets = $("#md-full-profile-tweets");
	var container = $(".md-contact-full-profile");

	if (!tweets.size()) {
		var profile = $(".md-full-profile-text").detach();
		second_half.append(profile);
	}

	$(".profile-text-short-more").on("click", function(ev) {
		ev.preventDefault();
		$(".profile-text-short").hide();
		$(".profile-text-long").show();
	});

	var create_twitter_feed = function() {
		twttr.widgets.createTimeline("613124944952737792", $("#md-full-profile-tweets").get(0), {
			screenName: <?= json_encode($result->profile_data->twitter) ?>,
			chrome: "noheader nofooter noborders noscrollbar",
			height: parseInt(first_half.height(), 10)
				- parseInt(second_half.height(), 10)
		});
	};

	setTimeout(function() {
		if (first_half.height() > 0) {
			create_twitter_feed();
			container.parent().scrollTop(0);
			return;
		}
		setTimeout(arguments.callee, 50);
	}, 50);

	var notes_box = $("#md-full-profile-notes");
	notes_box.on("change", function() {
		var notes = notes_box.val();
		var data = { contact_profile_id: contact_profile_id, notes: notes };
		$.post("<?= $ci->uri->segment(1) ?>/contact/media_database/set_profile_notes", data);
	});

});

</script>