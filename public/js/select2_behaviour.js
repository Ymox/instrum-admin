/**
 *
 */
$(function() {
	const addBehaviour = function (where) {
		$('select.searchable', where || document).select2({
			theme: "bootstrap-5"
		});
	}
	addBehaviour();
	const modalObserver = new MutationObserver( function(mutations) {
		mutations.forEach( function(mutation) {
			addBehaviour($(mutation.addedNodes[3]));
		});
	});
	modalObserver.observe(document.getElementsByClassName('modal-content')[0], { childList: true });

	$(document).on('hidden.bs.modal', function() {
		addBehaviour();
	});
});