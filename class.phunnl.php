<?php

class phunnl {
	const API_HOST = 'api.phunnl.com';
	const API_PORT = 80;
	const MAX_DELAY_BEFORE_MODERATION_EMAIL = 86400; // One day in seconds

	private static $last_comment = '';
	private static $initiated = false;
	private static $prevent_moderation_email_for_these_comments = array();
	private static $last_comment_result = null;
	private static $comment_as_submitted_allowed_keys = array( 'blog' => '', 'blog_charset' => '', 'blog_lang' => '', 'blog_ua' => '', 'comment_agent' => '', 'comment_author' => '', 'comment_author_IP' => '', 'comment_author_email' => '', 'comment_author_url' => '', 'comment_content' => '', 'comment_date_gmt' => '', 'comment_tags' => '', 'comment_type' => '', 'guid' => '', 'is_test' => '', 'permalink' => '', 'reporter' => '', 'site_domain' => '', 'submit_referer' => '', 'submit_uri' => '', 'user_ID' => '', 'user_agent' => '', 'user_id' => '', 'user_ip' => '' );
	private static $is_rest_api_call = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

		add_action( 'wp_insert_comment', array( 'phunnl', 'auto_check_update_meta' ), 10, 2 );
		add_filter( 'preprocess_comment', array( 'phunnl', 'auto_check_comment' ), 1 );
		add_filter( 'rest_pre_insert_comment', array( 'phunnl', 'rest_auto_check_comment' ), 1 );

		add_action( 'phunnl_scheduled_delete', array( 'phunnl', 'delete_old_comments' ) );
		add_action( 'phunnl_scheduled_delete', array( 'phunnl', 'delete_old_comments_meta' ) );
		add_action( 'phunnl_schedule_cron_recheck', array( 'phunnl', 'cron_recheck' ) );

		add_action( 'comment_form',  array( 'phunnl',  'add_comment_nonce' ), 1 );

		add_action( 'admin_head-edit-comments.php', array( 'phunnl', 'load_form_js' ) );
		add_action( 'comment_form', array( 'phunnl', 'load_form_js' ) );
		add_action( 'comment_form', array( 'phunnl', 'inject_ak_js' ) );
		add_filter( 'script_loader_tag', array( 'phunnl', 'set_form_js_async' ), 10, 3 );

		add_filter( 'comment_moderation_recipients', array( 'phunnl', 'disable_moderation_emails_if_unreachable' ), 1000, 2 );
		add_filter( 'pre_comment_approved', array( 'phunnl', 'last_comment_status' ), 10, 2 );

		add_action( 'transition_comment_status', array( 'phunnl', 'transition_comment_status' ), 10, 3 );

		// Run this early in the pingback call, before doing a remote fetch of the source uri
		add_action( 'xmlrpc_call', array( 'phunnl', 'pre_check_pingback' ) );

