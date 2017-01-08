<form action="planner/save/<?= $vd->planner->id ?>" method="post" id="planner-form" class="marbot-50 required-form">	

	 <?php 

		$loader = new Assets\JS_Loader(
			$ci->conf('assets_base'), 
			$ci->conf('assets_base_dir'));
		$loader->add('lib/jquery.deserialize.js');
		$render_basic = $ci->is_development();
		$ci->add_eob($loader->render($render_basic));

	?>
	<script>

	$(function() {

		$(document).on("click", ".radio-js a", function(ev) {
			ev.preventDefault();
			var a = $(this);
			var r = a.parents(".radio-js");
			a.find("input[type=radio]")
				.prop("checked", true)
				.trigger("change");
		});

		$(document).on("change", ".radio-js a input", function(ev) {
			var i = $(this);
			if (!i.is(":checked")) return;
			var a = i.parents(".radio-js a");
			var r = a.parents(".radio-js");
			r.find("a").removeClass("selected");
			a.addClass("selected");
		});	

		$(document).on("click", ".checkbox-js a", function(ev) {
			ev.preventDefault();
			var a = $(this);
			var r = a.parents(".checkbox-js");
			var i = a.find("input[type=checkbox]");
			i.prop("checked", !i.is(":checked"))
			i.trigger("change");
		});

		$(document).on("change", ".checkbox-js a input", function(ev) {
			var i = $(this);
			var a = i.parents(".checkbox-js a");
			a.toggleClass("selected", i.is(":checked"));
		});
	});

	$(function() {

		var form = $("#planner-form");
		var data = <?= json_encode($vd->rdata) ?>;
		form.deserialize(data);

		setTimeout(function() {			
			form.find("input").trigger("change");
			form.find("select").trigger("change");
		}, 0);

	});

	</script>

	<?= $ci->load->view(sprintf('website/pages/planner/%s', $vd->step)) ?>

</form>