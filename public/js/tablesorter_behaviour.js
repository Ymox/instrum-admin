/**
 * 
 */
$(function() {
	const $table = $('table').tablesorter({
		widgets: ['filter', 'columnSelector', 'saveSort'],
		widgetOptions: {
			filter_columnFilters: false,
			filter_liveSearch: 3,
			filter_external : '.searchField',
			filter_reset: '[type="reset"]',
			filter_saveFilters : true,
			filter_cssFilter: 'form-control',
			filter_filteredRow: 'd-none',
			savesort: true,
			columnSelector_mediaquery: false,
			columnSelector_container: '#ColumnSelector',
			columnSelector_layout: document.getElementById('ColumnSelector').dataset.columnSelectorPrototype
		}
	});
	$('[type="reset"]', $table).click( function() {
		$table
			.trigger('saveSortReset')
			.trigger('sortReset');
	});
});