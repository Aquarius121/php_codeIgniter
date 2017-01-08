<div class="md-contact-hover-profile">
	<div class="md-hover-profile-name clearfix md-hover-profile-section">
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
		<?php endif ?>
	</div>
	<div class="row-fluid">	
		<?php if (!empty($result->profile_data->companies)
			&& count($result->profile_data->companies)): ?>
		<div class="md-hover-profile-text md-hover-profile-section span6 col-lg-6">
			<div><strong class="md-contact-profile-header">Companies</strong></div>
			<?php foreach ($result->profile_data->companies as $k => $company): ?>
				<?php if ($k >= 3) break; ?>
				<div>
					<div><?= $vd->esc($company) ?> </div>
					<span class="status-info-muted"><?= $vd->esc($result->profile_data->roles[$k]) ?></span>
				</div>
			<?php endforeach ?>
			<?php if (count($result->profile_data->companies) > 3): ?>
				<div class="status-alternative">
					... and <?= count($result->profile_data->companies) - 3 ?> more. 
				</div>
			<?php endif ?>
		</div>
		<?php endif ?>
		<?php if (!empty($result->profile_data->beats)
			&& count($result->profile_data->beats)): ?>
		<div class="md-hover-beats md-hover-profile-section span6 col-lg-6">
			<div><strong class="md-contact-profile-header">Beats</strong></div>
			<?php foreach ($result->profile_data->beats as $k => $beat): ?>
				<?php if ($k >= 3) break; ?>
				<div><?= $vd->esc($beat) ?></div>
			<?php endforeach ?>
		</div>
		<?php endif ?>	
	</div>
	<?php if (!empty($result->profile_data->profile)
		&& count($result->profile_data->profile)): ?>
	<div class="md-hover-profile-text md-hover-profile-section col-lg-12">
		<div><strong class="md-contact-profile-header">Profile</strong></div>
		<?= $vd->esc($result->profile_data->profile) ?>
	</div>
	<?php endif ?>
</div>