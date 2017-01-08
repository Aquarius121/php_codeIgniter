<?php $dt_created = Date::out($m_wr_session->date_created, $m_newsroom->timezone); ?>

<h1 style="color:#c8483f; font-size:20px; font-weight:500; margin:0; padding:0 30px 0 30px; text-align:center;
	font-family:Helvetica, Arial, sans-serif;">You have not finished the writing order that you started.</h1>
<br />

You need to fill in all the requested information and then submit the order.

<br /><br />

<a href="<?= $m_newsroom->url("manage/writing/process/{$m_wr_session->id}/1") ?>" title="" target="_blank" 
	style="color:#1357a8;"><?= $m_newsroom->url("manage/writing/process/{$m_wr_session->id}/1") ?></a>

<br /><br />

Code: <span style="color:#f79432;"><?= $m_wr_session->nice_id() ?></span>
<br />
Date: <span style="color:#f79432;"><?= $dt_created->format('M j, Y H:i') ?></span>