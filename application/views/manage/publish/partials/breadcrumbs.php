<ul class="breadcrumb">
	<li><a href="manage/publish">Distribution</a> <span class="divider">&raquo;</span></li>
	<?php if ($vd->m_content && $vd->m_content->title): ?>
	<li><a href="manage/publish/<?= $ci->uri->segment(3) ?>"><?= 
		Model_Content::full_type_plural($ci->uri->segment(3)) ?></a> 
		<span class="divider">&raquo;</span></li>
	<li class="active"><?= $vd->esc($vd->cut($vd->m_content->title, 50)) ?></li>
	<?php else: ?>
	<li><a href="manage/publish/<?= $ci->uri->segment(3) ?>"><?= 
		Model_Content::full_type_plural($ci->uri->segment(3)) ?></a> 
		<span class="divider">&raquo;</span></li>
	<li class="active">Submit <?= 
		Model_Content::full_type($ci->uri->segment(3)) ?></li>
	<?php endif ?>
</ul>