/* global jQuery, VideoCapture, swfobject, FormData, console */

jQuery(function ($) {
	'use strict';

	var pro_version = VideoCapture.pro_version;
	var record_btn_element = '';
	var record_type = '';
	var configure_options = {
		'name': VideoCapture.collect_name,
		'email': VideoCapture.collect_email,
		'phone': VideoCapture.collect_phone,
		'birthday': VideoCapture.collect_birthday,
		'location': VideoCapture.collect_location,
		'additional_data': VideoCapture.collect_additional_data,
		'language': VideoCapture.collect_language,
		'collect_custom_data': VideoCapture.collect_custom_data
	};

	var required_options = filter_configure_options(configure_options);
	var name = '';
	var email = '';
	var phone = '';
	var birthday = '';
	var location = '';
	var additional_data = '';
	var language = '';
	var custom_fields = {};

	// UUID generator
	function generateUUID() {
		var d = Date.now();
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
			var r = (d + Math.random() * 16) % 16 | 0;
			d = Math.floor(d / 16);
			return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
		});
	}

	// Detect if we're on desktop or mobile
	if (VideoCapture.mobile) {
		$('.wp-video-capture-mobile').show();
	} else {
		$('.wp-video-capture-desktop').show();

		// Desktop upload button.
		if (VideoCapture.desktop_upload) {
			$('.wp-video-capture-desktop-upload').show();
		}
	}

	function filter_configure_options(required_options) {
		var USED_VALUES = ['mandatory', 'optional'];
		var options = {};
		for (var key in required_options) {
			if (required_options.hasOwnProperty(key)) {
				if (USED_VALUES.indexOf(required_options[key]) !== -1) {
					options[key] = required_options[key];
				}
			}
		}

		var isMandatory = false;

		required_options['collect_custom_data'].forEach(function(el) {
			console.log(el.name);
			if (el.name.length != 0 && el.value != 'no') {
				isMandatory = true;
			}
		})

		if (isMandatory) {
			options['custom'] = required_options['collect_custom_data']
				.filter(function (el) {
					return USED_VALUES.indexOf(el.value) !== -1 && el.name
				})
				.map(function (el) {
					var res = {};
					res[el.name] = el.value;
					return res;
				})
				.reduce(function (res, el) {
					return Object.assign(res, el)
				}, {});
		}

		return options;
	}

	// Record and upload button click  action.
	$('.wp-video-capture-record-button-mobile, .wp-video-capture-upload-button-desktop, .wp-video-capture-record-button-desktop').click(function (event) {
		event.preventDefault();
		event.stopPropagation();

		record_btn_element = $(this);
		record_type = $(record_btn_element).data('record-type');

		if (Object.keys(required_options).length > 0 && true == pro_version) {

			$('.wp-video-collect-data').show();
			for (var key in required_options) {
				switch (key) {
					case 'birthday':
						$('#collect-birthday').datepicker({
							changeYear: true,
							yearRange: "c-100:c"
						});
						break;
					case 'language':
						$("#collect-language").select2();
						break;
					case 'custom':
						renderCustomCollectOptions(required_options[key]);
						continue;
						break;
					default :
						break
				}

				$('.wp-video-collect-data-block[data-collect="' + key + '"]').show();
				if ('mandatory' === required_options[key]) {
					$('.wp-video-collect-data-block[data-collect="' + key + '"]').find('.required').show();
					$('.wp-video-collect-data-form').find('.required-text').show();
					$('.wp-video-collect-data-block[data-collect="' + key + '"]').find('.wp-video-collect-data-input').attr('data-required', true);
				} else {
					$('.wp-video-collect-data-block[data-collect="' + key + '"]').find('.wp-video-collect-data-input').attr('data-required', false);
				}
			}
		}
		else {
			recordAction(record_type);
		}

		return;
	});

	function renderCustomCollectOptions(custom_options) {
		for (var i = 1; i <= Object.keys(custom_options).length; i++) {
			if ('' == Object.keys(custom_options)[i - 1]) continue;
			$('.wp-video-collect-data-block[data-collect="custom-' + i + '"]').show();
			$('.custom-' + i + '-name').html(Object.keys(custom_options)[i - 1]);
			$('.wp-video-collect-data-block[data-collect="custom-' + i + '"]').find('.wp-video-collect-data-input').attr('name', 'vidrack-capture-' + Object.keys(custom_options)[i - 1]);
			if ('mandatory' === custom_options[Object.keys(custom_options)[i - 1]]) {
				$('.wp-video-collect-data-block[data-collect="custom-' + i + '"]').find('.required').show();
				$('.wp-video-collect-data-form').find('.required-text').show();
				$('.wp-video-collect-data-block[data-collect="custom-' + i + '"]').find('.wp-video-collect-data-input').attr('data-required', true);
			} else {
				$('.wp-video-collect-data-block[data-collect="custom-' + i + '"]').find('.wp-video-collect-data-input').attr('data-required', false);
			}
		}
	}

	$('.wp-video-collect-data-form input[name="record-action"]').click(function (event) {
		event.preventDefault();
		event.stopPropagation();

		var action_submit = $(this).val().toLowerCase();

		var failed_validation = '';
		$('.wp-video-collect-data-input:visible').each(function () {
			$(this).next('.wp-video-capture-collect-error').hide();
			if (!validateInputAction(this)) {
				$(this).next('.wp-video-capture-collect-error').show();
				failed_validation = true;
				return;
			} else {
				var input_name = $(this).attr('name');
				switch (input_name) {
					case 'vidrack-capture-name':
						name = $(this).val()
						break;
					case 'vidrack-capture-email':
						email = $(this).val()
						break;
					case 'vidrack-capture-phone':
						phone = $(this).val()
						break;
					case 'vidrack-capture-birthday':
						birthday = $(this).val()
						break;
					case 'vidrack-capture-location':
						location = $(this).val()
						break;
					case 'vidrack-capture-additional-data':
						additional_data = $(this).val();
						break;
					case 'vidrack-capture-language':
						language = $(this).val();
						break;
					default :
						custom_fields[input_name] = $(this).val();
						break;
				}
			}
		})

		if (!failed_validation) {
			recordAction(record_type);
			$('.wp-video-collect-data').hide();
			return;
		}
		else {
			return;
		}
	});

	function validateInputAction(input) {
		var input_name = $(input).attr('name');
		var input_val = $(input).val();
		var input_required = $(input).data('required');
		if (input_required && '' == input_val) {
			return false;
		} else if (!input_required && '' == input_val) {
			return true;
		}
		switch (input_name) {
			case 'vidrack-capture-email':
				return isEmail(input_val);
				break;
			case 'vidrack-capture-phone':
				return isPhone(input_val);
				break;
			default :
				return true;
				break;
		}
	}

	// Handle record action.
	function recordAction(record_type) {
		switch (record_type) {
			case 'upload':
				$(record_btn_element).parent().parent().find('.wp-video-capture-file-selector').click();
				break;
			case 'record':
				// Display popup window on desktop
				if (VideoCapture.window_modal) {
					$('.wp-video-capture-flash-container')
						.addClass('wp-video-capture-flash-container-popup');
					$('span.wp-video-capture-record-flash-runner').magnificPopup({
						type: 'inline',
						preloader: false,
						callbacks: {
							beforeOpen: function () {
								$('#wp-video-capture-flash-block').show();
							},
							afterClose: function () {
								$('#wp-video-capture-flash-block').hide();
							}
						}
					});
				}
				renderSWF(record_btn_element)
				break;
			default:
				break
		}
		return;
	}

	// Check correct email address.
	function isEmail(email) {
		var pattern = new RegExp(/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i);
		return pattern.test(email);
	}

	// Check correct phone number.
	function isPhone(phone) {
		var pattern = new RegExp(/^[\s()+-]*([0-9][\s()+-]*){6,20}(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$/i);
		return pattern.test(phone);
	}

	function renderSWF(element) {
		// Pass SWF Video Player params
		var flashvars = {
			ajaxurl: VideoCapture.ajaxurl,
			ip: VideoCapture.ip,
			email: email,
			name: name,
			phone: phone,
			birthday: birthday,
			location: location,
			language: language,
			additional_data: additional_data,
			custom_fields: JSON.stringify(custom_fields),
			external_id: $(element).parent().parent().parent().parent().data('external-id'),
			tag: $(element).parent().parent().parent().parent().data('tag'),
			desc: $(element).parent().parent().parent().parent().data('desc'),
			js_callback: VideoCapture.js_callback,
			site_name: VideoCapture.site_name,
			backLink: VideoCapture.display_branding,
			nonce: VideoCapture.nonce
		};

		// Embed SWFObject
		swfobject.embedSWF(
			VideoCapture.plugin_url + 'lib/swf/recorder.swf',
			'wp-video-capture-flash',
			'420', // Width
			'350', // Height
			'9',   // Flash version
			'',
			flashvars
		);

		if (!VideoCapture.window_modal) {
			// Show SWF container
			$(element).parent().parent().find('.wp-video-capture-flash-container').show();

			// Hide the button
			$(element).hide();
		} else {
			$('.wp-video-capture-record-flash-runner').click();
		}
	}

	// Submit video automatically after file has been selected
	$('.wp-video-capture-file-selector').on('change', function () {
		if ($(this).val()) {
			submitVideo($(this).parent().parent().parent());
		}
	});

	// Bind to upload button click
	function submitVideo(d) {
		d.find('.wp-video-capture-progress-indicator-container').css('display', 'inline-block');

		d.find('.wp-video-capture-ajax-success-store').hide();
		d.find('.wp-video-capture-ajax-success-upload').hide();
		d.find('.wp-video-capture-ajax-error-store').hide();
		d.find('.wp-video-capture-ajax-error-upload').hide();
		d.find('.wp-video-capture-progress-container').show();
		d.find('.wp-video-capture-progress-text').show();

		var form = d.find('.wp-video-capture-upload-form');
		var got_file = d.find('.wp-video-capture-file-selector').val().replace(/.*(\/|\\)/, '');

		// Get extension before sanitizing file name
		var ext_re = /(?:\.([^.]+))?$/;
		var ext = ext_re.exec(got_file)[1];

		// Sanitize filename
		var filename =
			VideoCapture.site_name + '_' +
			generateUUID() +
			'.' + ext.toLowerCase();

		console.log('Submitting file "' + filename + '"');

		var ip = VideoCapture.ip;
		var tag = d.parent().parent().data('tag');
		var desc = d.parent().parent().data('desc');
		var external_id = d.parent().parent().data('external-id');
		var nonce = VideoCapture.nonce;

		var form_data = new FormData();
		form_data.append('filename', filename);
		form_data.append('video', d.find('.wp-video-capture-file-selector')[0].files[0]);

		// Store video on the server
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			contentType: false,
			data: form_data,
			async: true,
			cache: false,
			processData: false,

			// Progress indicator
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					myXhr.upload.addEventListener(
						'progress',
						function (event) {
							var progress = Math.round(event.loaded / event.total * 100);
							d.find('.wp-video-capture-progress').val(progress);
							d.find('.wp-video-capture-progress-text').find('span').html(progress);
						},
						false
					);
				}
				return myXhr;
			},

			// AJAX error
			error: function (jqXHR) {
				d.find('.wp-video-capture-ajax-error-upload')
					.html('Error uploading video (AJAX): ' + jqXHR.responseJSON.message);
				d.find('.wp-video-capture-ajax-error-upload').show();
			},

			success: function (data) {
				if (data.status === 'success') {
					d.find('.wp-video-capture-ajax-success-upload')
						.html('Success uploading video: ' + data.message);
					d.find('.wp-video-capture-ajax-success-upload').show();
					d.find('.wp-video-capture-powered-by').show();

					// Store video info in Wordpress DB
					$.post(
						VideoCapture.ajaxurl,
						{
							action: 'store_video_file',
							filename: filename,
							ip: ip,
							email: email,
							name: name,
							phone: phone,
							birthday: birthday,
							additional_data: additional_data,
							language: language,
							location: location,
							custom_fields: JSON.stringify(custom_fields),
							external_id: external_id,
							tag: tag,
							desc: desc,
							nonce: nonce
						}
					).done(function (data) {
						if (data.status === 'success') {
							d.find('.wp-video-capture-ajax-success-store')
								.html('Success storing video: ' + data.message);
							d.find('.wp-video-capture-ajax-success-store').show();

							// Callback function for 3rd party integration.
							if (VideoCapture.js_callback) {
								var js_callback = VideoCapture.js_callback + '("' + filename + '", "' + ip + '", "' + external_id + '");';
								console.log('Calling JS function ' + js_callback);
								eval(js_callback);
							}

							console.log('Video submitted successfully!');
						} else {
							d.find('.wp-video-capture-ajax-error-store')
								.html('Error storing video: ' + data.message);
							d.find('.wp-video-capture-ajax-error-store').show();
						}
					}).fail(function (jqXHR, textStatus) {
						d.find('.wp-video-capture-ajax-error-store')
							.html('Error storing video (AJAX): ' + textStatus);
						d.find('.wp-video-capture-ajax-error-store').show();
					});

				} else {
					d.find('.wp-video-capture-ajax-error-upload')
						.html('Error uploading video: ' + data.message);
					d.find('.wp-video-capture-ajax-error-upload').show();
				}
			},

			complete: function () {
				d.find('.wp-video-capture-file-selector').val('');
				d.find('.wp-video-capture-progress-container').hide();
			}
		});
	}
});
