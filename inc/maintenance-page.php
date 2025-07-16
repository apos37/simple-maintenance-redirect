<?php 
/**
 * Maintenance mode page
 */


/**
 * Define Namespaces
 */
namespace Apos37\SimpleMaintenanceRedirect;
use Apos37\SimpleMaintenanceRedirect\Settings;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Instantiate the class
 */
new MaintenancePage();


/**
 * The class
 */
class MaintenancePage {

    /**
     * Store settings
     */
    private $SETTINGS;
    private $enabled;


    /**
	 * Constructor
	 */
	public function __construct() {

        // Are we enabled?
        $this->SETTINGS = new Settings();
        $this->enabled = $this->SETTINGS->enabled();
        if ( !$this->enabled ) {
            return;
        }

        // Maintenance mode page display
        add_action( 'template_redirect', [ $this, 'activate_maintenance_mode' ], 1000 );

        // Maintenance mode hide header and footer
        add_action( 'wp', [ $this, 'hide_header_footer_on_maintenance' ] );
        
	} // End __construct()

    
    /**
     * Enable coming soon page
     *
     * @return void
     */
    public function activate_maintenance_mode() {
        if ( $this->enabled == 'page' ) {
            $page_id = $this->SETTINGS->get_page_id();
            if ( $page_id && get_post_status( $page_id ) === 'publish' ) {
            
                // Only front-end, not logged in as admin, not login page, and not query string pw
                if ( $this->should_redirect_to_page( $page_id ) )  {

                    // Redirect
                    wp_redirect( esc_url( get_permalink( $page_id ) ) );
                    exit;
                }
            }
        } elseif ( $this->enabled == 'url' ) {
            $url = $this->SETTINGS->get_url();
            if ( $url && $this->should_redirect_to_page() ) {

                // Redirect
                wp_redirect( $url );
                exit;
            }
        }
    } // End activate_maintenance_mode()


    /**
     * Checks for redirect
     *
     * @return boolean
     */
    public function should_redirect_to_page( $page_id = null ) {
        // Get the current uri
        $request_uri = isset( $_SERVER[ 'REQUEST_URI' ] ) ? sanitize_url( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) : home_url( add_query_arg( [], '' ) );

        // The current page load must meet all these requirements to redirect
        $login_path = wp_parse_url( wp_login_url(), PHP_URL_PATH );
        $checks = [
            'front_end'            => !is_admin(),
            'logged_out'           => !is_user_logged_in(),
            'not_login_page'       => strpos( $request_uri, $login_path ) === false,
            'not_json_page'        => !preg_match( '#^/wp-json/#', $request_uri ),
            'not_rest_request'     => !( defined( 'REST_REQUEST' ) && REST_REQUEST )
        ];

        if ( !is_null( $page_id ) ) {
            $checks[ 'not_maintenance_page' ] = !is_page( $page_id );
        }

        // Allow developers to modify/add checks
        $checks = apply_filters( 'smredirect_redirect_rules', $checks, $page_id, $request_uri );

        // Check them all and return false if found
        if ( is_array( $checks ) ) {
            foreach ( $checks as $check ) {
                if ( !$check ) {
                    return false;
                }
            }
        }

        // Otherwise return true
        return true;
    } // End should_redirect_to_page()


    /**
     * Hide the header and footer on maintenance mode page
     *
     * @return void
     */
    public function hide_header_footer_on_maintenance() {
        if ( $this->enabled == 'page' ) {

            // Only on the maintenance page
            $page_id = $this->SETTINGS->get_page_id();
            if ( $page_id && is_page( $page_id ) ) {

                // Add custom body class
                add_filter( 'body_class', function( $classes ) {
                    $classes[] = 'maintenance-mode';
                    return $classes;
                } );

                // Enqueue inline CSS
                add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_maintenance_css' ] );
            }
        }
    } // End hide_header_footer_on_maintenance()


    /**
     * Maintenance mode CSS
     *
     * @return void
     */
    public function enqueue_maintenance_css() {
        if ( $this->enabled == 'page' ) {

            // Only on the maintenance page
            $page_id = $this->SETTINGS->get_page_id();
            if ( $page_id && is_page( $page_id ) ) {

                // The CSS we want to add
                $custom_css = "
                    .maintenance-mode header, 
                    .maintenance-mode footer {
                        display: none !important;
                    }
                    .site-container {
                        align-items: center;
                        display: flex;;
                        align-self: center;
                    }
                ";
            
                // Add it inline
                wp_add_inline_style( 'wp-block-library', $custom_css );
            }
        }
    } // End enqueue_maintenance_css()

}
