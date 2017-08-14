<?php

if ( ! class_exists( 'WP_Video_Capture_Vidrack' ) ) {
	class WP_Video_Capture_Vidrack {

		/**
		 * Plugin initialization functions.
		 */
		public static function plugin_init() {
			self::create_post_type();
			self::add_options();
			self::update_check();
		}

		/**
		 * Create custom post type to store video information.
		 */
		function create_post_type() {
			register_post_type( 'vidrack_video',
				array(
					'labels' => array(
						'name' => __( 'Videos' ),
						'singular_name' => __( 'Video' ),
						'menu_name' => __( 'Vidrack' ),
						'name_admin_bar' => __( 'Vidrack' ),
						'search_items' => __( 'Search Videos' ),
						'not_found' => __( 'No videos found.' )
					),
					'capability_type' => 'post',
					'capabilities' => array(
						'create_posts' => false,
					),
					'map_meta_cap' => true,
					'public' => true,
					'supports' => false,
					'menu_position' => 13,
					'menu_icon' => plugins_url( 'images/icon_vidrack.png', VIDRACK_PLUGIN ),
				)
			);

			add_filter( 'manage_edit-vidrack_video_columns', array( __CLASS__, 'custom_columns' ) );
			add_action( 'manage_posts_custom_column', array( __CLASS__, 'populate_custom_columns' ) );
			add_filter( 'post_row_actions', array( __CLASS__, 'custom_row_actions' ) );
			add_action( 'before_delete_post', array( __CLASS__, 'delete_video' ) );
			add_filter( 'bulk_actions-edit-vidrack_video', array( __CLASS__, 'custom_bulk_actions' ) );
			add_filter( 'posts_clauses', array( __CLASS__, 'alter_posts_search' ) );
			add_filter( 'manage_edit-vidrack_video_sortable_columns', array( __CLASS__, 'vidrack_manage_sortable_columns' ) );
			add_action( 'posts_clauses', array( __CLASS__, 'vidrack_manage_pre_get_posts' ), 1, 2 );

			add_action( 'wp_head', array( __CLASS__, 'vidrack_post_head' ) );
			add_filter( 'the_title', array( __CLASS__, 'vidrack_post_title' ) );
			add_filter( 'the_content', array( __CLASS__, 'vidrack_post_content' ) );

			/*
			 * Disable Yoast SEO on vidrack_video
			 */
			global $pagenow;
			global $wpseo_meta_columns;
			if ( $pagenow === "edit.php" && isset( $_REQUEST['post_type'] ) ) {
				if ( isset( $wpseo_meta_columns ) && $_REQUEST['post_type'] === "vidrack_video" ) {
					remove_action( 'admin_init', array( $wpseo_meta_columns, 'setup_hooks' ) );
				}
			}

		}

		/**
		 * Create custom columns.
		 *
		 * @param Array $columns existing columns.
		 * @return Array custom columns data list.
		 */
		function custom_columns( $columns ) {
			global $pro_account;

			$custom_columns = array(
				'cb' => $columns['cb'],
				'title' => __( 'Filename' ),
			);

			if( $pro_account ) {
				$custom_columns = array_merge( $custom_columns, array(
					'vidrack_video_rating' => __( 'Rating' ),
					'vidrack_video_tag' => __( 'Tags' ),
					'vidrack_video_desc' => __( 'Description' ),
					'vidrack_video_configurable_options' => __( 'Configurable Options' ),
				) );
			}

			$custom_columns = array_merge( $custom_columns, array(
				'vidrack_video_ip' => __( 'IP' ),
				'vidrack_video_external_id' => __( 'External ID' ),
				'date' => $columns['date'],
			) );

			return $custom_columns;
		}

		/**
		 * Populate custom columns with metadata.
		 *
		 * @param string $column column.
		 */
		function populate_custom_columns( $column ) {
			if ( 'vidrack_video_ip' === $column ) {
				echo esc_html( get_post_meta( get_the_ID(), '_vidrack_ip', true ) );
			} elseif ( 'vidrack_video_external_id' === $column ) {
				echo esc_html( get_post_meta( get_the_ID(), '_vidrack_external_id', true ) );
			} elseif ( 'vidrack_video_rating' === $column ) {
				echo esc_html( get_post_meta( get_the_ID(), '_vidrack_video_rating', true ) );
			} elseif ( 'vidrack_video_configurable_options' === $column ) {
				$configure_options_array = array( );
				$configure_options_array[ 'email' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_email', true ) );
				$configure_options_array[ 'name' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_name', true ) );
				$configure_options_array[ 'phone' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_phone', true ) );
				$configure_options_array[ 'birthday' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_birthday', true ) );
				$configure_options_array[ 'location' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_location', true ) );
				$configure_options_array[ 'language' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_language', true ) );
				$configure_options_array[ 'message' ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_additional_data', true ) );

				$custom_collect_data_name_1 = get_option( 'vidrack_custom_collect_data_name_1' );
				$custom_collect_data_name_2 = get_option( 'vidrack_custom_collect_data_name_2' );
				$custom_collect_data_name_3 = get_option( 'vidrack_custom_collect_data_name_3' );

				$configure_options_array[ $custom_collect_data_name_1 ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_'.$custom_collect_data_name_1, true ) );
				$configure_options_array[ $custom_collect_data_name_2 ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_'.$custom_collect_data_name_2, true ) );
				$configure_options_array[ $custom_collect_data_name_3 ] = esc_html( get_post_meta( get_the_ID(), '_vidrack_'.$custom_collect_data_name_3, true ) );


				foreach( $configure_options_array as $key=> $value ){
					if( ! $value ) continue;
					echo ucfirst( $key ).': '.$value.'<br/>';
				}
			} elseif ( 'vidrack_video_tag' === $column ) {
				echo esc_html( get_post_meta( get_the_ID(), '_vidrack_tag', true ) );
			} elseif ( 'vidrack_video_desc' === $column ) {
				echo esc_html( get_post_meta( get_the_ID(), '_vidrack_desc', true ) );
			}
		}

		/**
		 * Customize row actions from vidrack_video post type.
		 *
		 * @param Array $actions current actions.
		 * @return Array $actions updated current actions.
		 */
		function custom_row_actions( $actions ) {
			global $current_screen;
			global $pro_account;

			if ( 'vidrack_video' === $current_screen->post_type ) {
				unset( $actions['edit'] );
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
				$actions['download'] =
					'<a href="http://vidrack-media.s3.amazonaws.com/' .
					get_post( get_the_ID() )->post_title .
					'" title="Download" class="vidrack-download-video-link" rel="permalink" download>'.__( 'Download', 'video-capture' ).'</a>';
				if( $pro_account ) {
					$actions['play'] =
						'<a href="http://vidrack-media.s3.amazonaws.com/' .
						get_post( get_the_ID() )->post_title .
						'" title="Play" class="vidrack-play-video-link" rel="permalink" play>'.__( 'Play', 'video-capture' ).'</a>';
					$actions['upload_to_youtube'] = '';
					$actions['share_to'] =
						'<a class="facebook-share" href="https://www.facebook.com/sharer.php?u='.urlencode( get_permalink( get_the_ID() ) ).'" target="_blank" rel="permalink" ></a>
                        <a 
                          href="http://twitter.com/share" class="twitter-share-button"
                          data-url="'.get_permalink( get_the_ID() ).'"
                          data-text="Vidrack Video" >
                        Tweet
                        </a>';
				}
			}

			return $actions;
		}

		/**
		 * Remove video from S3 once it's deleted from Trash.
		 *
		 * @param string $post_id post id.
		 */
		function delete_video( $post_id ) {
			global $post_type;
			if ( 'vidrack_video' !== $post_type ) {
				return;
			}

			$video = get_post( $post_id );
			$url = 'https://storage.vidrack.com/video/' . $video->post_title;
			$options = array( 'http' => array( 'method' => 'DELETE' ) );
			$context  = stream_context_create( $options );
			$result = file_get_contents( $url, false, $context );
		}

		/**
		 * Set custom bulk actions on Vidrack video list page.
		 *
		 * @param Array $actions bulk actions.
		 * @return Array $actions updated bulk actions.
		 */
		function custom_bulk_actions( $actions ){
			unset( $actions['edit'] );
			return $actions;
		}

		/**
		 * Additional data posts search.
		 *
		 * @param Array $pieces query values.
		 * @return Array $pieces updated query actions.
		 */
		function alter_posts_search( $pieces ){
			global $wpdb;

			if ( isset( $_GET['post_type'] ) && 'vidrack_video' === $_GET['post_type'] && isset( $_GET['s'] ) && '' != $_GET['s'] ) { // Input var "post_type" and "s" is set.

				$search_string = like_escape($_GET['s']);
				$where = $wpdb->prepare(
					"( $wpdb->postmeta.meta_key IN( '_vidrack_desc', '_vidrack_tag')  AND $wpdb->postmeta.meta_value LIKE %s ) OR ",
					"%{$search_string}%"
				);

				$pieces['where'] = str_replace('AND (((', "AND ((({$where} ", $pieces['where']);
				$pieces['join'] = "INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )";
			}
			return $pieces;
		}

		/**
		 * Custom sortable fields.
		 *
		 * @param Array $$sortable_columns existing columns.
		 * @return Array custom columns data list.
		 */
		function vidrack_manage_sortable_columns( $sortable_columns ) {
			global $post;

			if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {

				$sortable_columns['vidrack_video_rating'] = 'vidrack_video_rating';
				$sortable_columns['vidrack_video_tag'] = 'vidrack_video_tag';
				$sortable_columns['vidrack_video_desc'] = 'vidrack_video_desc';
				$sortable_columns['vidrack_video_ip'] = 'vidrack_video_ip';
				$sortable_columns['vidrack_video_external_id'] = 'vidrack_video_external_id';

			}

			return $sortable_columns;

		}

		/**
		 * Custom queries for posts grid.
		 *
		 * @param Array $pieces existing pieces of query.
		 * @param Array $query
		 * @return Array custom pieces.
		 */
		function vidrack_manage_pre_get_posts( $pieces, $query ) {
			global $wpdb;
			global $post;

			if ( isset( $_GET['post_type'] ) && 'vidrack_video' === $_GET['post_type'] && $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {

				$order = strtoupper( $query->get( 'order' ) );
				if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
					$order = 'ASC';
				}

				switch( $orderby ) {
					case 'vidrack_video_rating':
						$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '_vidrack_video_rating'";
						$pieces[ 'orderby' ] = "wp_rd.meta_value $order";
						break;
					case 'vidrack_video_tag':
						$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '_vidrack_tag'";
						$pieces[ 'orderby' ] = "wp_rd.meta_value IS NULL OR wp_rd.meta_value = '', wp_rd.meta_value $order";
						break;
					case 'vidrack_video_desc':
						$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '_vidrack_desc'";
						$pieces[ 'orderby' ] = "wp_rd.meta_value IS NULL OR wp_rd.meta_value = '', wp_rd.meta_value $order";
						break;
					case 'vidrack_video_ip':
						$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '_vidrack_ip'";
						$pieces[ 'orderby' ] = "wp_rd.meta_value IS NULL OR wp_rd.meta_value = '', wp_rd.meta_value $order";
						break;
					case 'vidrack_video_external_id':
						$pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '_vidrack_external_id'";
						$pieces[ 'orderby' ] = "wp_rd.meta_value IS NULL OR wp_rd.meta_value = '', wp_rd.meta_value $order";
						break;
				}
			}

			return $pieces;
		}

		/**
		 * Frontend vidrack post head.
		 */
		function vidrack_post_head() {

			global $post;
			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
				$title = 'Vidrack Video';
				$desc = ( get_post_meta( $post->ID, '_vidrack_desc', true ) ) ? get_post_meta( $post->ID, '_vidrack_desc', true ) : 'Created by Vidrack Video Plugin';
				$output='
                    <meta property="og:title" content="'.$title.'" />
                    <meta property="og:description" content="'.$desc.'" />
                    <meta property="og:image" content="/wp-content/plugins/wp-video-capture/assets/banner-500x162.jpg" />
                    <meta property="og:type" content="article"/>
                    ';
				echo $output;
			}
		}

		/**
		 * Frontend vidrack post title.
		 */
		function vidrack_post_title( $title ) {

			global $post;
			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
				$title = substr( $title, 0 , ( strrpos( $title, "." ) ) );
			}
			return $title;
		}

		/**
		 * Frontend vidrack post content.
		 */
		function vidrack_post_content( $content ) {
			global $post;
			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
				$content = "<div class='vidrack-video-post-wrap'>
                                <video id='vidrack-video-post' class='video-js' controls preload='auto' data-setup='{}'>
                                    <source src='http://vidrack-media.s3.amazonaws.com/".$post->post_title."' type='video/x-flv'>
                                    <p class='vjs-no-js'>
                                      To view this video please enable JavaScript, and consider upgrading to a web browser that
                                      <a href='http://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                                    </p>
                                </video>
                                <p>
                                    Powered by <a href='http://vidrack.com' target='_blank\'>vidrack.com</a>
                                </p>
                            </div>";
			}
			return $content;
		}

		/**
		 * Check for version update.
		 */
		function update_check() {
			$installed_ver = get_option( 'vidrack_version' );

			if ( $installed_ver === VIDRACK_VERSION ) {
				return;
			}

			// [1.6] Remove old options.
			if ( version_compare( $installed_ver, '1.6', '<' ) ) {
				delete_option( 'registration_email' );
				delete_option( 'display_branding' );
			}

			// [1.7.1] Migrate videos table to custom posts and add JS callback option.
			if ( version_compare( $installed_ver, '1.7.1', '<' ) ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'video_capture';

				// Migrate data.
				$items = $wpdb->get_results( $wpdb->prepare( 'SELECT filename, ip, uploaded_at FROM  %s', $table_name ) ); // Db call ok.
				foreach ( $items as $item ) {
					$video = array(
						'post_type' => 'vidrack_video',
						'post_title' => $item->filename,
						'post_status' => 'publish',
						'post_date' => $item->uploaded_at,
					);
					$post_id = wp_insert_post( $video, true );
					add_post_meta( $post_id, '_vidrack_ip', $item->ip, true );
				}

				// Remove old database table.
				$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $table_name ) ); // Db call ok.
			}

			// Bump up the version after successful update.
			update_option( 'vidrack_version', VIDRACK_VERSION );
		}

		/**
		 * Add options on init.
		 */
		private function add_options() {
			// Add settings options.
			// 'add_option' does nothing if option already exists.
			add_option( 'vidrack_display_branding', 1 );
			add_option( 'vidrack_window_modal', 1 );
			add_option( 'vidrack_js_callback' );
			add_option( 'vidrack_desktop_upload' );
			add_option( 'vidrack_collect_email', 'no' );
			add_option( 'vidrack_collect_name', 'no' );
			add_option( 'vidrack_collect_phone', 'no' );
			add_option( 'vidrack_collect_birthday', 'no' );
			add_option( 'vidrack_collect_location', 'no' );
			add_option( 'vidrack_collect_additional_data', 'no' );
			add_option( 'vidrack_collect_language', 'no' );
			add_option( 'vidrack_version', VIDRACK_VERSION );
		}

	}

}