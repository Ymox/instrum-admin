/**
 * 
 */
 $(function() {
	const removeButton = function($where) {
		$('>div, >tbody>tr', $where).filter(function() {
			return $('>.remover', $where).length === 0;
		}).css('position', 'relative').append('<button type="button" class="btn btn-sm btn-danger remover" style="position: absolute; top: 2px; right: 2px; width: auto;"><i class="fa fa-times"></i></button>')
	};
	const addButton = function($where) {
		$where.prev().append(' ').append($('<button>', {type: 'button', 'class': 'btn btn-sm btn-success adder', 'html': '<i class="fa fa-plus-circle"></i>'}));
		removeButton($where);
	}
	$('[data-prototype]').each( function() {
		addButton($(this));
		const collectionObserver = new MutationObserver( function(mutations) {
			mutations.forEach( function(mutation) {
				addButton($(mutation.addedNodes[0]).find('[data-prototype]'));
			});
		});
		collectionObserver.observe(this, { childList: true });
	});
	const modalObserver = new MutationObserver( function(mutations) {
		mutations.forEach( function(mutation) {
			addButton($(mutation.addedNodes[3]).find('[data-prototype]'));
		});
	});
	modalObserver.observe(document.getElementsByClassName('modal-content')[0], { childList: true });
	$('form, #Modal').on('click', '.adder', function() {
		const $container = $(this).closest('fieldset, form').find('[data-prototype]').first();
		let prototype = $container.data('prototype');
		const index = new Date().getTime() % (10 * 60 * 1000);
		prototype = prototype.replace(/__name__(label__)?/ig, index);
		$container.prepend(prototype);
		removeButton($container);
		/*$('>div, >tbody>tr', $container).filter(function() {
			return $('>.remover', this).length === 0;
		}).css('position', 'relative').append('<button type="button" class="btn btn-sm btn-danger remover" style="position: absolute; top: 2px; right: 2px; width: auto;"><i class="fa fa-times"></i></button>');*/
	});
	$('form, #Modal').on('click', '.remover', function() {
		$(this).toggleClass('btn-danger btn-light').html('<i class="fa fa-' + ($(this).is('.btn-danger') ? 'times' : 'undo') + '"></i>');
		$(this).closest('div').toggleClass('bg-danger text-white').find(':input:not(button)').prop('disabled', !$(this).is('.btn-danger'));
	});
});