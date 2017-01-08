<style>
.pad-right20
{
	padding-right: 20px;
}

.border-bottom-dotted
{
	border-bottom: 1px dotted #888888;
}

.release-type.press-contact {
  background-color: #1357a8;
}

.release-type {
	border-radius: 3px;
	color: #fff;
	display: table;
	font-weight: bold;
	padding: 2px 5px;
	margin-top: 10px;
	clear: both;
}
</style>
<div id="feedback_area"></div>
	<div class="content">		
		<h4 class="marbot-20">Company Contacts - <?= $vd->company_name ?></h4>
		<?php foreach ($vd->results as $result): ?>
			<div class="row-fluid clear">
				<div class="span12 marbot-20 padbot-20 border-bottom-dotted">
					<div class="span3">
						<?php if (!empty($result->image_url)): ?>
							<img src="<?= $result->image_url ?>">
						<?php else: ?>
							<img src="assets/im/contact_image_162.png">
						<?php endif ?>
					</div>
					<div class="span9">
						<h4><?= $vd->esc($result->name) ?></h4>
						
						<?php if ($result->is_press_contact): ?>
							<span class="release-type press-contact marbot-5">Press Contact</span>
						<?php endif ?>

						<?php if (!empty($result->title)): ?>
							<?= $vd->esc($result->title) ?><br />
						<?php endif ?>

						<?php if (!empty($result->area_of_specialization)): ?>
							<?= $vd->esc($result->area_of_specialization) ?><br />
						<?php endif ?>
						
						<?php if (!empty($result->email)): ?>
							<a href="mailto:<?= $vd->esc($result->email) ?>">
								<i class="icon-envelope"></i> <?= $vd->esc($result->email) ?>
							</a><br />
						<?php endif ?>
						
						<?php if (!empty($result->phone)): ?>
							<a href="tel:<?= $vd->esc($result->phone) ?>">
								<i class="icon-phone"></i> <?= $vd->esc($result->phone) ?>
							</a><br />
						<?php endif ?>

						<?php if (@$result->facebook || @$result->twitter || @$result->linkedin ||
								@$result->skype): ?>
							
							<?php if (@$result->facebook): ?>
								<a class="pad-right20" target='_blank' href="<?= $vd->esc(
									Social_Facebook_Profile::url($result->facebook)) ?>">
									<i class="icon-facebook"></i> <?= $vd->esc($result->facebook) ?>
								</a>
							<?php endif ?>

							<?php if (@$result->twitter): ?>
								<a class="pad-right20" target='_blank' href="<?= $vd->esc(
									Social_Twitter_Profile::url($result->twitter)) ?>">
									<i class="icon-twitter"></i> <?= $vd->esc($result->twitter) ?>
								</a>
							<?php endif ?>

							<?php if (@$result->linkedin): ?>
								<a class="pad-right20" target='_blank' href="<?= $vd->esc(
									Social_Linkedin_Profile::url($result->linkedin)) ?>">
									<i class="icon-linkedin"></i> <?= $vd->esc($result->linkedin) ?>
								</a>
							<?php endif ?>

							<?php if (@$result->skype): ?>
								<a class="pad-right20" href="skype:<?= $vd->esc($result->skype) ?>">
									<i class="icon-skype"></i> <?= $vd->esc($result->skype) ?>
								</a>
							<?php endif ?>
							<br />
						<?php endif ?>

						<?php if (!empty($result->description)): ?>
							<br /><?= htmlspecialchars_decode($vd->esc($result->description)) ?>
						<?php endif ?>



					</div>
				</div>
			</div>
		<?php endforeach ?>
				
	</div>
</div>

