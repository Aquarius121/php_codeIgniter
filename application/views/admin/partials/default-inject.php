<script>
	
$(function() {

	$(".newsroom-panel").remove();
	$(".top-menu").remove();
	$("ul.breadcrumb").remove();
	$(".main-menu-bar").html($.create("div").addClass("null-menu"));
	$(".login-panel ul.dropdown-menu li").each(function() {
		var _this = $(this);
		var _href = $(this).children("a").attr("href");
		if (_href === "manage/companies") return;
		if (_href === "shared/logout") return;
		_this.remove();
	});

});

</script>