<?php if ($vd->collab_rd->invite_message): ?>
<?= $vd->esc($vd->collab_rd->invite_message) ?>
<?php else: ?>
Hello {{name}}, 

I just created a Press Release draft at Newswire.com. Your feedback is important before we finalize the content. Simply click the link below and share your input. 

{{link}}

We are aiming to have this ready for the <?= 
	Date::out($vd->m_content->date_publish)->format('jS') ?> of <?= 
	Date::out($vd->m_content->date_publish)->format('F') ?>, so please check as soon as possible. 

Thank you.
<?php endif ?>