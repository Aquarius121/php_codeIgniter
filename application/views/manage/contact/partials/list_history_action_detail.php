<div class="cl-action-title">
	<?php if ($action->type == Model_Contact_List_Action::TYPE_IMPORT_FROM_CSV): ?>
		Imported contacts from CSV:
	<?php elseif ($action->type == Model_Contact_List_Action::TYPE_ADD_CONTACT_FROM_MDB): ?>
		Added to list from media database filters: 
	<?php elseif ($action->type == Model_Contact_List_Action::TYPE_CREATE_LIST_FROM_MDB): ?>
		Created list from media database filters: 
	<?php endif ?>
</div>

<?php foreach ($action->details as $detail): ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_CSV_STORED_FILE_ID): ?>
		<div class="cl-action-detail"><span class="status-alternative-2"><?= $detail->csv_file_name ?></span> / 
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_CSV_NUM_CONTACTS_IMPORTED): ?>
		<span class="status-true"><?= $detail->value ?> contacts.</span></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_NUM_CONTACTS_ADDED): ?>
		<div class="cl-action-detail"><span class="status-alternative">Contacts added: <?= $detail->value ?></span></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_BEAT_ID): ?>
		<div class="cl-action-detail">Beat: <?= $vd->esc($detail->beat_name) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_MEDIA_TYPE_ID): ?>
		<div class="cl-action-detail">Media Type: <?= $vd->esc($detail->media_type) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_ROLE_ID): ?>
		<div class="cl-action-detail">Role: <?= $vd->esc($detail->role) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_COVERAGE_ID): ?>
		<div class="cl-action-detail">Coverage: <?= $vd->esc($detail->coverage) ?></div>
	<?php endif ?>

	

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_COUNTRY_ID): ?>
		<div class="cl-action-detail">Country: <?= $vd->esc($detail->country_name) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_REGION_ID): ?>
		<div class="cl-action-detail">Region: <?= $vd->esc($detail->region_name) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_LOCALITY_ID): ?>
		<div class="cl-action-detail">Locality: <?= $vd->esc($detail->locality_name) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_FILTER_SEARCH_TEXT_ID): ?>
		<div class="cl-action-detail">Search: <?= $vd->esc($detail->search_text) ?></div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_OPTION_UNIQUE_EMAILS): ?>
		<div class="cl-action-detail">Unique emails only: Yes</div>
	<?php endif ?>

	<?php if ($detail->detail == Model_Contact_List_Action_Detail::DETAIL_MDB_OPTION_CONTACT_WITH_PICS): ?>
		<div class="cl-action-detail">Contact with pics only: Yes</div>
	<?php endif ?>

<?php endforeach ?>