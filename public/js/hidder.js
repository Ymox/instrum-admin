/**
 * 
 */
$(function() {
    $('[data-hide]').click( function() {
        if (this.checked || this.selected) {
            $(`[title="${this.dataset.hide}"]`).closest('.to-hide').hide();
        } else {
            $(`[title="${this.dataset.hide}"]`).closest('.to-hide').show();
        }
    })
})