<?php
/**
 * Recommended Plugins
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
    (new RecommendedPlugins())->init();
} );


/**
 * The class
 */
class RecommendedPlugins {

    /**
     * Nonce action
     *
     * @var string
     */
    public $nonce_action = 'smredirect_recommended_plugins_nonce';


	/**
	 * Load on init
	 */
	public function init() {
        add_action( 'wp_ajax_smredirect_activate_plugin', [ $this, 'ajax_activate_plugin' ] );
	} // End init()


    /**
     * Get the recommended plugins list
     *
     * @return array
     */
    public function get_plugins() : array {
        $plugins = [
            'clear-cache-everywhere' => [
                'name'          => 'Clear Cache Everywhere',
                'file'          => 'clear-cache-everywhere/clear-cache-everywhere.php',
                'url'           => 'https://wordpress.org/plugins/clear-cache-everywhere/',
                'description'   => __( 'One-click cache clearing so your maintenance page shows up immediately everywhere.', 'simple-maintenance-redirect' ),
                'logo'          => SMREDIRECT_INCLUDES_URL . 'logos/clear-cache-everywhere.png',
                'settings_page' => admin_url( 'tools.php?page=clear_cache_everywhere' ),
            ],
            'dev-debug-tools' => [
                'name'          => 'Developer Debug Tools',
                'file'          => 'dev-debug-tools/dev-debug-tools.php',
                'url'           => 'https://wordpress.org/plugins/dev-debug-tools/',
                'description'   => __( 'Handy debug log viewer and utilities while you work on the site during maintenance.', 'simple-maintenance-redirect' ),
                'logo'          => SMREDIRECT_INCLUDES_URL . 'logos/dev-debug-tools.png',
                'settings_page' => admin_url( 'admin.php?page=dev-debug-dashboard' ),
            ],
            'admin-help-docs' => [
                'name'          => 'Admin Help Docs',
                'file'          => 'admin-help-docs/admin-help-docs.php',
                'url'           => 'https://wordpress.org/plugins/admin-help-docs/',
                'description'   => __( 'Give your team quick reference docs right in wp-admin, including how maintenance mode works on this site.', 'simple-maintenance-redirect' ),
                'logo'          => SMREDIRECT_INCLUDES_URL . 'logos/admin-help-docs.png',
                'settings_page' => admin_url( 'admin.php?page=admin-help-docs&tab=settings' ),
            ],
            'broken-link-notifier' => [
                'name'          => 'Broken Link Notifier',
                'file'          => 'broken-link-notifier/broken-link-notifier.php',
                'url'           => 'https://wordpress.org/plugins/broken-link-notifier/',
                'description'   => __( 'Catches broken links so nothing slips through while you\'re making changes during maintenance.', 'simple-maintenance-redirect' ),
                'logo'          => SMREDIRECT_INCLUDES_URL . 'logos/broken-link-notifier.png',
                'settings_page' => admin_url( 'admin.php?page=broken-link-notifier&tab=settings' ),
            ],
            'wcag-admin-accessibility-tools' => [
                'name'          => 'WCAG Admin Accessibility Tools',
                'file'          => 'wcag-admin-accessibility-tools/wcag-admin-accessibility-tools.php',
                'url'           => 'https://wordpress.org/plugins/wcag-admin-accessibility-tools/',
                'description'   => __( 'Check your maintenance page and site for accessibility issues before pointing visitors to it.', 'simple-maintenance-redirect' ),
                'logo'          => SMREDIRECT_INCLUDES_URL . 'logos/wcag-admin-accessibility-tools.png',
                'settings_page' => admin_url( 'tools.php?page=wcag-admin-accessibility-tools' ),
            ],
        ];

        return apply_filters( 'smredirect_recommended_plugins', $plugins );
    } // End get_plugins()


    /**
     * Check if a plugin is installed
     *
     * @param string $plugin_file
     * @return bool
     */
    public function is_installed( string $plugin_file ) : bool {
        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return array_key_exists( $plugin_file, get_plugins() );
    } // End is_installed()


