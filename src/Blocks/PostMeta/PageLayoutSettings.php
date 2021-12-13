<?php namespace ElementalThemeBuilder\Blocks\PostMeta;

use Exception;

/**
 * PageLayoutSettings
 */
final class PageLayoutSettings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ), 10, 0 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ), 10, 0 );
	}

	/**
	 * Register with editor
	 *
	 * @internal Used as a callback.
	 */
	public function register() {
		register_post_meta(
			'',
			'elemental_theme_builder_template_siteheader',
			array(
				'type'              => 'string',
				'single'            => 1,
				'description'       => __( 'Select a header template to display on the frontend.', 'elemental-theme-builder' ),
				'show_in_rest'      => 1,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			'',
			'elemental_theme_builder_template_sitefooter',
			array(
				'type'              => 'string',
				'single'            => 1,
				'description'       => __( 'Select a footer template to display on the frontend.', 'elemental-theme-builder' ),
				'show_in_rest'      => 1,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'etb-meta-sidebar', ELEMENTAL_THEME_BUILDER_URI . 'assets/js/meta-sidebar.min.js', array( 'wp-blocks', 'wp-element', 'wp-components' ), ELEMENTAL_THEME_BUILDER_VER, true );

		wp_localize_script(
			'etb-meta-sidebar',
			'elementorSiteBuilderData',
			array(
				'headerTemplates' => $this->list_header_templates(),
				'footerTemplates' => $this->list_footer_templates(),
			)
		);
	}

	/**
	 * List all available header templates
	 *
	 * @return array
	 */
	private function list_header_templates() {
		$options = array(
			array(
				'label' => __( 'Inherit', 'elemental-theme-builder' ),
				'value' => 'inherit',
			),
			array(
				'label' => __( 'Theme Default', 'elemental-theme-builder' ),
				'value' => 'default',
			),
		);

		$headers = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'post_status'            => 'publish',
				'meta_key'               => '_elementor_template_type',
				'meta_value'             => 'siteheader',
				'ignore_sticky_posts'    => true,
				'nopaging'               => true,
				'no_found_rows'          => true,
				'posts_per_page'         => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( $headers ) {
			foreach ( $headers as $header ) {
				$options[] = array(
					'label' => $header->post_title,
					'value' => $header->post_name,
				);
			}
		}

		return $options;
	}

	/**
	 * List all available footer templates
	 *
	 * @return array
	 */
	private function list_footer_templates() {
		$options = array(
			array(
				'label' => __( 'Inherit', 'elemental-theme-builder' ),
				'value' => 'inherit',
			),
			array(
				'label' => __( 'Theme Default', 'elemental-theme-builder' ),
				'value' => 'default',
			),
		);

		$footers = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'post_status'            => 'publish',
				'meta_key'               => '_elementor_template_type',
				'meta_value'             => 'sitefooter',
				'ignore_sticky_posts'    => true,
				'nopaging'               => true,
				'no_found_rows'          => true,
				'posts_per_page'         => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( $footers ) {
			foreach ( $footers as $footer ) {
				$options[] = array(
					'label' => $footer->post_title,
					'value' => $footer->post_name,
				);
			}
		}

		return $options;
	}
}
