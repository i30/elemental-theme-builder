<?php
/**
 * Plugin Name: Elemental Theme Builder
 * Plugin URI: https://wordpress.org/plugins/elemental-theme-builder
 * Description: An intuitive theme builder for Elementor Page Builder.
 * Author: WP Clevel
 * Author URI: https://wpclevel.com
 * Version: 1.0.0
 * Text Domain: elemental-theme-builder
 * Requires PHP: 7.2
 * Requires at least: 5.6
 *
 * @package ElementalThemeBuilder
 */

// Useful constants.
define( 'ELEMENTAL_THEME_BUILDER_VER', '1.0.0' );
define( 'ELEMENTAL_THEME_BUILDER_DIR', __DIR__ . '/' );
define( 'ELEMENTAL_THEME_BUILDER_URI', plugins_url( '/', __FILE__ ) );

/**
 * Register autoloading for PHP classes.
 *
 * @internal Callback
 * @param string $class Loading classname.
 * @throws Exception Class not found.
 */
function wpclevel_etb_register_autoloading( $class ) {
	if ( 0 !== strpos( $class, 'ElementalThemeBuilder\\' ) ) {
		return; // Not in our namespace.
	}

	$path = str_replace( 'ElementalThemeBuilder', __DIR__ . '/src', $class );
	$file = str_replace( '\\', '/', $path ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	} else {
		/* translators: %s: loading class. */
		throw new Exception( sprintf( __( 'Autoloading failed. Class "%s" not found.', 'elemental-theme-builder' ), $class ) );
	}
}
spl_autoload_register( 'wpclevel_etb_register_autoloading', true, false );

/**
 * Do activation
 *
 * @internal Callback.
 * @see  https://developer.wordpress.org/reference/functions/register_activation_hook/
 * @param bool $network Is being activated on multisite or not.
 * @throws Exception Activation error.
 */
function wpclevel_etb_do_activation( $network ) {
	try {
		if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
			throw new Exception( __( 'Elemental Theme Builder requires PHP version 7.2 at least!', 'elemental-theme-builder' ) );
		}
		if ( version_compare( $GLOBALS['wp_version'], '5.6', '<' ) ) {
			throw new Exception( __( 'Elemental Theme Builder requires WordPress version 5.6 at least!', 'elemental-theme-builder' ) );
		}
		if ( ! defined( 'WP_CONTENT_DIR' ) || ! is_writable( WP_CONTENT_DIR ) ) {
			throw new Exception( __( 'WordPress content directory is inaccessible.', 'elemental-theme-builder' ) );
		}
		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, '3.3.0', '<' ) ) {
			throw new Exception( __( 'Elemental Theme Builder requires Elementor version 3.3.0 at least.', 'elemental-theme-builder' ) );
		}
	} catch ( Exception $e ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
			status_header( 500 );
			exit(
				wp_json_encode(
					array(
						'success' => false,
						'name'    => __( 'Plugin Activation Error', 'elemental-theme-builder' ),
						'message' => $e->getMessage(),
					)
				)
			);
		} else {
			exit( esc_html( $e->getMessage() ) );
		}
	}
}
add_action( 'activate_elemental-theme-builder/elemental-theme-builder.php', 'wpclevel_etb_do_activation' );

/**
 * Do installation
 *
 * @internal Callback.
 *
 * @see https://developer.wordpress.org/reference/hooks/plugins_loaded/
 */
function wpclevel_etb_do_installation() {
	// Make sure translation is available.
	load_plugin_textdomain( 'elemental-theme-builder', false, 'elemental-theme-builder/languages' );

	// Initialize modules.
	new ElementalThemeBuilder\Documents\Manager();
	new ElementalThemeBuilder\Blocks\PostMeta\PageLayoutSettings();
}
add_action( 'plugins_loaded', 'wpclevel_etb_do_installation' );
