<?php 
/**
 * Plugin page
 */


/**
 * Define Namespaces
 */
namespace Apos37\SimpleMaintenanceRedirect;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Instantiate the class
 */
new PluginPage();


/**
 * The class
 */
class PluginPage {

    /**
	 * Constructor
	 */
	public function __construct() {

        // Add a settings link to plugins list page
        add_filter( 'plugin_action_links_' . MMREDIRECT_BASENAME, [ $this, 'settings_link' ] );

        // Add links to the website and discord
        add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
        
	} // End __construct()


    /**
     * Add a settings link to plugins list page
     *
     * @param array $links
     * @return array
     */
    public function settings_link( $links ) {
        // Get the settings url
        $url = esc_url( admin_url( 'options-general.php' ) );

        // Add a link to it on our plugin
        array_unshift(
            $links,
            '<a href=' . $url . '>' . __( 'Settings', 'simple-maintenance-redirect' ) . '</a>'
        );

        // Return the links
        return $links;
    } // End settings_link()


    /**
     * Add links to the website and discord
     *
     * @param array $links
     * @return array
     */
    public function plugin_row_meta( $links, $file ) {
        // Only apply to this plugin
        if ( MMREDIRECT_BASENAME == $file ) {

            // Add the link
            $row_meta = [
                'discord' => '<a href="' . esc_url( MMREDIRECT_DISCORD_SUPPORT_URL ) . '" target="_blank">' . esc_html__( 'Discord Support', 'simple-maintenance-redirect' ) . '</a>'
            ];
            return array_merge( $links, $row_meta );
        }

        // Return the links
        return (array) $links;
    } // End plugin_row_meta()

}
