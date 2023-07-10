/**
 * 
 */
$(function() {
	const $table = $('table').tablesorter({
		widgets: ['filter', 'columnSelector'],
		widgetOptions: {
			filter_columnFilters: false,
			filter_liveSearch: 3,
			filter_external : '.searchField',
			filter_reset: '[type="reset"]',
			filter_saveFilters : true,
			filter_cssFilter: 'form-control',
			filter_filteredRow: 'd-none',
			columnSelector_mediaquery: false,
			columnSelector_container: '#ColumnSelector',
			columnSelector_layout: document.getElementById('ColumnSelector').dataset.columnSelectorPrototype
		}
	});
});