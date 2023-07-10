/**
 * 
 */
 $(function() {
	$('#MailCollector, .mail-collector').click( function() {
		const mailType = this.dataset.mailType || '';
		let mails = '';
		$('tbody tr:visible td:contains("@") a' + mailType + '[href^="mailto"]').each ( function() {
			mails = mails + ' ' + this.href.substring(7);
		});
		mails = mails
			.split(' ')
			.filter((address, index, self) => address != false && self.indexOf(address) === index).join(', ');
		$(this.dataset.bsTarget).find('.modal-content').html(this.dataset.prototype.replace(/__mails__/g, mails));
	})
});