<?= $ci->load->view('manage/partials/email-template-css') ?>

<div class="ei-from-content">

	<span class="ei-content-view-pixel">
		<img src="((content-view-pixel))" width="1" height="1" alt="" />
	</span>

	<div class="ei-html-content">		
		<?php if ($template->id === 'first_look'): ?>
			<?= $ci->load->view('manage/contact/partials/pitch-templates/tpl-first-look') ?>
		<?php endif; ?>

		<?php if ($template->id === 'exclusive'): ?>
			<?= $ci->load->view('manage/contact/partials/pitch-templates/tpl-exclusive') ?>
		<?php endif; ?>

		<?php if ($template->id === 'media_advisory'): ?>
			<?= $ci->load->view('manage/contact/partials/pitch-templates/tpl-media-advisory') ?>
		<?php endif; ?>
	</div>

</div>	