<ul class="pop-over-menu" role="menu"
	id="list-options-menu">
	<li>
		<div class="checkbox-container-box marbot">
			<label class="checkbox-container louder">
				<input type="checkbox" id="options-unique-only" value="1" />
				<span class="checkbox"></span>
				Unique Email Addresses
			</label>
			<p class="muted text-muted">
				Don't show contacts more than once when they 
				work for multiple companies. This ensures that 
				each unique email address will only appear once. 
			</p>
		</div>
	</li>
	<li>
		<div class="checkbox-container-box marbot">
			<label class="checkbox-container louder">
				<input type="checkbox" id="options-pictures-only" value="1" />
				<span class="checkbox"></span>
				Contacts with Pictures
			</label>
			<p class="muted text-muted">
				Only show contacts who have a picture.
			</p>
		</div>
	</li>
</ul>

<script>
	
$(function() {

	var unique_only = $("#options-unique-only");
	var pictures_only = $("#options-pictures-only");
	var client = window.__media_database_client;

	unique_only.on("change", function() {
		client.options.unique_only = unique_only.is(":checked");
		window.__media_database_refresh();
	});

	pictures_only.on("change", function() {
		client.options.pictures_only = pictures_only.is(":checked");
		window.__media_database_refresh();
	});

});

</script>