/**
 * 
 */
$(function() {
	$(document).on('show.bs.modal', function(e) {
		if ((e.relatedTarget.nodeName == 'A') && e.relatedTarget.href) {
			$('.modal-content').load(e.relatedTarget.href);
		}
	})

	$(document).on('hidden.bs.modal', function(e) {
		const target = $(e.target).find('.modal-content').get(0);
		target.innerHTML = target.dataset.emptyContent;
	});
});