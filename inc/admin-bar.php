<?php 
/**
 * Admin Bar
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
new AdminBar();


/**
 * The class
 */
class AdminBar {
    
    /**
     * Options
     *
     * @var string
     */
    public $color_1 = '#870927'; // Crimson Burgundy
    public $color_2 = '#8B0000'; // Dark Red


    /**
	 * Constructor
	 */
	public function __construct() {

        // Are we enabled?
        if ( !(new Settings())->enabled() ) {
            return;
        }

        // Change admin bar color when maintenance mode is active
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_bar_styles' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_admin_bar_styles' ] );
        
	} // End __construct()


    /**
     * Change the admin bar color when maintenance mode is active.
     */
    public function enqueue_admin_bar_styles() {
        if ( !is_admin_bar_showing() ) {
            return;
        }

        // Register a dummy handle if needed (or use an existing style handle).
        $handle = 'smredirect-admin-bar'; 

        // Register a minimal stylesheet.
        wp_register_style( $handle, false );
        wp_enqueue_style( $handle );

        // Get colors (allowing filter override)
        $defaults = [
            'color_1' => $this->color_1,
            'color_2' => $this->color_2,
        ];
        $colors = apply_filters( 'smredirect_admin_bar_colors', $defaults );

        // Build the CSS
        $css = '
            #wpadminbar {
                background: repeating-linear-gradient(
                    -45deg,
                    ' . esc_html( $colors[ 'color_1' ] ) . ',
                    ' . esc_html( $colors[ 'color_1' ] ) . ' 10px,
                    ' . esc_html( $colors[ 'color_2' ] ) . ' 10px,
                    ' . esc_html( $colors[ 'color_2' ] ) . ' 20px
                ) !important;
            }
        ';

        // Add the inline CSS
        wp_add_inline_style( $handle, $css );
    } // End enqueue_admin_bar_styles()

}
