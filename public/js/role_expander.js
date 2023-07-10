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
	let value = '';
	if (self.name.match(/on/)) {
		value = selected.join(' or ');
		selected.length = 0;
		$('[name="' + self.name.replace(/on/, 'off') + '"]:checked').each( function() {
			selected.push('!' + this.value);
		});
		if (value.length) {
			value = value + ' && ';
		}
		if (selected.length) {
			value = value + selected.join(' && ');
		}
	} else {
		if (selected.length) {
			value += '!' + selected.join(' and !');
		}
		selected.length = 0;
		$('[name="' + self.name.replace(/off/, 'on') + '"]:checked').each( function() {
			selected.push(this.value);
		});
		if (value.length) {
			value = value + ' && ';
		}
		if (selected.length) {
			value = value + selected.join(' or ');
		}
	}
	const searchField = $(self).closest('.form-select-sm').next('.searchField');
	if (value.length) {
		searchField.val(value);
	} else {
		searchField.val(null);
	}
	searchField.blur();
})