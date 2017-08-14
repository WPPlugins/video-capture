/* global jQuery, console */
jQuery(function ($) {
    'use strict';

    var ajaxurl = VideoDownload.ajaxurl;
    var nonce = VideoDownload.nonce;

    // Add event listener of clicking download video link
    $(".vidrack-download-video-link").on("click", function(e){

		e.preventDefault();

    	var $this = $(this);
        var video_link_href = $this.attr('href');

        // Is video on Amazon server?
        videoIsset(video_link_href, function(is_set) {
            if (video_link_href && is_set) {
            	var doc = document;
            	var body = document.body;
                var link = doc.createElement("a");

                link.href = video_link_href;
                if (typeof link.download != "undefined") {
                    link.download = '';
                }

				body.appendChild(link);
                link.click();
				body.removeChild(link);
            }
            else {
                alert("An error occurred, please refresh the page and try again!");
                return false;
            }
        });
    });

    function videoIsset(video_link_href, cb) {
        // Send AJAX request to check if video exists
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "json",
            async: false,
            data: {
                action: 'validate_video_download_link',
                video_link: video_link_href,
                nonce: nonce
            },
            error: function () {
                cb(false);
            },
            success: function (data) {
                cb(data.status === 'success');
            }
        });
    }
});




