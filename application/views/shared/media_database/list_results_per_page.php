<select class="selectpicker show-menu-arrow nomarbot dropup smaller" id="select-results-per-page">
	<option value="10">10 Results Per Page</option>
	<option value="20" selected>20 Results Per Page</option>
	<option value="50">50 Results Per Page</option>
	<option value="100">100 Results Per Page</option>
</select>
<script>

$(function() {

	var results_per_page = $("#select-results-per-page");
	var client = window.__media_database_client;
	results_per_page.val(client.options.chunk_size);
	results_per_page.on_load_select();
	results_per_page.on("change", function() {
		client.options.chunk_size = results_per_page.val();
		window.__media_database_refresh();
	});

	$('.selectpicker').selectpicker({
		container: document.body,
		size: 10
	});

}); 

</script>