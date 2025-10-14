$('.form-select-sm').on('change', '[type="checkbox"]', function() {
	const self = this;
	$(self).siblings('[type="checkbox"]').prop('checked', false)
	const selfLft = parseInt(self.dataset.lft, 10);
	const selfRgt = parseInt(self.dataset.rgt, 10);
	const selected = this.checked ? [this.value] : [];
	$('[name="' + self.name + '"]').not(this).each( function() {
		if ((parseInt(this.dataset.lft, 10) > selfLft) && (selfRgt > parseInt(this.dataset.rgt, 10))) {
			this.checked = self.checked;
		}
		if (this.checked) {
			$(this).siblings('[type="checkbox"]').prop('checked', false);
			selected.push(this.value);
		}
	});
	let value = {'on': [], 'off': []};
	$(this).closest('.form-select-sm').find('[type="checkbox"]:checked').each( function() {
		if (this.name.match(/\[on\]/)) {
			value.on.push(this.value);
		} else {
			value.off.push(this.value);
		}
	});
	const searchField = $(self).closest('.form-select-sm').next('.searchField');
	if (value.on?.length || value.off?.length) {
		searchField.val(JSON.stringify(value));
	} else {
		searchField.val(null);
	}
	searchField.blur();
})