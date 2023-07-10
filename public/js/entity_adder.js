/**
 * 
 */
$(function() {
	const addButton = function($addable) {
		const uri = $addable.data('uri');
		const $label = $addable.prev(); 
		if (!$label.find('a').length) {
			$label.append(' ').append(
				$('<a>', {
					href: uri || '#',
					'data-bs-target': '#Modal',
					'data-bs-toggle': 'modal',
					'html': '<i class="fa fa-plus-circle"></i>',
					target: '_blank',
					'class': 'btn btn-sm btn-outline-success'
				})
			);
		}
	};

	$('select.addable').each( function() {
		addButton($(this));
	});

	$(document).on('show.bs.modal', function(e) {
		$('#Modal').data('fieldTarget', '#' + $(e.relatedTarget).parent().attr('for'));
		$('.modal-content').load($(e.relatedTarget).attr('href'));
	});
	$(document).on('hidden.bs.modal', function(e) {
		const target = $(e.target).find('.modal-content');
		target.html($(target).data('emptyContent'));
	});
});