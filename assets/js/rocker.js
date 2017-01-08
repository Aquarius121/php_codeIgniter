$(function() {
	
	var rockers = $(".rocker");
	rockers.each(function() {

		var rocker = $(this);

		rocker.selected = null;
		rocker.inputs = rocker.find("input");
		rocker.options = [];

		if (!rocker.inputs.length) 
			return;

		var options_container = $.create("span");
		options_container.addClass("options");
		rocker.append(options_container);

		rocker.inputs.each(function() {

			var input = $(this);
			var option = $.create("a");
			option.input = input;
			option.attr("class", input.attr("class"));
			option.text(input.data("label"));
			option.addClass("option");
			if (input.is(":checked")) 
				rocker.selected = option;
			if (!rocker.selected && input.hasClass("default"))
				rocker.selected = option;
			options_container.append(option);
			rocker.options.push(option);

			option.on("click", (function(option) {

				return function() {
					rocker.selected.removeClass("selected");
					rocker.selected.input.prop("checked", false);
					option.input.prop("checked", true);
					option.addClass("selected");
					rocker.selected.input.trigger("deselect");
					option.input.trigger("select");
					rocker.selected = option;
				};

			})(option));
			
		});

		if (!rocker.selected)
			rocker.selected = rocker.options[0];
		rocker.selected.addClass("selected");

	});

});