		// Jetpack compatibility
		add_filter( 'phunnl_options_whitelist', array( 'phunnl', 'add_to_phunnl_options_whitelist' ) );
		add_action( 'update_option_wordpress_phunnl_api_key', array( 'phunnl', 'updated_option' ), 10, 2 );
	}

	public static function get_api_key() {

       // esc_html_e('************ get_api_key  called', 'phunnl');
		return apply_filters( 'phunnl_get_api_key', defined('WPCOM_PHUNNL_API_KEY') ? constant('WPCOM_PHUNNL_API_KEY') : get_option('wordpress_phunnl_api_key') );
	}

	public static function check_key_status( $key, $ip = null ) {

        //esc_html_e( '************ check_key_status  called', 'phunnl');
		return self::http_post( phunnl::build_query( array( 'key' => $key ) ), 'verify-key', $ip );
	}

	public static function verify_key( $key, $ip = null ) {
		$response = self::check_key_status( $key, $ip );

         phunnl::log($response[1]);

		if ( $response[1] != 'valid' && $response[1] != 'invalid' )
			return 'failed';

		return $response[1];
	}

	public static function deactivate_key( $key ) {
		$response = self::http_post( phunnl::build_query( array( 'key' => $key, 'blog' => get_option( 'home' ) ) ), 'deactivate' );

		if ( $response[1] != 'deactivated' )
			return 'failed';

		return $response[1];
	}

	/**
	 * Add the phunnl option to the phunnl options management whitelist.
	 *
	 * @param array $options The list of whitelisted option names.
	 * @return array The updated whitelist
	 */
	public static function add_to_phunnl_options_whitelist( $options ) {
		$options[] = 'wordpress_phunnl_api_key';
		return $options;
	}

	/**
	 * When the phunnl option is updated, run the registration call.
	 *
	 * This should only be run when the option is updated from the Jetpack/WP.com
	 * API call, and only if the new key is different than the old key.
	 *
	 * @param mixed  $old_value   The old option value.
	 * @param mixed  $value       The new option value.
	 */
	public static function updated_option( $old_value, $value ) {
		// Not an API call
		if ( ! class_exists( 'WPCOM_JSON_API_Update_Option_Endpoint' ) ) {
			return;
		}
		// Only run the registration if the old key is different.
		if ( $old_value !== $value ) {
			self::verify_key( $value );
		}
	}




	public static function is_test_mode() {
		return defined('phunnl_TEST_MODE') && phunnl_TEST_MODE;
	}

	public static function allow_discard() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return false;
		if ( is_user_logged_in() )
			return false;

		return ( get_option( 'phunnl_strictness' ) === '1'  );
	}

	public static function get_ip_address() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
	}



	private static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}

	private static function get_referer() {
		return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
	}

	// return a comma-separated list of role names for the given user
	public static function get_user_roles( $user_id ) {
		$roles = false;

		if ( !class_exists('WP_User') )
			return false;

		if ( $user_id > 0 ) {
			$comment_user = new WP_User( $user_id );
			if ( isset( $comment_user->roles ) )
				$roles = join( ',', $comment_user->roles );
		}

		if ( is_multisite() && is_super_admin( $user_id ) ) {
			if ( empty( $roles ) ) {
				$roles = 'admin';
			} else {
				$comment_user->roles[] = 'admin';
				$roles = join( ',', $comment_user->roles );
			}
		}

		return $roles;
	}



	public static function _cmp_time( $a, $b ) {
		return $a['time'] > $b['time'] ? -1 : 1;
	}

	public static function _get_microtime() {
		$mtime = explode( ' ', microtime() );
		return $mtime[1] + $mtime[0];
	}

	/**
	 * Make a POST request to the phunnl API.
	 *
	 * @param string $request The body of the request.
	 * @param string $path The path for the request.
	 * @param string $ip The specific IP address to hit.
     * @return array A two-member array consisting of the headers and the response body, both empty in the case of a failure.
     */
	public static function http_post( $request, $path, $ip=null ) {

		$phunnl_ua = sprintf( 'WordPress/%s | phunnl/%s', $GLOBALS['wp_version'], constant( 'phunnl_VERSION' ) );
		$phunnl_ua = apply_filters( 'phunnl_ua', $phunnl_ua );

		$content_length = strlen( $request );

		$api_key   = self::get_api_key();
		$host      = self::API_HOST;

        //if ( !empty( $api_key ) )
        //    $host = $api_key.'.'.$host;

		$http_host = $host;
		// use a specific IP if provided
		// needed by phunnl_Admin::check_server_connectivity()
        //if ( $ip && long2ip( ip2long( $ip ) ) ) {
        //    $http_host = $ip;
        //}

		$http_args = array(
			'body' => $request,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
				'Host' => $host,
				'User-Agent' => $phunnl_ua,
			),
			'httpversion' => '1.0',
			'timeout' => 15
		);

		$phunnl_url = $http_phunnl_url = "http://{$http_host}/plugin/1_0/{$path}";

		/**
		 * Try SSL first; if that fails, try without it and don't try it again for a while.
		 */

		$ssl = $ssl_failed = false;

		// Check if SSL requests were disabled fewer than X hours ago.
		$ssl_disabled = get_option( 'phunnl_ssl_disabled' );

		if ( $ssl_disabled && $ssl_disabled < ( time() - 60 * 60 * 24 ) ) { // 24 hours
			$ssl_disabled = false;
			delete_option( 'phunnl_ssl_disabled' );
		}
		else if ( $ssl_disabled ) {
			do_action( 'phunnl_ssl_disabled' );
		}

		if ( ! $ssl_disabled && ( $ssl = wp_http_supports( array( 'ssl' ) ) ) ) {
			$phunnl_url = set_url_scheme( $phunnl_url, 'https' );

			do_action( 'phunnl_https_request_pre' );
		}

		$response = wp_remote_post( $phunnl_url, $http_args );

		phunnl::log( compact( 'phunnl_url', 'http_args', 'response' ) );

		if ( $ssl && is_wp_error( $response ) ) {
			do_action( 'phunnl_https_request_failure', $response );

			// Intermittent connection problems may cause the first HTTPS
			// request to fail and subsequent HTTP requests to succeed randomly.
			// Retry the HTTPS request once before disabling SSL for a time.
			$response = wp_remote_post( $phunnl_url, $http_args );

			phunnl::log( compact( 'phunnl_url', 'http_args', 'response' ) );

			if ( is_wp_error( $response ) ) {
				$ssl_failed = true;

				do_action( 'phunnl_https_request_failure', $response );

				do_action( 'phunnl_http_request_pre' );

				// Try the request again without SSL.
				$response = wp_remote_post( $http_phunnl_url, $http_args );

				phunnl::log( compact( 'http_phunnl_url', 'http_args', 'response' ) );

               // esc_html_e( 'api: response: ' .$response, 'phunnl' );
			}
		}

		if ( is_wp_error( $response ) ) {
			do_action( 'phunnl_request_failure', $response );

			return array( '', '' );
		}

		if ( $ssl_failed ) {
			// The request failed when using SSL but succeeded without it. Disable SSL for future requests.
			update_option( 'phunnl_ssl_disabled', time() );

			do_action( 'phunnl_https_disabled' );
		}

		$simplified_response = array( $response['headers'], $response['body'] );

        //esc_html_e( 'response headers: ' .$simplified_response[0], 'phunnl' );
        //esc_html_e( 'response headers body: ' .$simplified_response[1], 'phunnl' );



		self::update_alert( $simplified_response );

		return $simplified_response;
	}

	// given a response from an API call like check_key_status(), update the alert code options if an alert is present.
	public static function update_alert( $response ) {
		$code = $msg = null;
		if ( isset( $response[0]['x-phunnl-alert-code'] ) ) {
			$code = $response[0]['x-phunnl-alert-code'];
			$msg  = $response[0]['x-phunnl-alert-msg'];
		}

		// only call update_option() if the value has changed
		if ( $code != get_option( 'phunnl_alert_code' ) ) {
			if ( ! $code ) {
				delete_option( 'phunnl_alert_code' );
				delete_option( 'phunnl_alert_msg' );
			}
			else {
				update_option( 'phunnl_alert_code', $code );
				update_option( 'phunnl_alert_msg', $msg );
			}
		}
	}

	public static function load_form_js() {
		wp_register_script( 'phunnl-form', plugin_dir_url( __FILE__ ) . '_inc/form.js', array(), phunnl_VERSION, true );
		wp_enqueue_script( 'phunnl-form' );
	}

	/**
	 * Mark form.js as async. Because nothing depends on it, it can run at any time
	 * after it's loaded, and the browser won't have to wait for it to load to continue
	 * parsing the rest of the page.
	 */
	public static function set_form_js_async( $tag, $handle, $src ) {
		if ( 'phunnl-form' !== $handle ) {
			return $tag;
		}

		return preg_replace( '/^<script /i', '<script async="async" ', $tag );
	}

	public static function inject_ak_js( $fields ) {
		echo '<p style="display: none;">';
		echo '<input type="hidden" id="ak_js" name="ak_js" value="' . mt_rand( 0, 250 ) . '"/>';
		echo '</p>';
	}

	private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$phunnl = plugin_basename( phunnl__PLUGIN_DIR . 'phunnl.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $phunnl ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

	public static function view( $name, array $args = array() ) {
		$args = apply_filters( 'phunnl_view_arguments', $args, $name );

		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}

		load_plugin_textdomain( 'phunnl' );

		$file = phunnl__PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], phunnl__MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'phunnl' );

			$message = '<strong>'.sprintf(esc_html__( 'phunnl %s requires WordPress %s or higher.' , 'phunnl'), phunnl_VERSION, phunnl__MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the phunnl plugin</a>.', 'phunnl'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/phunnl/download/');

			phunnl::bail_on_activation( $message );
		}
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		self::deactivate_key( self::get_api_key() );

		// Remove any scheduled cron jobs.
        //$phunnl_cron_events = array(
        //    'phunnl_schedule_cron_recheck',
        //    'phunnl_scheduled_delete',
        //);

		foreach ( $phunnl_cron_events as $phunnl_cron_event ) {
			$timestamp = wp_next_scheduled( $phunnl_cron_event );

			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $phunnl_cron_event );
			}
		}
	}

	/**
	 * Essentially a copy of WP's build_query but one that doesn't expect pre-urlencoded values.
	 *
	 * @param array $args An array of key => value pairs
	 * @return string A string ready for use as a URL query string.
	 */
	public static function build_query( $args ) {
		return _http_build_query( $args, '', '&' );
	}

	/**
	 * Log debugging info to the error log.
	 *
	 * Enabled when WP_DEBUG_LOG is enabled (and WP_DEBUG, since according to
	 * core, "WP_DEBUG_DISPLAY and WP_DEBUG_LOG perform no function unless
	 * WP_DEBUG is true), but can be disabled via the phunnl_debug_log filter.
	 *
	 * @param mixed $phunnl_debug The data to log.
	 */
	public static function log( $phunnl_debug ) {
		if ( apply_filters( 'phunnl_debug_log', defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG) ) {
			error_log( print_r( compact( 'phunnl_debug' ), true ) );
		}
	}

	public static function pre_check_pingback( $method ) {
		if ( $method !== 'pingback.ping' )
			return;

		global $wp_xmlrpc_server;

		if ( !is_object( $wp_xmlrpc_server ) )
			return false;

		// Lame: tightly coupled with the IXR class.
		$args = $wp_xmlrpc_server->message->params;

		if ( !empty( $args[1] ) ) {
			$post_id = url_to_postid( $args[1] );

			// If this gets through the pre-check, make sure we properly identify the outbound request as a pingback verification
			phunnl::pingback_forwarded_for( null, $args[0] );
			add_filter( 'http_request_args', array( 'phunnl', 'pingback_forwarded_for' ), 10, 2 );

			$comment = array(
				'comment_author_url' => $args[0],
				'comment_post_ID' => $post_id,
				'comment_author' => '',
				'comment_author_email' => '',
				'comment_content' => '',
				'comment_type' => 'pingback',
				'phunnl_pre_check' => '1',
				'comment_pingback_target' => $args[1],
			);

			$comment = phunnl::auto_check_comment( $comment );

			if ( isset( $comment['phunnl_result'] ) && 'true' == $comment['phunnl_result'] ) {
				// Lame: tightly coupled with the IXR classes. Unfortunately the action provides no context and no way to return anything.
				$wp_xmlrpc_server->error( new IXR_Error( 0, 'Invalid discovery target' ) );
			}
		}
	}

	public static function pingback_forwarded_for( $r, $url ) {
		static $urls = array();

		// Call this with $r == null to prime the callback to add headers on a specific URL
		if ( is_null( $r ) && !in_array( $url, $urls ) ) {
			$urls[] = $url;
		}

		// Add X-Pingback-Forwarded-For header, but only for requests to a specific URL (the apparent pingback source)
		if ( is_array( $r ) && is_array( $r['headers'] ) && !isset( $r['headers']['X-Pingback-Forwarded-For'] ) && in_array( $url, $urls ) ) {
			$remote_ip = preg_replace( '/[^a-fx0-9:.,]/i', '', $_SERVER['REMOTE_ADDR'] );

			// Note: this assumes REMOTE_ADDR is correct, and it may not be if a reverse proxy or CDN is in use
			$r['headers']['X-Pingback-Forwarded-For'] = $remote_ip;

			// Also identify the request as a pingback verification in the UA string so it appears in logs
			$r['user-agent'] .= '; verifying pingback from ' . $remote_ip;
		}

		return $r;
	}

	/**
	 * Ensure that we are loading expected scalar values from phunnl_as_submitted commentmeta.
	 *
	 * @param mixed $meta_value
	 * @return mixed
	 */
	private static function sanitize_comment_as_submitted( $meta_value ) {
		if ( empty( $meta_value ) ) {
			return $meta_value;
		}

		$meta_value = (array) $meta_value;

		foreach ( $meta_value as $key => $value ) {
			if ( ! isset( self::$comment_as_submitted_allowed_keys[$key] ) || ! is_scalar( $value ) ) {
				unset( $meta_value[$key] );
			}
		}

		return $meta_value;
	}

	public static function predefined_api_key() {
		if ( defined( 'WPCOM_PHUNNL_API_KEY' ) ) {
			return true;
		}

		return apply_filters( 'phunnl_predefined_api_key', false );
	}

  
}
