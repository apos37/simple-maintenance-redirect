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
    public $omit_pages_option = 'smredirect_omit_pages';
    public $status_code_option = 'smredirect_status_code';
    public $exempt_rest_option = 'smredirect_exempt_rest';


    /**
     * Settings page slug
     *
     * @var string
     */
    public $page_slug = 'smredirect-settings';


	/**
	 * Load on init
	 */
	public function init() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_notices', [ $this, 'settings_moved_notice' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_settings_page_assets' ] );
        add_action( 'wp_ajax_smredirect_dismiss_moved_notice', [ $this, 'ajax_dismiss_moved_notice' ] );
    } // End init()


    /**
     * Add the settings page under Settings
     *
     * @return void
     */
    public function add_settings_page() {
        add_options_page(
            __( 'Simple Maintenance Redirect', 'simple-maintenance-redirect' ),
            __( 'Simple Maintenance Redirect', 'simple-maintenance-redirect' ),
            'manage_options',
            $this->page_slug,
            [ $this, 'render_settings_page' ]
        );
    } // End add_settings_page()


    /**
     * ---------------------------------------------------------------
     * Register Settings, Sections, and Fields
     * ---------------------------------------------------------------
     */


    /**
     * Register all settings for the plugin's page
     *
     * @return void
     */
    public function register_settings() {
        // Enable
        register_setting( $this->page_slug, $this->enable_option, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_key',
            'default'           => '',
        ] );

        // Page
        register_setting( $this->page_slug, $this->page_id_option, [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitize_page_id' ],
            'default'           => '',
        ] );

        // URL
        register_setting( $this->page_slug, $this->url_option, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_url',
            'default'           => '',
        ] );

        // Omit pages
        register_setting( $this->page_slug, $this->omit_pages_option, [
            'type'              => 'array',
            'sanitize_callback' => [ $this, 'sanitize_omit_pages' ],
            'default'           => [],
        ] );

        // General section
        add_settings_section(
            'smredirect_general_section',
            __( 'Maintenance Mode', 'simple-maintenance-redirect' ),
            '__return_false',
            $this->page_slug
        );

        add_settings_field(
            $this->enable_option, 
            __( 'Enable Maintenance Mode Redirect', 'simple-maintenance-redirect' ),
            [ $this, 'enable_setting_field' ],
            $this->page_slug,
            'smredirect_general_section'
        );

        add_settings_field(
            $this->page_id_option, 
            __( 'Maintenance Mode Redirect Page', 'simple-maintenance-redirect' ),
            [ $this, 'page_id_setting_field' ],
            $this->page_slug,
            'smredirect_general_section'
        );

        add_settings_field(
            $this->url_option, 
            __( 'Maintenance Mode Redirect External URL', 'simple-maintenance-redirect' ),
            [ $this, 'url_setting_field' ], 
            $this->page_slug,
            'smredirect_general_section'
        );

        // Status code
        register_setting( $this->page_slug, $this->status_code_option, [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitize_status_code' ],
            'default'           => '302',
        ] );

        add_settings_field(
            $this->status_code_option,
            __( 'Redirect Status Code', 'simple-maintenance-redirect' ),
            [ $this, 'status_code_setting_field' ],
            $this->page_slug,
            'smredirect_general_section'
        );

        // Exempt REST API
        register_setting( $this->page_slug, $this->exempt_rest_option, [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitize_exempt_rest' ],
            'default'           => '1',
        ] );

        add_settings_field(
            $this->exempt_rest_option,
            __( 'Exempt REST API Requests', 'simple-maintenance-redirect' ),
            [ $this, 'exempt_rest_setting_field' ],
            $this->page_slug,
            'smredirect_general_section'
        );

        // Omit pages section
        add_settings_section(
            'smredirect_omit_section',
            __( 'Pages to Never Redirect', 'simple-maintenance-redirect' ),
            [ $this, 'render_omit_section_description' ],
            $this->page_slug
        );

        add_settings_field(
            $this->omit_pages_option,
            __( 'Omit Pages', 'simple-maintenance-redirect' ),
            [ $this, 'render_omit_pages_field' ],
            $this->page_slug,
            'smredirect_omit_section'
        );
    } // End register_settings()


    /**
     * ---------------------------------------------------------------
     * Field Renderers — Maintenance Mode
     * ---------------------------------------------------------------
     */


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
     * The status code field
     *
     * @return void
     */
    public function status_code_setting_field() {
        $value = $this->get_status_code();

        echo '<select id="' . esc_attr( $this->status_code_option ) . '" name="' . esc_attr( $this->status_code_option ) . '">';
            echo '<option value="302"' . wp_kses_post( selected( $value, 302, false ) ) . '>' . esc_html( __( '302 — Temporary Redirect', 'simple-maintenance-redirect' ) ) . '</option>';
            echo '<option value="307"' . wp_kses_post( selected( $value, 307, false ) ) . '>' . esc_html( __( '307 — Temporary Redirect (preserves request method)', 'simple-maintenance-redirect' ) ) . '</option>';
            echo '<option value="503"' . wp_kses_post( selected( $value, 503, false ) ) . '>' . esc_html( __( '503 — Service Unavailable (recommended for SEO during real maintenance)', 'simple-maintenance-redirect' ) ) . '</option>';
        echo '</select>';
        echo '<p class="description">' . esc_html( __( 'Use 503 if this is genuine maintenance downtime, so search engines don\'t index the redirect as permanent.', 'simple-maintenance-redirect' ) ) . '</p>';
    } // End status_code_setting_field()


    /**
     * Sanitizes the status code option
     *
     * @param mixed $value
     * @return string
     */
    public function sanitize_status_code( $value ) {
        $allowed = [ '302', '307', '503' ];
        return in_array( (string) $value, $allowed, true ) ? (string) $value : '302';
    } // End sanitize_status_code()


    /**
     * The exempt REST API field
     *
     * @return void
     */
    public function exempt_rest_setting_field() {
        $value = sanitize_key( get_option( $this->exempt_rest_option, '1' ) );

        echo '<label>';
            echo '<input type="checkbox" id="' . esc_attr( $this->exempt_rest_option ) . '" name="' . esc_attr( $this->exempt_rest_option ) . '" value="1"' . checked( $value, '1', false ) . '>';
            echo ' ' . esc_html( __( 'Allow REST API (wp-json) requests through, even when maintenance mode is active', 'simple-maintenance-redirect' ) );
        echo '</label>';
        echo '<p class="description">' . esc_html( __( 'Disable this only if you specifically want to block REST API access during maintenance. Leaving it checked prevents breaking things like the block editor, app connections, or other plugins that rely on REST.', 'simple-maintenance-redirect' ) ) . '</p>';
    } // End exempt_rest_setting_field()


    /**
     * Sanitizes the exempt REST API option
     *
     * @param mixed $value
     * @return string
     */
    public function sanitize_exempt_rest( $value ) {
        return ( $value === '1' ) ? '1' : '';
    } // End sanitize_exempt_rest()


    /**
     * ---------------------------------------------------------------
     * Field Renderers — Omit Pages
     * ---------------------------------------------------------------
     */


    /**
     * Sanitizes the submitted omit page IDs
     *
     * @param mixed $value
     * @return array
     */
    public function sanitize_omit_pages( $value ) {
        if ( !is_array( $value ) ) {
            return [];
        }

        return array_values( array_unique( array_map( 'absint', $value ) ) );
    } // End sanitize_omit_pages()


    /**
     * Omit pages section description
     *
     * @return void
     */
    public function render_omit_section_description() {
        echo '<p>' . esc_html( __( 'Select any pages that should never be redirected, even when maintenance mode is active. Search to filter the list.', 'simple-maintenance-redirect' ) ) . '</p>';
    } // End render_omit_section_description()


    /**
     * Render the omit pages checkbox list with a search filter
     *
     * @return void
     */
    public function render_omit_pages_field() {
        $selected = $this->get_omit_pages();
        $pages = get_pages( [ 'post_status' => [ 'publish', 'draft' ] ] );

        usort( $pages, function ( $a, $b ) use ( $selected ) {
            $a_selected = in_array( $a->ID, $selected, true );
            $b_selected = in_array( $b->ID, $selected, true );

            if ( $a_selected === $b_selected ) {
                return strcasecmp( $a->post_title, $b->post_title );
            }

            return $a_selected ? -1 : 1;
        } );

        echo '<div class="smredirect-omit-wrap">';
            echo '<input type="text" id="smredirect_omit_search" class="regular-text" placeholder="' . esc_attr( __( 'Search pages…', 'simple-maintenance-redirect' ) ) . '" autocomplete="off">';
            echo '<div id="smredirect_omit_list" class="smredirect-omit-list">';

                foreach ( $pages as $page ) {
                    $checked = in_array( $page->ID, $selected, true ) ? ' checked' : '';
                    $draft_label = ( get_post_status( $page->ID ) == 'draft' ) ? ' <em>— ' . esc_html( __( 'Draft', 'simple-maintenance-redirect' ) ) . '</em>' : '';

                    echo '<label class="smredirect-omit-item" data-title="' . esc_attr( strtolower( $page->post_title ) ) . '">';
                        echo '<input type="checkbox" name="' . esc_attr( $this->omit_pages_option ) . '[]" value="' . esc_attr( $page->ID ) . '"' . $checked . '>';
                        echo ' ' . esc_html( $page->post_title ) . wp_kses( $draft_label, [ 'em' => [] ] );
                    echo '</label>';
                }

            echo '</div>';
        echo '</div>';
    } // End render_omit_pages_field()


    /**
     * ---------------------------------------------------------------
     * Getters (used by other classes)
     * ---------------------------------------------------------------
     */


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
     * Get the omitted page IDs
     *
     * @return array
     */
    public function get_omit_pages() {
        $value = get_option( $this->omit_pages_option, [] );
        return is_array( $value ) ? array_map( 'absint', $value ) : [];
    } // End get_omit_pages()


    /**
     * Get the redirect status code
     *
     * @return int
     */
    public function get_status_code() : int {
        return (int) $this->sanitize_status_code( get_option( $this->status_code_option, '302' ) );
    } // End get_status_code()


    /**
     * Check if REST requests should be exempt from redirect
     *
     * @return bool
     */
    public function exempt_rest_requests() : bool {
        return sanitize_key( get_option( $this->exempt_rest_option, '1' ) ) === '1';
    } // End exempt_rest_requests()


    /**
     * ---------------------------------------------------------------
     * Admin Notice
     * ---------------------------------------------------------------
     */


    /**
     * Maintenance mode notice
     *
     * @return void
     */
    public function maintenance_mode_notice() {
        $screen = get_current_screen();
        if ( !$screen || $screen->id !== 'settings_page_' . $this->page_slug ) {
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
                __( 'Visitors are being redirected to your %s page.', 'simple-maintenance-redirect' ),
                $link
            );
        } else {
            $url = $this->get_url();
            $link = '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>';
            $message = sprintf(
                // translators: %s is the external redirect URL.
                __( 'Visitors are being redirected to %s.', 'simple-maintenance-redirect' ),
                $link
            );
        }

        echo '<div class="smredirect-status-banner">';
            echo '<span class="dashicons dashicons-warning"></span>';
            echo '<div class="smredirect-status-banner-text">';
                echo '<p class="smredirect-status-title">' . esc_html( __( 'Maintenance mode is currently active', 'simple-maintenance-redirect' ) ) . '</p>';
                echo '<p>' . wp_kses( $message, [ 'a' => [ 'href' => [], 'target' => [] ] ] ) . '</p>';
            echo '</div>';
        echo '</div>';
    } // End maintenance_mode_notice()


    /**
     * Notify admins the settings have moved, on Settings > General only
     *
     * @return void
     */
    public function settings_moved_notice() {
        $screen = get_current_screen();
        if ( !$screen || $screen->id !== 'options-general' ) {
            return;
        }

        if ( get_user_meta( get_current_user_id(), 'smredirect_dismissed_moved_notice', true ) ) {
            return;
        }

        $url = esc_url( admin_url( 'options-general.php?page=' . $this->page_slug ) );
        $message = sprintf(
            // translators: %s is a link to the new settings page.
            __( 'Simple Maintenance Redirect settings have moved to their own page to make room for additional options. %s', 'simple-maintenance-redirect' ),
            '<a href="' . $url . '">' . esc_html( __( 'Go to Settings', 'simple-maintenance-redirect' ) ) . '</a>'
        );

        echo '<div class="notice notice-info is-dismissible smredirect-moved-notice"><p>' . wp_kses( $message, [ 'a' => [ 'href' => [] ] ] ) . '</p></div>';
    } // End settings_moved_notice()


    /**
     * Handle AJAX dismissal of the settings-moved notice
     *
     * @return void
     */
    public function ajax_dismiss_moved_notice() {
        check_ajax_referer( 'smredirect_dismiss_notice', 'nonce' );

        update_user_meta( get_current_user_id(), 'smredirect_dismissed_moved_notice', 1 );
        wp_send_json_success();
    } // End ajax_dismiss_moved_notice()


    /**
     * ---------------------------------------------------------------
     * Settings Page Output
     * ---------------------------------------------------------------
     */


    /**
     * Render the full settings page
     *
     * @return void
     */
    public function render_settings_page() {
        echo '<div class="wrap smredirect-wrap">';
            echo '<h1>' . esc_html( __( 'Simple Maintenance Redirect', 'simple-maintenance-redirect' ) ) . ' <span class="smredirect-version">v' . esc_html( SMREDIRECT_VERSION ) . '</span></h1>';
            echo '<p class="smredirect-tagline">' . esc_html( __( 'A simple, focused way to redirect visitors during maintenance.', 'simple-maintenance-redirect' ) ) . '</p>';

            $this->maintenance_mode_notice();

            echo '<form method="post" action="options.php">';
                settings_fields( $this->page_slug );
                $this->render_sections();
                echo '<p class="submit smredirect-submit-row">';
                    submit_button( null, 'primary', 'submit', false );
                echo '</p>';
            echo '</form>';

            ( new RecommendedPlugins() )->render();
        echo '</div>';
    } // End render_settings_page()


    /**
     * Render settings sections as styled cards instead of default WP tables
     *
     * @return void
     */
    public function render_sections() {
        global $wp_settings_sections, $wp_settings_fields;

        if ( empty( $wp_settings_sections[ $this->page_slug ] ) ) {
            return;
        }

        foreach ( (array) $wp_settings_sections[ $this->page_slug ] as $section ) {
            echo '<div class="smredirect-section">';

                echo '<div class="smredirect-section-header">';
                    echo '<h2>' . esc_html( $section[ 'title' ] ) . '</h2>';

                    if ( !empty( $section[ 'callback' ] ) ) {
                        ob_start();
                        call_user_func( $section[ 'callback' ], $section );
                        $description = trim( ob_get_clean() );
                        if ( $description ) {
                            echo wp_kses_post( $description );
                        }
                    }
                echo '</div>';

                echo '<div class="smredirect-section-body">';

                    if ( !empty( $wp_settings_fields[ $this->page_slug ][ $section[ 'id' ] ] ) ) {
                        foreach ( (array) $wp_settings_fields[ $this->page_slug ][ $section[ 'id' ] ] as $field ) {
                            echo '<div class="smredirect-field-row">';
                                echo '<label class="smredirect-field-label" for="' . esc_attr( $field[ 'id' ] ) . '">' . esc_html( $field[ 'title' ] ) . '</label>';
                                call_user_func( $field[ 'callback' ], $field[ 'args' ] );
                            echo '</div>';
                        }
                    }

                echo '</div>';

            echo '</div>';
        }
    } // End render_sections()


    /**
     * ---------------------------------------------------------------
     * Enqueue Assets
     * ---------------------------------------------------------------
     */


    /**
     * Enqueue CSS/JS for the omit pages field, only on our settings screen
     *
     * @param string $hook_suffix
     * @return void
     */
    public function enqueue_settings_page_assets( $hook_suffix ) {
        if ( $hook_suffix === 'options-general.php' ) {
            wp_enqueue_script(
                'smredirect-dismiss-notice',
                SMREDIRECT_INCLUDES_URL . 'admin-dismiss-notice.js',
                [ 'jquery' ],
                SMREDIRECT_VERSION,
                true
            );

            wp_localize_script( 'smredirect-dismiss-notice', 'smredirectNotice', [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'smredirect_dismiss_notice' ),
            ] );

            return;
        }

        if ( $hook_suffix !== 'settings_page_' . $this->page_slug ) {
            return;
        }

        wp_enqueue_style(
            'smredirect-admin-settings-page',
            SMREDIRECT_INCLUDES_URL . 'admin-settings-page.css',
            [],
            SMREDIRECT_VERSION
        );

        wp_enqueue_style(
            'smredirect-admin-omit-pages',
            SMREDIRECT_INCLUDES_URL . 'admin-omit-pages.css',
            [],
            SMREDIRECT_VERSION
        );

        wp_enqueue_script(
            'smredirect-admin-omit-pages',
            SMREDIRECT_INCLUDES_URL . 'admin-omit-pages.js',
            [],
            SMREDIRECT_VERSION,
            true
        );

        wp_enqueue_style(
            'smredirect-admin-recommended-plugins',
            SMREDIRECT_INCLUDES_URL . 'admin-recommended-plugins.css',
            [],
            SMREDIRECT_VERSION
        );

        wp_enqueue_script( 'updates' );
        wp_enqueue_script(
            'smredirect-admin-recommended-plugins',
            SMREDIRECT_INCLUDES_URL . 'admin-recommended-plugins.js',
            [ 'jquery', 'updates' ],
            SMREDIRECT_VERSION,
            true
        );

        wp_localize_script( 'smredirect-admin-recommended-plugins', 'smredirectRecommended', [
            'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
            'nonce'          => wp_create_nonce( ( new RecommendedPlugins() )->nonce_action ),
            'installing'     => __( 'Installing…', 'simple-maintenance-redirect' ),
            'activating'     => __( 'Activating…', 'simple-maintenance-redirect' ),
            'activate'       => __( 'Activate', 'simple-maintenance-redirect' ),
            'active'         => __( 'Active', 'simple-maintenance-redirect' ),
            'installFailed'  => __( 'Install Failed', 'simple-maintenance-redirect' ),
            'activateFailed' => __( 'Activation failed.', 'simple-maintenance-redirect' ),
            'goToSettings'   => __( 'Go to settings', 'simple-maintenance-redirect' ),
        ] );
    } // End enqueue_settings_page_assets()

}