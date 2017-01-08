<?php $cgst = Model_Content_Google_Search_Title::find($vd->m_content->id); ?>
<?php $count = Google_Search_Result_Count::count($title); ?>
<?php $title = $cgst ? $cgst->title : $vd->m_content->title; ?>
<strong><?= value_if(!$count, '??', $count) ?><sup>3</sup></strong>
<a href="<?= Google_Search_Result_Count::url($title) ?>">
	<em>Google</em></a>
<?php if (Auth::is_admin_online()): ?>
<a id="edit-google-search-title" 
	class="sources-configure" 
	data-id="<?= $vd->m_content->id ?>"></a>
<script>
	
$(function() {

	$("#edit-google-search-title").on("click", function() {
		var content_id = $(this).data("id");
		var message = "Enter new google search term";
		bootbox.prompt(message, function(new_title) {
			if (!new_title) return;
			var data = { content_title: new_title, content_id: content_id };
			$.post("manage/analyze/content/update_google_search_title", data, function() {
				window.location = window.location;
			});
		});
	});

});

</script>
<?php endif ?>