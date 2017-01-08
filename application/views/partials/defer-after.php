<script>
		
if (window._defer_callback_jQuery && 
	 window._defer_callback_jQuery.length) {
	for (var i = 0; i < window._defer_callback_jQuery.length; i++)
		$(window._defer_callback_jQuery[i]);
}

if (window._defer_callback && 
	 window._defer_callback.length) {
	for (var i = 0; i < window._defer_callback.length; i++)
		window._defer_callback[i]();
}

</script>