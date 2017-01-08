<button id="generate-code-btn" type="button"
	<?= value_if_test(!Auth::user()->writing_credits(), 'disabled') ?>
	class="btn">Generate Code</button>
<script>
	
$(function() {
	
	var confirm_message = "This will consume a writing credit and generate a new\
		writing order code. Please confirm that you wish to continue.";		
	var failed_message = "Failed to generate a new code.\
		Check that you have credits available.";
	
	$("#generate-code-btn").on("click", function() {
		
		bootbox.confirm(confirm_message, function(confirmed) {
			
			if (!confirmed) return;
			
			$.post("reseller/publish/credit_to_code", function(result) {
				
				if (!result) {
					bootbox.alert(failed_message);
					return;
				}
				
				var e = $.create("input");
				e.addClass("code-text");
				e.val(result);
				
				bootbox.alert(e);
				e.focus().select();
				
			});
			
		});
	});
	
});

</script>