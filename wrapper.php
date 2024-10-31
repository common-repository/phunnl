<?php

global $WPCOM_PHUNNL_API_KEY, $phunnl_api_host, $phunnl_api_port;

$WPCOM_PHUNNL_API_KEY    = defined( 'WPCOM_PHUNNL_API_KEY' ) ? constant( 'WPCOM_PHUNNL_API_KEY' ) : '';
 

function phunnl_test_mode() {
	return phunnl::is_test_mode();
}

function phunnl_http_post( $request, $host, $path, $port = 80, $ip = null ) {
	$path = str_replace( '/1.1/', '', $path );

	return phunnl::http_post( $request, $path, $ip );
}

function phunnl_microtime() {
	return phunnl::_get_microtime();
}

function phunnl_delete_old() {
	return phunnl::delete_old_comments();
}

function phunnl_delete_old_metadata() {
	return phunnl::delete_old_comments_meta();
}

function phunnl_check_db_comment( $id, $recheck_reason = 'recheck_queue' ) {
   	return phunnl::check_db_comment( $id, $recheck_reason );
}

function phunnl_rightnow() {
	if ( !class_exists( 'phunnl_Admin' ) )
		return false;

   	return phunnl_Admin::rightnow_stats();
}

function phunnl_admin_init() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_version_warning() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_load_js_and_css() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_nonce_field( $action = -1 ) {
	return wp_nonce_field( $action );
}
function phunnl_plugin_action_links( $links, $file ) {
	return phunnl_Admin::plugin_action_links( $links, $file );
}
function phunnl_conf() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_stats_display() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_stats() {
	return phunnl_Admin::dashboard_stats();
}
function phunnl_admin_warnings() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_comment_row_action( $a, $comment ) {
	return phunnl_Admin::comment_row_actions( $a, $comment );
}
function phunnl_comment_status_meta_box( $comment ) {
	return phunnl_Admin::comment_status_meta_box( $comment );
}
function phunnl_comments_columns( $columns ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	return $columns;
}
function phunnl_comment_column_row( $column, $comment_id ) {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_text_add_link_callback( $m ) {
	return phunnl_Admin::text_add_link_callback( $m );
}
function phunnl_text_add_link_class( $comment_text ) {
	return phunnl_Admin::text_add_link_class( $comment_text );
}
function phunnl_check_for_spam_button( $comment_status ) {
	return phunnl_Admin::check_for_spam_button( $comment_status );
}
function phunnl_submit_nonspam_comment( $comment_id ) {
	return phunnl::submit_nonspam_comment( $comment_id );
}
function phunnl_submit_spam_comment( $comment_id ) {
	return phunnl::submit_spam_comment( $comment_id );
}
function phunnl_transition_comment_status( $new_status, $old_status, $comment ) {
	return phunnl::transition_comment_status( $new_status, $old_status, $comment );
}
function phunnl_spam_count( $type = false ) {
	return phunnl_Admin::get_spam_count( $type );
}
function phunnl_recheck_queue() {
	return phunnl_Admin::recheck_queue();
}
function phunnl_remove_comment_author_url() {
	return phunnl_Admin::remove_comment_author_url();
}
function phunnl_add_comment_author_url() {
	return phunnl_Admin::add_comment_author_url();
}
function phunnl_check_server_connectivity() {
	return phunnl_Admin::check_server_connectivity();
}
function phunnl_get_server_connectivity( $cache_timeout = 86400 ) {
	return phunnl_Admin::get_server_connectivity( $cache_timeout );
}
function phunnl_server_connectivity_ok() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return true;
}
function phunnl_admin_menu() {
	return phunnl_Admin::admin_menu();
}
function phunnl_load_menu() {
	return phunnl_Admin::load_menu();
}
function phunnl_init() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_get_key() {
	return phunnl::get_api_key();
}
function phunnl_check_key_status( $key, $ip = null ) {
	return phunnl::check_key_status( $key, $ip );
}
function phunnl_update_alert( $response ) {
	return phunnl::update_alert( $response );
}
function phunnl_verify_key( $key, $ip = null ) {
	return phunnl::verify_key( $key, $ip );
}
function phunnl_get_user_roles( $user_id ) {
	return phunnl::get_user_roles( $user_id );
}
function phunnl_result_spam( $approved ) {
	return phunnl::comment_is_spam( $approved );
}
function phunnl_result_hold( $approved ) {
	return phunnl::comment_needs_moderation( $approved );
}
function phunnl_get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url ) {
	return phunnl::get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url );
}
function phunnl_update_comment_history( $comment_id, $message, $event = null ) {
	return phunnl::update_comment_history( $comment_id, $message, $event );
}
function phunnl_get_comment_history( $comment_id ) {
	return phunnl::get_comment_history( $comment_id );
}
function phunnl_cmp_time( $a, $b ) {
	return phunnl::_cmp_time( $a, $b );
}
function phunnl_auto_check_update_meta( $id, $comment ) {
	return phunnl::auto_check_update_meta( $id, $comment );
}
function phunnl_auto_check_comment( $commentdata ) {
	return phunnl::auto_check_comment( $commentdata );
}
function phunnl_get_ip_address() {
	return phunnl::get_ip_address();
}
function phunnl_cron_recheck() {
	return phunnl::cron_recheck();
}
function phunnl_add_comment_nonce( $post_id ) {
	return phunnl::add_comment_nonce( $post_id );
}
function phunnl_fix_scheduled_recheck() {
	return phunnl::fix_scheduled_recheck();
}
function phunnl_spam_comments() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return array();
}
function phunnl_spam_totals() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return array();
}
function phunnl_manage_page() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_caught() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function redirect_old_phunnl_urls() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function phunnl_kill_proxy_check( $option ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	return 0;
}
function phunnl_pingback_forwarded_for( $r, $url ) {
	return phunnl::pingback_forwarded_for( $r, $url );
}
function phunnl_pre_check_pingback( $method ) {
	return phunnl::pre_check_pingback( $method );
}