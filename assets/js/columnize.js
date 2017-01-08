$.fn.columnize = function(options) {
	
	if (typeof options !== "object")
		options = new Object;
	if (options.columns === undefined)
		options.columns = 3;
	if (options.margin === undefined)
		options.margin = 0;
	if (options.harder === undefined)
		options.harder = false;

	var calc_col_width = function(width, col_limit) {
		return Math.floor((width - ((col_limit - 1) * 
			options.margin)) / col_limit)
	};

	var create_columns = function() {
		for (var i = 0; i < col_limit; i++) {
			var col = columns_arr[i] = $.create("div");
			if (i < (col_limit - 1))
				col.css("margin-right", options.margin);
			col.css("width", col_width);
			col.css("float", "left");
			_this.append(col);
		}
	};

	var shortest_column = function(columns_arr) {
		var min = Number.MAX_VALUE;
		var col = null;
		for (var i = 0; i < col_limit; i++) {
			var height = columns_arr[i].height();
			if (height < min) {
				col = columns_arr[i];
				min = height;
			}
		}
		return col;
	};

	var append_children = function() {
		for (var i = 0; i < children_arr.length; i++) {
			var child = children_arr[i];
			if (options.harder) 
			     col = shortest_column(columns_arr);
			else col = columns_arr[col_idx++ % col_limit];
			col.append(child);
			child.css("float", "none");
			child.css("width", "auto");
		};
	};

	var calculate = function() {
		col_width = calc_col_width(width, col_limit);	
		while (options.width && col_width < options.width && col_limit > 1) 
			col_width = calc_col_width(width, --col_limit);
	};

	var _this = $(this[0]);
	var children_arr = [];
	var columns_arr = [];
	var col_idx = 0;

	var width = _this[0].getBoundingClientRect().width;
	var col_limit = options.columns;
	var col_width;

	var children = _this.children();
	children.each(function() {
		var child = $(this);
		children_arr.push(child);
	});

	for (var i = 0; i < children_arr.length; i++) 
		children_arr[i].detach();

	calculate();
	create_columns();	
	append_children();

	var instance = {

		container: _this,
		columns: columns_arr,
		children: children_arr,
		options: options,

		append: function(child) {

			var child = $(child);
			instance.children.push(child);
			if (options.harder) 
			     col = shortest_column(instance.columns);
			else col = instance.columns[col_idx++ % col_limit];
			col.append(child);
			child.css("float", "none");
			child.css("width", "auto");

		}, 

		empty: function() {
			for (var i = 0; i < instance.children.length; i++) 
				instance.children[i].remove();
			col_idx = 0;
		},

		update: function(_options) {

			for (var idx in _options) 
				options[idx] = _options[idx];

			children_arr = instance.children;
			columns_arr = instance.columns;
			col_limit = options.columns;
			width = _this[0].getBoundingClientRect().width;
			col_idx = 0;

			$.each(instance.children, function(_, child) {
				child.detach();
			});

			$.each(instance.columns, function(_, column) {
				column.remove();
			});
			
			calculate();
			create_columns();
			append_children();

			instance.children = children_arr;
			instance.columns = columns_arr;

		}

	};

	return instance;

};