/**
 *
 */
$(function() {
	$('[data-dynamic-options]').each( function () {
		const selfId = '#' + this.id;
		let start = this.name.length;
		if (this.name.endsWith('[]')) {
			start = start - 3;
		}
		const sourceId = '#' + this.id.replace(this.name.substring(this.name.lastIndexOf('[', start) + 1, this.name.lastIndexOf(']', start)), this.dataset.dynamicOptions);
		$(sourceId).change( function() {
			const form = this.closest('form');
			$.ajax({
				url: form.action,
				method: form.method,
				data: {[this.name]: this.value},
				complete: function (response) {
					$(selfId).html(
						$(response.responseText).find(selfId).html()
					);
					$(selfId).change();
				}
			});
		})
	});
});