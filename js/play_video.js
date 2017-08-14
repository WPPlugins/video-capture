/* global jQuery, videojs, console */

jQuery(function() {
  'use strict';

    // Add event listener of clicking play video link
    jQuery(".vidrack-play-video-link").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();

        var video_link = jQuery(this).attr('href');
        var video_format = video_link.split('.').pop();

        if(video_format){

            var video_type;
            switch (video_format){
                case 'flv':
                    video_type = 'video/flv';
                    break;
                default :
                    video_type = 'video/mp4';
                    break;
            }

            var video = '<div class="vidrack-video-preview-wrap">' +
                            '<div class="vidrack-video-preview-close"></div>' +
                            '<video id="vidrack-video-preview" class="video-js vjs-default-skin" controls preload="none">'+
                            '   <p class="vjs-no-js">' +
                            '       To view this video please enable JavaScript, and consider upgrading to a web browser that' +
                            '       <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>' +
                            '   </p>'
                            '</video>'
                        '</div>';

            var window_fade = '<div class="window-fade"></div>';

            jQuery('body').append(window_fade).append(video);

            var g_player = videojs('vidrack-video-preview', {"autoplay": false});
            g_player.src({ type: video_type, src: video_link });

            jQuery(".vidrack-video-preview-close, .window-fade, .vjs-error-display").on("click", function () {
                    g_player.dispose();
                    jQuery( ".vidrack-video-preview-wrap, .window-fade" ).remove();
            })
        }
        return;
    });

});
