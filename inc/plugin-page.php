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
        add_filter( 'plugin_action_links_' . SMREDIRECT_BASENAME, [ $this, 'settings_link' ] );

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
        if ( SMREDIRECT_BASENAME == $file ) {

            $guide_url = SMREDIRECT_GUIDE_URL;
            $docs_url = SMREDIRECT_DOCS_URL;
            $support_url = SMREDIRECT_SUPPORT_URL;
            $plugin_name = SMREDIRECT_NAME;

            $our_links = [
                'guide' => [
                    // translators: Link label for the plugin's user-facing guide.
                    'label' => __( 'How-To Guide', 'simple-maintenance-redirect' ),
                    'url'   => $guide_url
                ],
                'docs' => [
                    // translators: Link label for the plugin's developer documentation.
                    'label' => __( 'Developer Docs', 'simple-maintenance-redirect' ),
                    'url'   => $docs_url
                ],
                'support' => [
                    // translators: Link label for the plugin's support page.
                    'label' => __( 'Support', 'simple-maintenance-redirect' ),
                    'url'   => $support_url
                ],
            ];

            $row_meta = [];
            foreach ( $our_links as $key => $link ) {
                // translators: %1$s is the link label, %2$s is the plugin name.
                $aria_label = sprintf( __( '%1$s for %2$s', 'simple-maintenance-redirect' ), $link[ 'label' ], $plugin_name );
                $row_meta[ $key ] = '<a href="' . esc_url( $link[ 'url' ] ) . '" target="_blank" aria-label="' . esc_attr( $aria_label ) . '">' . esc_html( $link[ 'label' ] ) . '</a>';
            }

            // Add the links
            return array_merge( $links, $row_meta );
        }

        // Return the links
        return (array) $links;
    } // End plugin_row_meta()

}
