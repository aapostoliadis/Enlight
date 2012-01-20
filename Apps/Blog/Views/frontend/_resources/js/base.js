jQuery(document).ready(function($) {
    // Removes hiding class for all script related elements
    //$('.hide_script').removeClass('hide_script');

    $('select.auto-submit').bind('change', function() {this.form.submit(); });
    $('input.auto-submit:radio, a.auto-submit, input.auto-submit:checkbox').live('click', function() { this.form.submit(); });
    $('input.auto-submit:text').live('blur', function() { this.form.submit(); });
});