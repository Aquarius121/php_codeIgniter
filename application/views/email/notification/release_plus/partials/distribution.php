<?php

$mDistBundle = $mContent->distribution_bundle();
$mExtras = Model_Content_Distribution_Extras::find($mContent->id);
$mReleasePlus = Model_Content_Release_Plus::find_all_content($mContent->id);

$vd->mContent = $mContent;
$vd->mDistBundle = $mDistBundle;
$vd->mExtras = $mExtras;
$vd->mReleasePlus = $mReleasePlus;

?>

<br><br>
The distribution information (<?= Date::format('H:i:s') ?> UTC):
<?= $this->load->view('admin/publish/distribution/details') ?>