<script>
		
window._defer_callback_jQuery = [];
window._defer_callback = [];

window.$ = function(callback) {
	if (typeof callback === 'function')
		_defer_callback_jQuery.push(callback);
};

window.defer = function(callback) {
	if (typeof callback === 'function')
		_defer_callback.push(callback);
};

</script>