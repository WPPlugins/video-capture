/* global jQuery, console, starRating */

jQuery(function () {
    'use strict';

    var ajaxurl = VideoRating.ajaxurl;
    var nonce = VideoRating.nonce;

    // Add rating starts system to each rating column.
    jQuery('#the-list .column-vidrack_video_rating').each(function (){

        var rating_value = jQuery(this).html();
        jQuery(this).empty();

        jQuery(this).starRating({
            setRating: 1,
            activeColor: '#FFA500',
            useGradient: false,
            starSize: 18,
            disableAfterRate: false,
            initialRating: rating_value,
            callback: function(new_rating, $el){
                // Event new rating value, upadate current value.
                var post_id =  jQuery($el).parent().attr('id').split('-').pop();
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: "json",
                    async: false,
                    data: {
                        action: 'set_rating_video',
                        post_id: post_id,
                        rating_value: new_rating,
                        nonce: nonce
                    },
                    success: function (data) {
                        if( data.status == 'error' || data.result == false )
                            alert( 'An error occurred! Please refresh the page and try again!' );
                    },
                    error: function () {
                        alert( 'An error occurred! Please refresh the page and try again!' );
                    }
                });
            }
        });
    });
});