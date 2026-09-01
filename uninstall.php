<?php
/**
 * Uninstall
 */


/**
 * Exit if uninstall is not called from WordPress.
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


delete_option( 'smredirect_enabled' );
delete_option( 'smredirect_page_id' );
delete_option( 'smredirect_url' );
delete_option( 'smredirect_omit_pages' );
delete_metadata( 'user', 0, 'smredirect_dismissed_moved_notice', '', true );