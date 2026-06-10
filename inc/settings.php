<?php 
/**
 * Settings
 */


/**
 * Define Namespaces
 */
namespace PluginRx\SimpleMaintenanceRedirect;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Instantiate the class
 */
add_action( 'init', function() {
    (new Settings())->init();
} );


/**
 * The class
 */
class Settings {
    
    /**
     * Options
     *
     * @var string
     */
    public $enable_option = 'smredirect_enabled';
    public $page_id_option = 'smredirect_page_id';
    public $url_option = 'smredirect_url';


    /**
	 * Load on init
	 */
	public function init() {

        // General settings fields
        add_action( 'admin_init', [ $this, 'general_settings' ] );
        add_action( 'admin_notices', [ $this, 'maintenance_mode_notice' ] );
        
	} // End init()


	/**
     * Add a setting for the org name
     *
     * @return void
     */
    public function general_settings() {
        // Enable
        register_setting( 'general', $this->enable_option, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_key',
            'default'           => '',
        ] );
    
        add_settings_field(
            $this->enable_option, 
            __( 'Enable Maintenance Mode Redirect', 'simple-maintenance-redirect' ),
            [ $this, 'enable_setting_field' ],
            'general'
        );


        // Page
        register_setting( 'general', $this->page_id_option, [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitize_page_id' ],
            'default'           => '',
        ] );
    
        add_settings_field(
            $this->page_id_option, 
            __( 'Maintenance Mode Redirect Page', 'simple-maintenance-redirect' ),
            [ $this, 'page_id_setting_field' ],
            'general'
        );

        // URL
        register_setting( 'general', $this->url_option, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_url',
            'default'           => '',
        ] );
    
        add_settings_field(
            $this->url_option, 
            __( 'Maintenance Mode Redirect External URL', 'simple-maintenance-redirect' ),
            [ $this, 'url_setting_field' ], 
            'general'
        );
    } // End general_settings()


    /**
     * The enable field
     *
     * @return void
     */
    public function enable_setting_field() {
        $value = sanitize_key( get_option( $this->enable_option ) );
        
        echo '<select id="' . esc_attr( $this->enable_option ) . '" name="' . esc_attr( $this->enable_option ) . '">';
            echo '<option value=""' . wp_kses_post( selected( $value, '', false ) ) . '>' . esc_html( __( 'Disabled', 'simple-maintenance-redirect' ) ) . '</option>';
            echo '<option value="page"' . wp_kses_post( selected( $value, 'page', false ) ) . '>' . esc_html( __( 'Enable Redirect to Page', 'simple-maintenance-redirect' ) ) . '</option>';
            echo '<option value="url"' . wp_kses_post( selected( $value, 'url', false ) ) . '>' . esc_html( __( 'Enable Redirect to External URL', 'simple-maintenance-redirect' ) ) . '</option>';
        echo '</select>';
    } // End enable_setting_field()


    /**
     * The page id field
     *
     * @return void
     */
    public function page_id_setting_field() {
        $value = $this->sanitize_page_id( get_option( $this->page_id_option ) );
        $pages = get_pages( [ 'post_status' => [ 'publish', 'draft' ] ] );
        
        echo '<select id="' . esc_attr( $this->page_id_option ) . '" name="' . esc_attr( $this->page_id_option ) . '">';
            echo '<option value="0">' . esc_html( __( 'None', 'simple-maintenance-redirect' ) ) . '</option>';
            
            foreach ( $pages as $page ) {
                $selected = selected( $value, $page->ID, false );
                $incl_draft = ( get_post_status( $page->ID ) == 'draft' ) ? ' <em>— ' . __( 'Draft', 'simple-maintenance-redirect' ) . '</em>' : '';
                echo '<option value="' . esc_attr( $page->ID ) . '" ' . wp_kses_post( $selected ) . '>' . esc_html( $page->post_title ) . wp_kses( $incl_draft, [ 'em' => [] ] ) . '</option>';
            }
        echo '</select>';
    } // End page_id_setting_field()


    /**
     * Sanitizes the page ID option.
     *
     * @param mixed $value
     * @return string|int
     */
    public function sanitize_page_id( $value ) {
        return is_numeric( $value ) ? absint( $value ) : '';
    } // End sanitize_page_id()


    /**
     * The url field
     *
     * @return void
     */
    public function url_setting_field() {
        $value = sanitize_url( get_option( $this->url_option, '' ) );
        echo '<input type="url" id="' . esc_attr( $this->url_option ) . '" name="' . esc_attr( $this->url_option ) . '" value="' . esc_attr( $value ) . '">';
    } // End url_setting_field()


    /**
     * Check if we are redirecting
     *
     * @return string
     */
    public function enabled() {
        return sanitize_key( get_option( $this->enable_option ) );
    } // End enabled()

    
    /**
     * Get the page ID
     *
     * @return string
     */
    public function get_page_id() {
        return $this->sanitize_page_id( get_option( $this->page_id_option ) );
    } // End get_page_id()


    /**
     * Get the URL
     *
     * @return string
     */
    public function get_url() {
        return esc_url( get_option( $this->url_option ) );
    } // End get_url()


    /**
     * Maintenance mode notice
     *
     * @return void
     */
    public function maintenance_mode_notice() {
        $screen = get_current_screen();
        if ( !$screen || $screen->id !== 'options-general' ) {
            return;
        }
        $enabled = $this->enabled();
        if ( !$enabled ) {
            return;
        }

        if ( $enabled === 'page' ) {
            $page_id = $this->get_page_id();
            $page_title = get_the_title( $page_id );
            $page_url = esc_url( get_permalink( $page_id ) );
            $link = '<a href="' . $page_url . '" target="_blank">' . esc_html( $page_title ) . '</a>';
            $message = sprintf(
                // translators: %s is a link to the maintenance page.
                __( 'Maintenance mode is currently active. Visitors are being redirected to your %s page. You can disable it below.', 'simple-maintenance-redirect' ),
                $link
            );
        } else {
            $url = $this->get_url();
            $link = '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>';
            $message = sprintf(
                // translators: %s is the external redirect URL.
                __( 'Maintenance mode is currently active. Visitors are being redirected to %s. You can disable it below.', 'simple-maintenance-redirect' ),
                $link
            );
        }

        echo '<div class="notice notice-warning"><p><strong>' . wp_kses( $message, [ 'a' => [ 'href' => [], 'target' => [] ] ] ) . '</strong></p></div>';
    } // End maintenance_mode_notice()

}
