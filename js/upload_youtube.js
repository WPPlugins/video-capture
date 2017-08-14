jQuery(function () {
    'use strict';

    var ajaxurl = YouTubeUpload.ajaxurl;
    var nonce = YouTubeUpload.nonce;

    // Add event listener of clicking upload to YouTube link.
    jQuery('.upload-video-to-youtube').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var link_object = this;

        var is_user_has_api_application = jQuery(link_object).data('has-application');
        if (!is_user_has_api_application) {
            alert('Please add YouTube credentials in Vidrack Settings');
            return;
        }

        var result = confirm('Do you want to upload this video to YouTube?');
        if( !result ) return;

        var is_oauth = jQuery(link_object).data('is-oauth');
        if( is_oauth ) {
            youtubeAction( link_object );
            return;
        }

        var auth_url = jQuery(link_object).data('auth-url');
        if( auth_url ){
           var oauth_window =  window.open(auth_url, 'authentication', 'width=600,height=400');
            var timer = setInterval(function() {
                if(oauth_window.closed) {
                    clearInterval(timer);
                    youtubeAction( link_object );
                }
            }, 500);
        }
        else{
            youtubeAction( link_object) ;
        }
        return;
    });

    // Call YouTube action from backend.
    function youtubeAction( link_object ) {
        var video_link = jQuery(link_object).attr('href');
        var video_post_id = jQuery(link_object).data('post-id');
        jQuery(document)
            .ajaxStart(function () {
                runSpinner( link_object );
            })
            .ajaxStop(function () {
                stopSpinner();
                jQuery(document).unbind( 'ajaxStart' );
                jQuery(document).unbind( 'ajaxStop' );
            });

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "json",
            data: {
                action: 'youtubeAction',
                video_link: video_link,
                post_id: video_post_id,
                nonce: nonce,
            },
            error: function () {
                alert( 'An error occurred!' );
                window.location.reload( true );
            },
            success: function ( data ) {
                if ( data.status &&  'success' === data.status ) {
                    alert( 'Video was successfully uploaded!' );
                    window.location.reload( true) ;
                }
                else{
                    alert( data.message );
                    window.location.reload(true);
                }

            }
        })
    }
});




