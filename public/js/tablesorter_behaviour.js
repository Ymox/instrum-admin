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
			columnSelector_layout: document.getElementById('ColumnSelector').dataset.columnSelectorPrototype,
			filter_functions: {
				0: function (cell, normalizedData, filterValue, columnIndex, $row, config, data) {
					if (filterValue.length <= 17) {
						return;
					}
					const values = JSON.parse(filterValue);
					return !!values.on.length && values.on.some(wanted => (new RegExp(wanted, 'i')).test(cell))
						&& !!values.off.length && !values.off.some(unwanted => (new RegExp(unwanted, 'i')).test(cell));
				}
			}
		}
	});
	$('[type="reset"]', $table).click( function() {
		$table
			.trigger('saveSortReset')
			.trigger('sortReset');
	});
});