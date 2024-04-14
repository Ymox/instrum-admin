/**
 * 
 */
$(function() {
    $('.icon-verify').click( function() {
        const icon = 'fa-' + this.previousElementSibling.value;
        const initialClasses = $('i', this.nextElementSibling).attr('class');
        $('i', this.nextElementSibling).attr('class', initialClasses.replace(/fa-([^ ]*|$)/, icon));
    });
});