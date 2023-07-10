/**
 * 
 */
 $(function() {
	$('[data-dynamic-options]').each( function () {
		const selfId = '#' + this.id;
		const sourceId = '#' + this.id.replace(this.name.substring(this.name.lastIndexOf('[') + 1, this.name.lastIndexOf(']')), this.dataset.dynamicOptions);
		$(sourceId).change( function() {
			const form = this.closest('form');
			$.ajax({
				url: form.action,
				method: form.method,
				data: {[this.name]: this.value},
				complete: function (response) {
					$(selfId).replaceWith(
						$(response.responseText).find(selfId)
					);
				}
			});
		})
	});
});