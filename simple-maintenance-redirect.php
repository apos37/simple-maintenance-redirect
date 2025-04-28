<?php
/**
 * Plugin Name:         Simple Maintenance Redirect
 * Plugin URI:          https://github.com/apos37/simple-maintenance-redirect
 * Description:         Redirect users to a specified page or external URL while in maintanence mode
 * Version:             1.0.1
 * Requires at least:   5.9
 * Tested up to:        6.8
 * Requires PHP:        7.4
 * Author:              PluginRx
 * Author URI:          https://pluginrx.com/
 * Support URI:         https://discord.gg/3HnzNEJVnR
 * Text Domain:         simple-maintenance-redirect
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Created on:          March 31, 2025
 */


/**
 * Define Namespace
 */
namespace Apos37\SimpleMaintenanceRedirect;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Defines
 */
$plugin_data = get_file_data( __FILE__, [
    'name'         => 'Plugin Name',
    'version'      => 'Version',
    'requires_php' => 'Requires PHP',
    'textdomain'   => 'Text Domain',
    'support_uri'  => 'Support URI',
] );

// Versions
define( 'SMREDIRECT_VERSION', $plugin_data[ 'version' ] );
define( 'SMREDIRECT_MIN_PHP_VERSION', $plugin_data[ 'requires_php' ] );

// Names
define( 'SMREDIRECT_NAME', $plugin_data[ 'name' ] );
define( 'SMREDIRECT_BASENAME', plugin_basename( __FILE__ ) );
define( 'SMREDIRECT_TEXTDOMAIN', $plugin_data[ 'textdomain' ] );
define( 'SMREDIRECT_DISCORD_SUPPORT_URL', $plugin_data[ 'support_uri' ] );

// Paths
define( 'SMREDIRECT_INCLUDES_ABSPATH', plugin_dir_path( __FILE__ ) . 'inc/' );


/**
 * Prevent loading the plugin if PHP version is not minimum
 */
if ( version_compare( PHP_VERSION, SMREDIRECT_MIN_PHP_VERSION, '<=' ) ) {
    add_action( 'admin_init', static function() {
        deactivate_plugins( SMREDIRECT_BASENAME );
    } );
    add_action( 'admin_notices', static function() {
        /* translators: 1: Plugin name, 2: Minimum PHP version */
        $message = sprintf( __( '"%1$s" requires PHP %2$s or newer.', 'simple-maintenance-redirect' ),
            SMREDIRECT_NAME,
            SMREDIRECT_MIN_PHP_VERSION
        );
        echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
    } );
    return;
}


/**
 * Includes
 */
require_once SMREDIRECT_INCLUDES_ABSPATH . 'settings.php';
require_once SMREDIRECT_INCLUDES_ABSPATH . 'maintenance-page.php';
require_once SMREDIRECT_INCLUDES_ABSPATH . 'plugin-page.php';
require_once SMREDIRECT_INCLUDES_ABSPATH . 'admin-bar.php';