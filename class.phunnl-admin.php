<?php

class phunnl_Admin {
	const NONCE = 'phunnl-update-key';

	private static $initiated = false;
	private static $notices   = array();
	private static $allowed   = array(
	    'a' => array(
	        'href' => true,
	        'title' => true,
	    ),
	    'b' => array(),
	    'code' => array(),
	    'del' => array(
	        'datetime' => true,
	    ),
	    'em' => array(),
	    'i' => array(),
	    'q' => array(
	        'cite' => true,
	    ),
	    'strike' => array(),
	    'strong' => array(),
	);

	public static function init() {

		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'enter-key' ) {
			self::enter_api_key();
		}
	}

	public static function init_hooks() {
		// The standalone stats page was removed in 3.0 for an all-in-one config and stats page.
		// Redirect any links that might have been bookmarked or in browser history.
		if ( isset( $_GET['page'] ) && 'phunnl-stats-display' == $_GET['page'] ) {
			wp_safe_redirect( esc_url_raw( self::get_page_url( 'stats' ) ), 301 );
			die;
		}

		self::$initiated = true;

		add_action( 'admin_init', array( 'phunnl_Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'phunnl_Admin', 'admin_menu' ), 2 );
		add_action( 'admin_notices', array( 'phunnl_Admin', 'display_notice' ) );
		add_action( 'admin_enqueue_scripts', array( 'phunnl_Admin', 'load_resources' ) );
		add_filter( 'plugin_action_links', array( 'phunnl_Admin', 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'phunnl.php'), array( 'phunnl_Admin', 'admin_plugin_settings_link' ) );
		add_filter( 'all_plugins', array( 'phunnl_Admin', 'modify_plugin_description' ) );
	}

	public static function admin_init() {
       // esc_html_e( '************admin_init called    key iS', 'phunnl');
        load_plugin_textdomain( 'phunnl' );
	}


	public static function admin_menu() {

			self::load_menu();
	}

	public static function admin_head() {
		if ( !current_user_can( 'manage_options' ) )
			return;
	}

	public static function admin_plugin_settings_link( $links ) {
  		$settings_link = '<a href="'.esc_url( self::get_page_url() ).'">'.__('Settings', 'phunnl').'</a>';
  		array_unshift( $links, $settings_link );
  		return $links;
	}

	public static function load_menu() {


		$hook = add_options_page( __('phunnl \/', 'phunnl'), __('phunnl \/', 'phunnl'), 'manage_options', 'phunnl-key-config', array( 'phunnl_Admin', 'display_page' ) );

		if ( $hook ) {

            if ( phunnl::get_api_key() ){

                wp_register_style( 'phunnl.css', plugin_dir_url( __FILE__ ) . '_inc/phunnl.css', array(), phunnl_VERSION );
                wp_enqueue_style( 'phunnl.css');
                wp_register_script( 'phunnl.js', plugin_dir_url( __FILE__ ) . '_inc/phunnl.js', array('jquery'), phunnl_VERSION );
                wp_enqueue_script( 'phunnl.js' );
                add_menu_page( 'phunnl IVR for WordPress', 'phunnl', 'manage_options', 'phunnl-key-config', array( 'phunnl_Admin', 'display_page' ), 'dashicons-filter', 6 );
            }

		}

	}



	public static function load_resources() {
		global $hook_suffix;

			wp_register_style( 'phunnl.css', plugin_dir_url( __FILE__ ) . '_inc/phunnl.css', array(), phunnl_VERSION );
			wp_enqueue_style( 'phunnl.css');

			wp_register_script( 'phunnl.js', plugin_dir_url( __FILE__ ) . '_inc/phunnl.js', array('jquery'), phunnl_VERSION );
			wp_enqueue_script( 'phunnl.js' );


	}


    function remove_footer_admin () 
    {
    echo '<span id="footer-thankyou">|||||||||||||||||||||||||||||||||||||</span>';
    }
 
    



	/**
     * Add help to the phunnl page
	 *
	 * @return false if not the phunnl page
	 */
	public static function admin_help() {
		$current_screen = get_current_screen();

		// Screen Content
		if ( current_user_can( 'manage_options' ) ) {
			if ( !phunnl::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) ) {
				//setup page
				$current_screen->add_help_tab(
					array(
						'id'		=> 'overview',
						'title'		=> __( 'Overview' , 'phunnl'),
						'content'	=>
							'<p><strong>' . esc_html__( 'phunnl Setup' , 'phunnl') . '</strong></p>' .
							'<p>' . esc_html__( 'phunnl is IVR for WordPress eCommerce.' , 'phunnl') . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to set up the phunnl plugin.' , 'phunnl') . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'setup-signup',
						'title'		=> __( 'New to phunnl' , 'phunnl'),
						'content'	=>
							'<p><strong>' . esc_html__( 'phunnl Setup' , 'phunnl') . '</strong></p>' .
							'<p>' . esc_html__( 'You need to enter a Secret Key to activate the phunnl service on your site.' , 'phunnl') . '</p>' .
							'<p>' . sprintf( __( 'Sign up for an account on %s to get a Secret Key.' , 'phunnl'), '<a href="https://signup.phunnl.com/phunnl_signup.html" target="_blank">phunnl.com</a>' ) . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'setup-manual',
						'title'		=> __( 'Enter a Secret Key' , 'phunnl'),
						'content'	=>
							'<p><strong>' . esc_html__( 'phunnl Setup' , 'phunnl') . '</strong></p>' .
							'<p>' . esc_html__( 'If you already have a Secret Key' , 'phunnl') . '</p>' .
							'<ol>' .
								'<li>' . esc_html__( 'Copy and paste the Secret Key into the text field.' , 'phunnl') . '</li>' .
								'<li>' . esc_html__( 'Click the Connect with Secret Key button.' , 'phunnl') . '</li>' .
							'</ol>',
					)
				);
			}
           
			else {
				//configuration page
				$current_screen->add_help_tab(
					array(
						'id'		=> 'overview',
						'title'		=> __( 'Overview' , 'phunnl'),
						'content'	=>
							'<p><strong>' . esc_html__( 'phunnl Configuration' , 'phunnl') . '</strong></p>' .
							'<p>' . esc_html__( 'phunnl is IVR for WordPress eCommerce.' , 'phunnl') . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to update your phunnl settings.' , 'phunnl') . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'settings',
						'title'		=> __( 'Settings' , 'phunnl'),
						'content'	=>
							'<p><strong>' . esc_html__( 'phunnl Configuration' , 'phunnl') . '</strong></p>' .
							( phunnl::predefined_api_key() ? '' : '<p><strong>' . esc_html__( 'Secret Key' , 'phunnl') . '</strong> - ' . esc_html__( 'Enter/remove a Secret Key.' , 'phunnl') . '</p>' )

					)
				);

				if ( ! phunnl::predefined_api_key() ) {
					$current_screen->add_help_tab(
						array(
							'id'		=> 'account',
							'title'		=> __( 'Account' , 'phunnl'),
							'content'	=>
								'<p><strong>' . esc_html__( 'phunnl Configuration' , 'phunnl') . '</strong></p>' .
								'<p><strong>' . esc_html__( 'Subscription Type' , 'phunnl') . '</strong> - ' . esc_html__( 'The phunnl subscription plan' , 'phunnl') . '</p>' .
								'<p><strong>' . esc_html__( 'Status' , 'phunnl') . '</strong> - ' . esc_html__( 'The subscription status - active, cancelled or suspended' , 'phunnl') . '</p>' .
                                '<p><strong>' . esc_html__( 'Next Billing Date' , 'phunnl') . '</strong> - ' . esc_html__( 'The next scheduled auto-billing date for this subscription' , 'phunnl') . '</p>'
						)
					);
				}
			}
		}

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:' , 'phunnl') . '</strong></p>' .
            //'<p><a href="https://phunnl.com/faq/" target="_blank">'     . esc_html__( 'phunnl FAQ' , 'phunnl') . '</a></p>' .
            '<p>Support Number: ' . esc_html__( '+1 619-695-0033 ' , 'phunnl') . '</a></p>'.
			'<p><a href="https://phunnl.com/contact.html" target="_blank">' . esc_html__( 'phunnl Support' , 'phunnl') . '</a></p>'
		);
	}

	public static function enter_api_key() {
		if ( ! current_user_can( 'manage_options' ) ) {
           // esc_html_e( '************current_user_can( manage_options', 'phunnl');
			die( __( 'Cheatin&#8217; uh?', 'phunnl' ) );
		}

		if ( !wp_verify_nonce( $_POST['_wpnonce'], self::NONCE ) )
			return false;


		if ( phunnl::predefined_api_key() ) {
			return false; //shouldn't have option to save key if already defined
		}

		$new_key = preg_replace( '/[^a-f0-9]/i', '', $_POST['key'] );
		$old_key = phunnl::get_api_key();

		if ( empty( $new_key ) ) {
			if ( !empty( $old_key ) ) {
				delete_option( 'wordpress_phunnl_api_key' );
				self::$notices[] = 'new-key-empty';
			}
		}
		elseif ( $new_key != $old_key ) {
			self::save_key( $new_key );
		}

		return true;
	}

	public static function save_key( $api_key ) {
		$key_status = phunnl::verify_key( $api_key );



		if ( $key_status == 'valid' ) {

			$phunnl_user = self::get_phunnl_user( $api_key );

			if ( $phunnl_user ) {

                if ( in_array( $phunnl_user->status, array( 'active', 'active-dunning', 'no-sub' ) ) )

                  //  esc_html_e( '************ save_key thing called    key iS' .  $api_key , 'phunnl');
                    update_option( 'wordpress_phunnl_api_key', $api_key );

				if ( $phunnl_user->status == 'active' )
					self::$notices['status'] = 'new-key-valid';

                elseif ( $phunnl_user->status == 'notice' )
                    self::$notices['status'] = $phunnl_user;

                else
                    self::$notices['status'] = $phunnl_user->status;
			}
			else
				self::$notices['status'] = 'new-key-invalid';




		}
		elseif ( in_array( $key_status, array( 'invalid', 'failed' ) ) )
			self::$notices['status'] = 'new-key-'.$key_status;
	}



	public static function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( plugin_dir_url( __FILE__ ) . '/phunnl.php' ) ) {
			$links[] = '<a href="' . esc_url( self::get_page_url() ) . '">'.esc_html__( 'Settings' , 'phunnl').'</a>';
		}

		return $links;
	}



	// Check connectivity between the WordPress blog and phunnl's servers.
	// Returns an associative array of server IP addresses, where the key is the IP address, and value is true (available) or false (unable to connect).
	public static function check_server_ip_connectivity() {

		$servers = $ips = array();

        //// Some web hosts may disable this function
        //if ( function_exists('gethostbynamel') ) {

        //    $ips = gethostbynamel( 'rest.phunnl.com' );
        //    if ( $ips && is_array($ips) && count($ips) ) {
        //        $api_key = phunnl::get_api_key();

        //        foreach ( $ips as $ip ) {
        //            $response = phunnl::verify_key( $api_key, $ip );
        //            // even if the key is invalid, at least we know we have connectivity
        //            if ( $response == 'valid' || $response == 'invalid' )
        //                $servers[$ip] = 'connected';
        //            else
        //                $servers[$ip] = $response ? $response : 'unable to connect';
        //        }
        //    }
        //}

		return $servers;
	}

	// Simpler connectivity check
	public static function check_server_connectivity($cache_timeout = 86400) {


        //$debug = array();
        //$debug[ 'PHP_VERSION' ]         = PHP_VERSION;
        //$debug[ 'WORDPRESS_VERSION' ]   = $GLOBALS['wp_version'];
        //$debug[ 'phunnl_VERSION' ]     = phunnl_VERSION;
        //$debug[ 'phunnl__PLUGIN_DIR' ] = phunnl__PLUGIN_DIR;
        //$debug[ 'SITE_URL' ]            = site_url();
        //$debug[ 'HOME_URL' ]            = home_url();

        //$servers = get_option('phunnl_available_servers');
        //if ( (time() - get_option('phunnl_connectivity_time') < $cache_timeout) && $servers !== false ) {
        //    $servers = self::check_server_ip_connectivity();
        //    update_option('phunnl_available_servers', $servers);
        //    update_option('phunnl_connectivity_time', time());
        //}

        //if ( wp_http_supports( array( 'ssl' ) ) ) {
        //    $response = wp_remote_get( 'https://api.phunnl.com/plugin/1_0' );
        //}


        //$debug[ 'gethostbynamel' ]  = function_exists('gethostbynamel') ? 'exists' : 'not here';
        //$debug[ 'Servers' ]         = $servers;
         //$debug[ 'Test Connection' ] = $response;

         //phunnl::log( $debug );

		//if ( $response && 'connected' == wp_remote_retrieve_body( $response ) )
			return true;

		//return false;
	}

	// Check the server connectivity and store the available servers in an option.
	public static function get_server_connectivity($cache_timeout = 86400) {
		return self::check_server_connectivity( $cache_timeout );
	}


	public static function get_page_url( $page = 'config' ) {

		$args = array( 'page' => 'phunnl-key-config' );

		if ( $page == 'stats' )
			$args = array( 'page' => 'phunnl-key-config', 'view' => 'stats' );
		elseif ( $page == 'delete_key' )
			$args = array( 'page' => 'phunnl-key-config', 'view' => 'start', 'action' => 'delete-key', '_wpnonce' => wp_create_nonce( self::NONCE ) );

		$url = add_query_arg( $args, class_exists( 'phunnl' ) ? admin_url( 'admin.php' ) : admin_url( 'options-general.php' ) );

		return $url;
	}

    // gets the current subscription info for a given phunnl Secret Key
	public static function get_phunnl_user( $api_key ) {
		$phunnl_user = false;

        //esc_html_e( '************ get_phunnl_user called : ' , 'phunnl');

         //get subscription here
		$subscription_verification = phunnl::http_post( phunnl::build_query( array( 'key' => $api_key) ), 'get-subscription' );


        if ( ! empty( $subscription_verification[1] ) ) {

            if ( 'invalid' !== $subscription_verification[1] ) {


                $phunnl_user = json_decode( $subscription_verification[1] );

            }
        }

       // esc_html_e( '************ get_phunnl_user end : ' , 'phunnl');

		return $phunnl_user;
	}



	public static function verify_wpcom_key( $api_key, $user_id, $extra = array() ) {
		$phunnl_account = phunnl::http_post( phunnl::build_query( array_merge( array(
			'user_id'          => $user_id,
			'api_key'          => $api_key,
			'get_account_type' => 'true'
		), $extra ) ), 'verify-wpcom-key' );

		if ( ! empty( $phunnl_account[1] ) )
			$phunnl_account = json_decode( $phunnl_account[1] );

		phunnl::log( compact( 'phunnl_account' ) );

		return $phunnl_account;
	}


	public static function display_alert() {
		phunnl::view( 'notice', array(
			'type' => 'alert',
			'code' => (int) get_option( 'phunnl_alert_code' ),
			'msg'  => get_option( 'phunnl_alert_msg' )
		) );
	}



	public static function display_api_key_warning() {
		phunnl::view( 'notice', array( 'type' => 'plugin' ) );
	}

	public static function display_page() {
		if ( !phunnl::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) )
			self::display_start_page();
        //elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'stats' )
        //    self::display_stats_page();
		else
			//self::display_configuration_page();
            self::display_configuration_page();
	}

	public static function display_start_page() {
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'delete-key' ) {
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], self::NONCE ) )
					delete_option( 'wordpress_phunnl_api_key' );
			}
		}

		if ( $api_key = phunnl::get_api_key() && ( empty( self::$notices['status'] ) || 'existing-key-invalid' != self::$notices['status'] ) ) {
			self::display_configuration_page();
			return;
		}

		$phunnl_user = false;

		if ( isset( $_GET['token'] ) && preg_match('/^(\d+)-[0-9a-f]{20}$/', $_GET['token'] ) )
			$phunnl_user = self::verify_wpcom_key( '', '', array( 'token' => $_GET['token'] ) );

		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'save-key' ) {
				if ( is_object( $phunnl_user ) ) {
					self::save_key( $phunnl_user->api_key );
					self::display_configuration_page();
					return;
				}
			}
		}

		phunnl::view( 'start', compact( 'phunnl_user' ) );


	}

	public static function display_stats_page() {
		phunnl::view( 'stats' );
	}

	public static function display_configuration_page() {
		$api_key      = phunnl::get_api_key();
		$phunnl_user = self::get_phunnl_user( $api_key );

		if ( ! $phunnl_user ) {
			// This could happen if the user's key became invalid after it was previously valid and successfully set up.
			self::$notices['status'] = 'existing-key-invalid';
			self::display_start_page();
			return;
		}





        $notices = array();



		phunnl::view( 'config', compact( 'api_key', 'phunnl_user', 'stat_totals', 'notices' ) );
	}

	public static function display_notice() {
		global $hook_suffix;

        //todo this is where we would show any account or plugin notices

        //if ( in_array( $hook_suffix, array( 'jetpack_page_phunnl-key-config', 'settings_page_phunnl-key-config' ) ) ) {
        //    // This page manages the notices and puts them inline where they make sense.
        //    return;
        //}

        if ( in_array( $hook_suffix, array( 'edit-comments.php' ) ) && (int) get_option( 'phunnl_alert_code' ) > 0 ) {
            phunnl::verify_key( phunnl::get_api_key() ); //verify that the key is still in alert state

            if ( get_option( 'phunnl_alert_code' ) > 0 )
                self::display_alert();
        }
        elseif ( $hook_suffix == 'plugins.php' && !phunnl::get_api_key() ) {
            self::display_api_key_warning();
        }

	}

	public static function display_status() {


		if ( ! self::get_server_connectivity() ) {
			phunnl::view( 'notice', array( 'type' => 'servers-be-down' ) );
		}
		else if ( ! empty( self::$notices ) ) {
			foreach ( self::$notices as $index => $type ) {
				if ( is_object( $type ) ) {
					$notice_header = $notice_text = '';

					if ( property_exists( $type, 'notice_header' ) ) {
						$notice_header = wp_kses( $type->notice_header, self::$allowed );
					}

					if ( property_exists( $type, 'notice_text' ) ) {
						$notice_text = wp_kses( $type->notice_text, self::$allowed );
					}

					if ( property_exists( $type, 'status' ) ) {
						$type = wp_kses( $type->status, self::$allowed );
						phunnl::view( 'notice', compact( 'type', 'notice_header', 'notice_text' ) );

						unset( self::$notices[ $index ] );
					}
				}
				else {
					phunnl::view( 'notice', compact( 'type' ) );

					unset( self::$notices[ $index ] );
				}
			}
		}
	}




	/**
	 * When phunnl is active, remove the "Activate phunnl" step from the plugin description.
	 */
	public static function modify_plugin_description( $all_plugins ) {
		if ( isset( $all_plugins['phunnl/phunnl.php'] ) ) {
			if ( phunnl::get_api_key() ) {
				$all_plugins['phunnl/phunnl.php']['Description'] = __( 'phunnl is a WordPress eCommerce extension for that gives small businesses professional IVR services. phunnl drives savings and delivers ROI based on your organization\'s existing Customer Service workflow.', 'phunnl' );
			}
			else {
				$all_plugins['phunnl/phunnl.php']['Description'] = __( 'phunnl is a WordPress eCommerce extension for that gives small businesses professional IVR services. phunnl drives savings and delivers ROI based on your organization\'s existing Customer Service workflow.', 'phunnl' );
            }
		}
         
		return $all_plugins;
	}

 
}
