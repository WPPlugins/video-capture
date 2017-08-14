/* global jQuery, console */

jQuery(function() {
    'use strict';

    jQuery(".post-type-vidrack_video td.column-title a.row-title").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        
        return false;
    })

});
