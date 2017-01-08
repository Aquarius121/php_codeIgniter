$(function() {

	var $window = $(window);
	var $html = $('html');
	var results = $('.results');
	var template = $('#result-template').html();
	var categories = $('#categories-selection');
	var categoriesButton = $('#categories-button');
	var media = $('#media-selection');
	var mediaButton = $('#media-button');
	var search = $('#search-field');
	var searchButton = $('#search-button');
	var resetButton = $('#reset-button');
	var alertButton = $('#alert-button');
	var alertEmail = $('#alert-email');
	var alertModal = $('#alert-modal');
	var alertActivateButton = $('#alert-activate-button');
	var alertCancelButton = $('#alert-cancel-button');
	var applyButton = $('#apply-button');
	var isCategoriesSelectionVisible = false;
	var isMediaSelectionVisible = false;
	var datePicker = null;
	var cize = null;

	var lastQueryParams = null;
	var moreResults = false;
	var queryParams = {};
	var queryOffset = 0;
	var queryLimit = 50;
	var queryID = 0;

	var __executeQuery = function(callback) {
		$.post('manage/insights/query/content', {
			params: queryParams,
			offset: queryOffset,
			limit: queryLimit,
			id: ++queryID,
		}, function(response) {
			if (!response) return;
			if (response.id < queryID) return;
			moreResults = response.results.length >= queryLimit;
			queryOffset += response.results.length;
			callback(response);
		});
	};

	var renderResult = function(result) {
		var html = Mustache.render(template, result);
		return html;
	};

	var renderQuery = function(response) {
		$html.removeClass('loader');
		$html.removeClass('loader-more');
		$html.removeClass('loader-new');
		for (var idx in response.results) {
			var result = response.results[idx];
			cize.append(renderResult(result));
		}
	};

	var renderNewQuery = function(response) {
		cize.empty();
		renderQuery(response);
	};

	var executeQuery = function() {
		$html.addClass('loader');
		$html.addClass('loader-more');
		__executeQuery(renderQuery);
	};

	var executeNewQuery = function() {
		if (window.compareObjects(lastQueryParams, queryParams))
			return false;		
		lastQueryParams = $.extend({}, queryParams);
		queryOffset = 0;
		$html.addClass('loader');
		$html.addClass('loader-new');
		__executeQuery(renderNewQuery);
		return true;
	};

	var calculateColumnCount = function() {
		var width = results[0].getBoundingClientRect().width;
		var columns = Math.floor(width / 350);
		return columns;
	};

	cize = results.columnize({
		columns: calculateColumnCount(),
		harder: true,
		margin: 30,
	});

	var cizeUpdate = function() {
		if (!cize) return;
		var columns = calculateColumnCount();
		cize.update({ columns: columns });
	};

	$window.on('resize', function() {
		rate_limit(cizeUpdate, 250);
	});
	
	var dateSelectFrom = $('#date-select-from');
	var dateSelectTo   = $('#date-select-to');
	
	dateSelectFrom.datepicker({
		orientation: 'left',
		autoclose: true,
		todayBtn: false,
		minView: 1,
	});

	dateSelectTo.datepicker({
		orientation: 'left',
		autoclose: true,
		todayBtn: true,
		minView: 1,
	});

	dateSelectFrom.siblings('.date-calendar').on('click', function() {
		dateSelectFrom.focus();
	});

	dateSelectTo.siblings('.date-calendar').on('click', function() {
		dateSelectTo.focus();
	});

	var setDateSelectClass = function() {
		datePicker = $('.datepicker-dropdown')
		datePicker.addClass('date-select-focus');
	};

	dateSelectFrom.on('focus', setDateSelectClass);
	dateSelectTo.on('focus', setDateSelectClass);

	$window.on('click', function(ev) {
		if (!isCategoriesSelectionVisible) return;
		if ($.contains(categories[0], ev.target)) return;
		isCategoriesSelectionVisible = false;
		categories.trigger('paramUpdate');
		categories.removeClass('visible');
		applyButton.removeClass('visible-categories');
	});

	$window.on('click', function(ev) {
		if (!isMediaSelectionVisible) return;
		if ($.contains(media[0], ev.target)) return;
		isMediaSelectionVisible = false;
		media.trigger('paramUpdate');
		media.removeClass('visible');
		applyButton.removeClass('visible-media');
	});

	categoriesButton.on('click', function(ev) {
		ev.preventDefault();
		if (isCategoriesSelectionVisible) return;
		setTimeout(function() { isCategoriesSelectionVisible = true; }, 0);
		categories.addClass('visible');
		applyButton.addClass('visible-categories');
		var offset = categoriesButton.offset();
		categories.css('top', offset.top + 51);
		categories.css('left', offset.left -5);
	});	

	mediaButton.on('click', function(ev) {
		ev.preventDefault();
		if (isMediaSelectionVisible) return;
		setTimeout(function() { isMediaSelectionVisible = true; }, 0);
		media.addClass('visible');
		applyButton.addClass('visible-media');
		var offset = mediaButton.offset();
		media.css('top', offset.top + 51);
		media.css('left', offset.left -5);
	});

	categories.on('paramUpdate', function() {
		var checked = $.makeArray(categories.find('input:checked'));
		queryParams.beats = $.map(checked, function(element) {
			return element.value;
		});		
		rate_limit(executeNewQuery, 0, true);
	});

	media.on('paramUpdate', function() {
		var checked = $.makeArray(media.find('input:checked'));
		queryParams.types = $.map(checked, function(element) {
			return element.value;
		});				
		rate_limit(executeNewQuery, 0, true);
	});

	searchButton.on('click', function() {
		search.trigger('paramUpdate');
		search.focus();
	});

	search.on('keypress', function(ev) {
		if (ev.which != 13) return;
		ev.preventDefault();
		search.trigger('paramUpdate');
	});

	search.on('change', function() {
		if (queryParams.terms != search.val())
			search.trigger('paramUpdate');
	});

	search.on('paramUpdate', function() {
		queryParams.terms = search.val();
		rate_limit(executeNewQuery, 0, true);
	});

	resetButton.on('click', function() {
		setFilterValues();
	});

	dateSelectFrom.on('change', function() {
		dateSelectFrom.trigger('paramUpdate');
	});

	dateSelectTo.on('change', function() {
		dateSelectTo.trigger('paramUpdate');
	});

	dateSelectFrom.on('paramUpdate', function() {
		var date = dateSelectFrom.val();
		queryParams.date_from = date;
		rate_limit(executeNewQuery, 0, true);
	});

	dateSelectTo.on('paramUpdate', function() {
		var date = dateSelectTo.val();
		queryParams.date_to = date;
		rate_limit(executeNewQuery, 0, true);
	});

	var setFilterValues = function() {
		categories.find('input:checked').prop('checked', false).trigger('change');
		media.find('input:checked').each(function() {
			var $this = $(this);
			$this.prop('checked', 
				!!$this.attr('checked'));
		}).trigger('change');
		dateSelectFrom.val(dateSelectFrom.attr('value'));
		dateSelectTo.val(dateSelectTo.attr('value'));
		search.val('').trigger('paramUpdate');
		categories.trigger('paramUpdate');
		media.trigger('paramUpdate');
		dateSelectFrom.trigger('paramUpdate');
		dateSelectTo.trigger('paramUpdate');		
	};

	var resultsScroll = function() {
		if (!moreResults) return;
		var scrollTop = $window.scrollTop();
		var windowHeight = $window.height();
		var contentHeight = $html.height();
		if (scrollTop + (3 * windowHeight) > contentHeight)
			rate_limit(executeQuery, 2000);
	};

	$window.on('scroll', function() {
		rate_limit(resultsScroll, 2000);
	});

	setFilterValues();

	// -----------------------------

	alertButton.on('click', function() {
		alertModal.modal('show');
	});

	alertCancelButton.on('click', function() {
		alertModal.modal('hide');
	});

	alertActivateButton.on('click', function() {
		alertModal.modal('hide');
		$.post('manage/insights/alert/create', {
			email: alertEmail.val(),
			params: lastQueryParams,
		}, function(response) {
			if (!response) return;
			if (!response.message) return;
			bootbox.alert(response);
		});
	});

});


document.documentElement.addEventListener('DOMAttrModified', function(e){
  if (e.attrName === 'style') {
    console.log('prevValue: ' + e.prevValue, 'newValue: ' + e.newValue);
  }
}, false);