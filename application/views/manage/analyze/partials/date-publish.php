<div class="alert alert-alternative">
	<strong>Attention!</strong>
	Most traffic arrives within the first few days of release. 
	Click <a href="manage/analyze/content/view/<?= $vd->m_content->id ?>?date_start=<?= 
		Date::days(-7, $vd->m_content->date_publish)->format('Y-m-d') ?>">here</a>
	to view data from the date of release. 
</div>