    /**
     * Check if a plugin is active
     *
     * @param string $plugin_file
     * @return bool
     */
    public function is_active( string $plugin_file ) : bool {
        return is_plugin_active( $plugin_file );
    } // End is_active()


    /**
     * Render the recommended plugins cards
     *
     * @return void
     */
    public function render() {
        $plugins = $this->get_plugins();

        echo '<br><br>';
        echo '<h2>' . esc_html( __( 'Recommended Plugins', 'simple-maintenance-redirect' ) ) . '</h2>';
        echo '<p>' . esc_html( __( 'A few plugins that pair well with Simple Maintenance Redirect, especially if you\'re just getting started or updating your site.', 'simple-maintenance-redirect' ) ) . '</p>';
        echo '<br>';

        echo '<div class="smredirect-recommended-list">';

            foreach ( $plugins as $slug => $plugin ) {
                $is_active = $this->is_active( $plugin[ 'file' ] );
                $is_installed = $this->is_installed( $plugin[ 'file' ] );

                echo '<div class="smredirect-recommended-card">';

                    echo '<div class="smredirect-recommended-card-header">';
                        if ( !empty( $plugin[ 'logo' ] ) ) {
                            echo '<img src="' . esc_url( $plugin[ 'logo' ] ) . '" alt="" class="logo">';
                        }
                        echo '<div class="smredirect-recommended-header-text">';
                            echo '<h3>' . esc_html( $plugin[ 'name' ] ) . '</h3>';
                            echo '<p class="smredirect-recommended-author">' . esc_html( __( 'By PluginRx', 'simple-maintenance-redirect' ) ) . '</p>';
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="smredirect-recommended-card-body">';
                        echo '<p>' . esc_html( $plugin[ 'description' ] ) . '</p>';
                    echo '</div>';

                    echo '<div class="smredirect-recommended-card-footer">';
                        if ( $is_active ) {
                            echo '<button type="button" class="button" disabled>' . esc_html( __( 'Active', 'simple-maintenance-redirect' ) ) . '</button>';

                            if ( !empty( $plugin[ 'settings_page' ] ) ) {
                                echo '<a href="' . esc_url( $plugin[ 'settings_page' ] ) . '" class="smredirect-recommended-external-link" title="' . esc_attr__( 'Go to settings', 'simple-maintenance-redirect' ) . '"><span class="dashicons dashicons-external"></span></a>';
                            }
                        } elseif ( $is_installed ) {
                            echo '<button type="button" class="button smredirect-activate-plugin" data-file="' . esc_attr( $plugin[ 'file' ] ) . '" data-settings-page="' . esc_attr( $plugin[ 'settings_page' ] ?? '' ) . '">' . esc_html( __( 'Activate', 'simple-maintenance-redirect' ) ) . '</button>';
                        } else {
                            echo '<button type="button" class="button smredirect-install-plugin" data-slug="' . esc_attr( $slug ) . '" data-installed-file="' . esc_attr( $plugin[ 'file' ] ) . '">' . esc_html( __( 'Install Now', 'simple-maintenance-redirect' ) ) . '</button>';
                        }
                    echo '</div>';

                echo '</div>';
            }

        echo '</div>';
    } // End render()


    /**
     * AJAX: activate a plugin by its file path
     *
     * @return void
     */
    public function ajax_activate_plugin() {
        check_ajax_referer( $this->nonce_action, 'nonce' );

        if ( !current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( __( 'You do not have permission to activate plugins.', 'simple-maintenance-redirect' ) );
        }

        $plugin_file = isset( $_POST[ 'plugin_file' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'plugin_file' ] ) ) : '';
        if ( !$plugin_file ) {
            wp_send_json_error( __( 'Missing plugin file.', 'simple-maintenance-redirect' ) );
        }

        $result = activate_plugin( $plugin_file );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success();
    } // End ajax_activate_plugin()

}