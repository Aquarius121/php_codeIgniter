We have received the <?= strtolower(Model_Content::full_type($content->type)) ?> submission 
detailed below. It has been stored in our database and scheduled for release.
<br /><br />
<img src="<?= $vd->assets_base ?>im/icon-arrow-right-blue.png" alt="" border="0" 
	style="border:0;" /> &nbsp; 
<span style="color:#797979; font-weight:500;"><?= $vd->esc($content->title) ?></span>, 
<span style="color:#999999;">(<?= Date::out($content->date_publish, $timezone)->format('M j, Y') ?>)</span>
