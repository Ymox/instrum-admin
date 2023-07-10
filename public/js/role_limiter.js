$('#member_statuses option, #member_statuses [type="checkbox"]').click( function() {
	const self = this;
	const selected = this.selected;
	const checked = this.checked;
	const selfLft = parseInt(self.dataset.lft, 10);
	const selfRgt = parseInt(self.dataset.rgt, 10);
	$('#member_statuses option, #member_statuses [type="checkbox"]').not(this).each( function() {
		if ((parseInt(this.dataset.lft, 10) < selfLft) && (selfRgt < parseInt(this.dataset.rgt, 10))) {
			this.disabled = selected || checked;
			this.selected = false;
			this.checked = checked;
		} else if ((parseInt(this.dataset.rgt, 10) >= selfLft) || (selfRgt >= parseInt(this.dataset.lft))) {
			this.disabled = false;
		}
	})
});
$('#member_statuses').on('select2:select select2:unselect', function(e) {
	$(e.params.data.element).click();
	$(this).trigger('change.select2');
});