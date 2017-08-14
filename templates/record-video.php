<?php
/**
 * [vidrack] tag template.
 *
 * @package wp-video-capture
 */

?>

<div class="wp-video-capture"
         style="text-align: <?php echo esc_html( $align ) ?>;"
         data-external-id="<?php echo esc_html( $ext_id ) ?>"
         data-tag="<?php echo esc_html( $tag ) ?>"
         data-desc="<?php echo esc_html( $desc ) ?>">

    <!-- Mobile Version -->
    <div class="wp-video-capture-mobile">
        <form class="wp-video-capture-upload-form" method="post" action="https://storage.vidrack.com/video">
            <div class="wp-video-capture-progress-indicator-container">
                <div class="wp-video-capture-ajax-success-store"></div>
                <div class="wp-video-capture-ajax-success-upload"></div>
                <div class="wp-video-capture-ajax-error-store"></div>
                <div class="wp-video-capture-ajax-error-upload"></div>
                <div class="wp-video-capture-progress-container">
                    <p><?php _e( 'Uploading','video-capture' );?>...</p>
                    <progress class="wp-video-capture-progress" value="0" max="100"></progress>
                    <div class="wp-video-capture-progress-text">
                        <span>0</span>%
                    </div>
                </div>
            </div>
			<div style="clear: both"></div>
            <div class="wp-video-capture-button-container">
                <div class="wp-video-capture-powered-by">
					<?php _e( 'Powered by','video-capture' );?> <a href="http://vidrack.com" target="_blank">Vidrack</a>
                </div>
                <a href class="wp-video-capture-record-button-mobile needsclick" data-record-type="upload" ><?php _e( 'Record Video','video-capture' );?></a>
                <input class="wp-video-capture-file-selector" type="file" accept="video/*;capture=camcorder" />
                <a class="wp-video-capture-troubleshooting" href="http://vidrack.com/fix" target="_blank">
					<?php _e( 'Problems recording a video?','video-capture' );?>
                </a>
            </div>
        </form>
    </div>

    <!-- Desktop Version -->
    <div class="wp-video-capture-desktop">
		<div class="wp-video-capture-desktop-record">
			<div class="wp-video-capture-flash-container" id="wp-video-capture-flash-block">
				<div id="wp-video-capture-flash">
					<p><?php _e( 'Your browser doesn\'t support Adobe Flash, sorry','video-capture' );?>.</p>
					<p><?php _e( 'Please install Adobe Flash plugin', 'video-capture' );?>. <a href="https://get.adobe.com/flashplayer/" target="_blank">Get Flash Player</a>.</p>
				</div>
			</div>
			<div class="wp-video-capture-button-container">
				<a href data-record-type="record" class="wp-video-capture-record-button-desktop"><?php _e( 'Record Video','video-capture' );?></a>
				<span data-mfp-src="#wp-video-capture-flash-block" class="wp-video-capture-record-flash-runner"></span>
			</div>
		</div>
	    <div class="wp-video-capture-desktop-upload">
		    <form class="wp-video-capture-upload-form" method="post" action="https://storage.vidrack.com/video">
			    <div class="wp-video-capture-progress-indicator-container">
				    <div class="wp-video-capture-ajax-success-store"></div>
				    <div class="wp-video-capture-ajax-success-upload"></div>
				    <div class="wp-video-capture-ajax-error-store"></div>
				    <div class="wp-video-capture-ajax-error-upload"></div>
				    <div class="wp-video-capture-progress-container">
					    <p><?php _e( 'Uploading...','video-capture' );?></p>
					    <progress class="wp-video-capture-progress" value="0" max="100"></progress>
					    <div class="wp-video-capture-progress-text">
						    <span>0</span>%
					    </div>
				    </div>
			    </div>
				<div style="clear: both"></div>
			    <div class="wp-video-capture-button-container">
				    <input class="wp-video-capture-file-selector" type="file" accept="video/*;capture=camcorder" />
				    <a href data-record-type="upload" class="wp-video-capture-upload-button-desktop"><i class="wp-video-capture-upload-button-icon"></i> <?php _e( 'Video Upload','video-capture' );?></a>
			    </div>
		    </form>
	    </div>
	    <a class="wp-video-capture-troubleshooting" href="http://vidrack.com/fix/" target="_blank">
			<?php _e( 'Problems recording a video?','video-capture' );?>
	    </a>
    </div>

	<!-- Collect Data -->
	<div class="wp-video-collect-data">
		<form class="wp-video-collect-data-form" method="post" action="#">
			<div class="wp-video-collect-data-block" data-collect="name">
				<label><?php _e( 'Your name','video-capture' );?><span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='vidrack-capture-name' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please paste correct name','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="email">
				<label><?php _e( 'Your email','video-capture' );?> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='vidrack-capture-email' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please paste correct email','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="phone">
				<label><?php _e( 'Your phone','video-capture' );?><span class="required">*</span>:</label>
				<input type="tel" class="wp-video-collect-data-input" name='vidrack-capture-phone' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please paste correct phone number','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="birthday">
				<label><?php _e( 'Your date of birth','video-capture' );?> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" id="collect-birthday" name='vidrack-capture-birthday' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please paste date of birth','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="location">
				<label><?php _e( 'Your location','video-capture' );?> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='vidrack-capture-location' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please paste location','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="language">
				<label><?php _e( 'Your language','video-capture' );?> <span class="required">*</span>:</label>
				<select style="width: 100%" class="wp-video-collect-data-input" id="collect-language" name='vidrack-capture-language'>
					<option value=""></option>
					<option value="Abkhazian">Abkhazian</option>
					<option value="Afar">Afar</option>
					<option value="Afrikaans">Afrikaans</option>
					<option value="Albanian">Albanian</option>
					<option value="Amharic">Amharic</option>
					<option value="Arabic">Arabic</option>
					<option value="Aragonese">Aragonese</option>
					<option value="Armenian">Armenian</option>
					<option value="Assamese">Assamese</option>
					<option value="Aymara">Aymara</option>
					<option value="Azerbaijani">Azerbaijani</option>
					<option value="Bashkir">Bashkir</option>
					<option value="Basque">Basque</option>
					<option value="Bengali(Bangla)">Bengali(Bangla)</option>
					<option value="Bhutani">Bhutani</option>
					<option value="Bihari">Bihari</option>
					<option value="Bislama">Bislama</option>
					<option value="Breton">Breton</option>
					<option value="Bulgarian">Bulgarian</option>
					<option value="Burmese">Burmese</option>
					<option value="Byelorussian(Belarusian)">Byelorussian(Belarusian)</option>
					<option value="Cambodian">Cambodian</option>
					<option value="Catalan">Catalan</option>
					<option value="Cherokee">Cherokee</option>
					<option value="Chewa">Chewa</option>
					<option value="Chinese">Chinese</option>
					<option value="Chinese(Simplified)">Chinese(Simplified)</option>
					<option value="Chinese(Traditional)">Chinese(Traditional)</option>
					<option value="Corsican">Corsican</option>
					<option value="Croatian">Croatian</option>
					<option value="Czech">Czech</option>
					<option value="Danish">Danish</option>
					<option value="Divehi">Divehi</option>
					<option value="Dutch">Dutch</option>
					<option value="Edo">Edo</option>
					<option value="English">English</option>
					<option value="Esperanto">Esperanto</option>
					<option value="Estonian">Estonian</option>
					<option value="Faeroese">Faeroese</option>
					<option value="Farsi">Farsi</option>
					<option value="Fiji">Fiji</option>
					<option value="Finnish">Finnish</option>
					<option value="Flemish">Flemish</option>
					<option value="French">French</option>
					<option value="Frisian">Frisian</option>
					<option value="Fulfulde">Fulfulde</option>
					<option value="Galician">Galician</option>
					<option value="Gaelic(Scottish)">Gaelic(Scottish)</option>
					<option value="Gaelic(Manx)">Gaelic(Manx)</option>
					<option value="Georgian">Georgian</option>
					<option value="German">German</option>
					<option value="Greek">Greek</option>
					<option value="Greenlandic">Greenlandic</option>
					<option value="Guarani">Guarani</option>
					<option value="Gujarati">Gujarati</option>
					<option value="HaitianCreole">HaitianCreole</option>
					<option value="Hausa">Hausa</option>
					<option value="Hawaiian">Hawaiian</option>
					<option value="Hebrew">Hebrew</option>
					<option value="Hindi">Hindi</option>
					<option value="Hungarian">Hungarian</option>
					<option value="Ibibio">Ibibio</option>
					<option value="Icelandic">Icelandic</option>
					<option value="Ido">Ido</option>
					<option value="Igbo">Igbo</option>
					<option value="Indonesian">Indonesian</option>
					<option value="Interlingua">Interlingua</option>
					<option value="Interlingue">Interlingue</option>
					<option value="Inuktitut">Inuktitut</option>
					<option value="Inupiak">Inupiak</option>
					<option value="Irish">Irish</option>
					<option value="Italian">Italian</option>
					<option value="Japanese">Japanese</option>
					<option value="Javanese">Javanese</option>
					<option value="Kannada">Kannada</option>
					<option value="Kanuri">Kanuri</option>
					<option value="Kashmiri">Kashmiri</option>
					<option value="Kazakh">Kazakh</option>
					<option value="Kinyarwanda(Ruanda)">Kinyarwanda(Ruanda)</option>
					<option value="Kirghiz">Kirghiz</option>
					<option value="Kirundi(Rundi)">Kirundi(Rundi)</option>
					<option value="Konkani">Konkani</option>
					<option value="Korean">Korean</option>
					<option value="Kurdish">Kurdish</option>
					<option value="Laothian">Laothian</option>
					<option value="Latin">Latin</option>
					<option value="Latvian(Lettish)">Latvian(Lettish)</option>
					<option value="Limburgish(Limburger)">Limburgish(Limburger)</option>
					<option value="Lingala">Lingala</option>
					<option value="Lithuanian">Lithuanian</option>
					<option value="Macedonian">Macedonian</option>
					<option value="Malagasy">Malagasy</option>
					<option value="Malay">Malay</option>
					<option value="Malayalam">Malayalam</option>
					<option value="Maltese">Maltese</option>
					<option value="Maori">Maori</option>
					<option value="Marathi">Marathi</option>
					<option value="Moldavian">Moldavian</option>
					<option value="Mongolian">Mongolian</option>
					<option value="Nauru">Nauru</option>
					<option value="Nepali">Nepali</option>
					<option value="Norwegian">Norwegian</option>
					<option value="Occitan">Occitan</option>
					<option value="Oriya">Oriya</option>
					<option value="Oromo(AfaanOromo)">Oromo(AfaanOromo)</option>
					<option value="Papiamentu">Papiamentu</option>
					<option value="Pashto(Pushto)">Pashto(Pushto)</option>
					<option value="Polish">Polish</option>
					<option value="Portuguese">Portuguese</option>
					<option value="Punjabi">Punjabi</option>
					<option value="Quechua">Quechua</option>
					<option value="Rhaeto-Romance">Rhaeto-Romance</option>
					<option value="Romanian">Romanian</option>
					<option value="Russian">Russian</option>
					<option value="Sami(Lappish)">Sami(Lappish)</option>
					<option value="Samoan">Samoan</option>
					<option value="Sangro">Sangro</option>
					<option value="Sanskrit">Sanskrit</option>
					<option value="Serbian">Serbian</option>
					<option value="Serbo-Croatian">Serbo-Croatian</option>
					<option value="Sesotho">Sesotho</option>
					<option value="Setswana">Setswana</option>
					<option value="Shona">Shona</option>
					<option value="SichuanYi">SichuanYi</option>
					<option value="Sindhi">Sindhi</option>
					<option value="Sinhalese">Sinhalese</option>
					<option value="Siswati">Siswati</option>
					<option value="Slovak">Slovak</option>
					<option value="Slovenian">Slovenian</option>
					<option value="Somali">Somali</option>
					<option value="Spanish">Spanish</option>
					<option value="Sundanese">Sundanese</option>
					<option value="Swahili(Kiswahili)">Swahili(Kiswahili)</option>
					<option value="Swedish">Swedish</option>
					<option value="Syriac">Syriac</option>
					<option value="Tagalog">Tagalog</option>
					<option value="Tajik">Tajik</option>
					<option value="Tamazight">Tamazight</option>
					<option value="Tamil">Tamil</option>
					<option value="Tatar">Tatar</option>
					<option value="Telugu">Telugu</option>
					<option value="Thai">Thai</option>
					<option value="Tibetan">Tibetan</option>
					<option value="Tigrinya">Tigrinya</option>
					<option value="Tonga">Tonga</option>
					<option value="Tsonga">Tsonga</option>
					<option value="Turkish">Turkish</option>
					<option value="Turkmen">Turkmen</option>
					<option value="Twi">Twi</option>
					<option value="Uighur">Uighur</option>
					<option value="Ukrainian">Ukrainian</option>
					<option value="Urdu">Urdu</option>
					<option value="Uzbek">Uzbek</option>
					<option value="Venda X">Venda X</option>
					<option value="Vietnamese X">Vietnamese X</option>
					<option value="VolapÃ¼k">VolapÃ¼k</option>
					<option value="Wallon">Wallon</option>
					<option value="Welsh">Welsh</option>
					<option value="Wolof">Wolof</option>
					<option value="Xhosa">Xhosa</option>
					<option value="Yi">Yi</option>
					<option value="Yiddish">Yiddish</option>
					<option value="Yoruba">Yoruba</option>
					<option value="Zulu">Zulu</option>
				</select>
				<div class="wp-video-capture-collect-error"><?php _e( 'Please select language','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="additional_data">
				<label><?php _e( 'Additional message','video-capture' );?> <span class="required">*</span>:</label>
				<textarea rows="3" class="wp-video-collect-data-input" name='vidrack-capture-additional-data'></textarea>
				<div class="wp-video-capture-collect-error"><?php _e( 'Please write message','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="custom-1">
				<label><span class="custom-1-name"></span> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please fill this field','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="custom-2">
				<label><span class="custom-2-name"></span> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please fill this field','video-capture' );?></div>
			</div>
			<div class="wp-video-collect-data-block" data-collect="custom-3">
				<label><span class="custom-3-name"></span> <span class="required">*</span>:</label>
				<input type="text" class="wp-video-collect-data-input" name='' autocomplete="off">
				<div class="wp-video-capture-collect-error"><?php _e( 'Please fill this field','video-capture' );?></div>
			</div>

			<p class="required-text"><span>*<span> <?php _e( 'required field','video-capture' );?></p>
			<input type="submit" class="wp-video-capture-filed-submit-save" name="record-action" value="Save">
		</form>
    </div>

</div>
