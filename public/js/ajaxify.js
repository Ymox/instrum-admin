$(function() {
	$(document).on('click', 'a.ajaxify', function(e) {
		$(this.dataset.loadTarget).load(this.href);
		e.preventDefault();
	});
	$(document).on('submit', 'form.ajaxify', function(e) {
		var postData = $(this).serializeArray();
		if (!this.dataset.loadTarget) {
			$.ajax({
				url: this.action,
				method: this.method,
				data: postData
			 }).then(
				function(data, stringStatus, jqXHR) {
					if (data.value && data.text) {
						$($('#Modal').data('fieldTarget')).append(new Option(data.text, data.value, true, true)).trigger('change');
						$('#Modal').modal('hide');
					} else {
						$('.modal-body').empty().append($('form', data));
					}
				},
				function(jqXHR, stringStatus, errorThrown) {
					$('.modal-body').empty().append($('form', jqXHR.responseText));
				}
			);
		} else {
			$(this.dataset.loadTarget).load(
				this.action,
				postData,
				function(data) {
					$(this.dataset.loadTarget).html(data);
				}
			);
		}
		e.preventDefault();
	});
});