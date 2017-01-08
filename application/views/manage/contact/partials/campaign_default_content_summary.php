<?= $ci->load->view('manage/partials/email-template-css') ?>

<div class="ei-from-content">

	<?php if ($m_content->summary): ?>
		<p><?= $vd->esc($m_content->summary) ?></p>
	<?php else: ?>
		<p><?= $vd->cut(html_entity_decode(
			strip_tags($m_content->content)), 250) ?></p>
	<?php endif ?>

	<p>
		To learn more, please 
		<a href="((tracking-link))">click here</a>.
	</p>

</div>	