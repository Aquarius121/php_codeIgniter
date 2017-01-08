<script>	
defer(function() {

	$('#country').on('change', function(ev){
		var v = this.value;
		var loc = "admin/nr_builder/<?= $vd->nr_source ?>/all";
		loc += "?filter_country=" + v;
		loc += "<?= @$vd->search_filter ?>";
		location.href = loc;
	});

});
</script>