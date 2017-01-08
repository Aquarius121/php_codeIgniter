<script>

(function() {

	var callFunction = window.parent.CKEDITOR.tools.callFunction;
	callFunction(<?= json_encode((int) $ci->input->get('CKEditorFuncNum')) ?>, 
		<?= json_encode($vd->img_url) ?>, <?= json_encode($vd->upload_error) ?>);

})();
	
</script>