/* global jQuery, console */

jQuery(function () {
    'use strict';

    // Facebook share button click.
    jQuery('.facebook-share').on('click', function(e) {
        e.preventDefault();
        window.open(jQuery(this).attr('href'), 'fbShareWindow', 'height=420, width=550, top=' + (jQuery(window).height() / 2 - 150) + ', left=' + (jQuery(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });

});