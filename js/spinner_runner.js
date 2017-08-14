function runSpinner( target ){

    var post_id = jQuery(target).data('post-id');
    jQuery('#'+post_id).trigger("mouseover");
    jQuery(target).css( 'position','relative' );

    var opts = {
         lines: 11 // The number of lines to draw
        , length: 16 // The length of each line
        , width: 10 // The line thickness
        , radius: 13 // The radius of the inner circle
        , scale: 0.25 // Scales overall size of the spinner
        , corners: 1 // Corner roundness (0..1)
        , color: '#000' // #rgb or #rrggbb or array of colors
        , opacity: 0.25 // Opacity of the lines
        , rotate: 28 // The rotation offset
        , direction: 1 // 1: clockwise, -1: counterclockwise
        , speed: 0.7 // Rounds per second
        , trail: 37 // Afterglow percentage
        , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
        , zIndex: 2e9 // The z-index (defaults to 2000000000)
        , className: 'spinner-loader' // The CSS class to assign to the spinner
        , top: '9px' // Top position relative to parent
        , left: '135px' // Left position relative to parent
        , shadow: false // Whether to render a shadow
        , hwaccel: false // Whether to use hardware acceleration
        , position: 'absolute' // Element positioning
    }

    spinner = new Spinner(opts).spin(target);
}

function stopSpinner(){
    spinner.stop();
}





