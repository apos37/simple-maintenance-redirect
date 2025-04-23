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
        add_action( 'admin_head', [ $this, 'change_admin_bar_color' ] );
        add_action( 'wp_head', [ $this, 'change_admin_bar_color' ] );
        
	} // End __construct()


    /**
     * Change the admin bar color when maintenance mode is active.
     */
    public function change_admin_bar_color() {
        // Allow color overrides via filter
        $defaults = [
            'color_1' => $this->color_1,
            'color_2' => $this->color_2
        ];
        $colors = apply_filters( 'smredirect_admin_bar_colors', $defaults );

        // Add the style
        echo '<style>
            #wpadminbar { 
            background: repeating-linear-gradient(
                -45deg, 
                ' . esc_html( $colors[ 'color_1' ] ) . ', 
                ' . esc_html( $colors[ 'color_1' ] ) . ' 10px, 
                ' . esc_html( $colors[ 'color_2' ] ) . ' 10px, 
                ' . esc_html( $colors[ 'color_2' ] ) . ' 20px
            ) !important; }
        </style>';
    } // End change_admin_bar_color()

}
