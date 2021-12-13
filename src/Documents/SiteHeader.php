<?php namespace ElementalThemeBuilder\Documents;

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Modules\Library\Documents\Library_Document;

/**
 * SiteHeader
 */
final class SiteHeader extends Library_Document {

	/**
	 * @var string
	 */
	const TYPE = 'siteheader';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		return [
			'has_elements' => true,
			'is_editable' => true,
			'edit_capability' => '',
			'show_in_finder' => true,
			'show_on_admin_bar' => true,
			'admin_tab_group' => 'library',
			'show_in_library' => true,
			'register_type' => true,
			'support_kit' => true,
			'support_wp_page_templates' => false,
		];
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return self::TYPE;
	}

	/**
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Header', 'elemental-theme-builder' );
	}

	/**
	 * @return string
	 */
	public function get_css_wrapper_selector() {
		return '.elemental-site-header';
	}

	/**
	 * Override container attributes
	 */
	public function get_container_attributes() {
		$id = $this->get_main_id();

		$settings = $this->get_frontend_settings();

		$attributes = [
			'data-elementor-type' => self::TYPE,
			'data-elementor-id' => $id,
			'class' => 'elementor elementor-' . $id . ' elemental-site-header',
		];

		return $attributes;
	}

	/**
	 * Override default wrapper.
	 */
	public function print_elements_with_wrapper( $data = null ) {
		if ( ! $data ) {
			$data = $this->get_elements_data();
		}

		do_action( 'before_print_elemental_site_header', $data );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<header <?php echo Utils::render_html_attributes( $this->get_container_attributes() ); ?>>
			<div class="elementor-section-wrap">
				<?php $this->print_elements( $data ); ?>
			</div>
		</header>
		<?php
		// phpcs:enable

		do_action( 'after_print_elemental_site_header', $data );
	}

	/**
	 * Register controls
	 */
	protected function register_controls() {
		$this->register_document_controls();

		$this->start_controls_section(
			'display_condition',
			[
				'label' => __( 'Display Condition', 'elemental-theme-builder' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_control(
			'meta_block_select',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'If multiple templates have the same display condition, the last updated one will be used.', 'elemental-theme-builder' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'show_on',
			[
				'label'       => __( 'Show On', 'elemental-theme-builder' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'none',
				'options' => [
					'none'     => __( 'None', 'elemental-theme-builder' ),
					'global'   => __( 'Entire Site', 'elemental-theme-builder' ),
					'blog'     => __( 'Blog Page', 'elemental-theme-builder' ),
					'front'    => __( 'Front Page', 'elemental-theme-builder' ),
					'archive'  => __( 'Archive Pages', 'elemental-theme-builder' ),
					'singular' => __( 'Singular Pages', 'elemental-theme-builder' ),
					'err404'   => __( 'Error 404 Page', 'elemental-theme-builder' ),
					'search'   => __( 'Search Result Page', 'elemental-theme-builder' ),
					'privacy'  => __( 'Privacy Policy Page', 'elemental-theme-builder' ),
					'wc_shop'  => __( 'WooCommerce Shop Page', 'elemental-theme-builder' ),
					'custom'   => __( 'Custom', 'elemental-theme-builder' ),
				],
			]
		);

		$this->add_control(
			'singular_pages',
			[
				'label' => __( 'Select Singular Type(s)', 'elemental-theme-builder' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->getSingularPagesOptions(),
				'condition' => [
					'show_on' => [ 'singular', 'custom' ],
				],
			]
		);

		$this->add_control(
			'archive_pages',
			[
				'label' => __( 'Select Archive Type(s)', 'elemental-theme-builder' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->getArchivePagesOptions(),
				'condition' => [
					'show_on' => [ 'archive', 'custom' ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return array
	 */
	private function getSingularPagesOptions() {
		global $wp_post_types;

		$options = [
			'post' => __( 'Post', 'elemental-theme-builder' ),
			'page' => __( 'Page', 'elemental-theme-builder' ),
			'attachment' => __( 'Attachment', 'elemental-theme-builder' ),
		];

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'elementor_library' !== $type ) {
				$options[ $type ] = $object->labels->singular_name;
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	private function getArchivePagesOptions() {
		global $wp_taxonomies, $wp_post_types;

		$options = [
			'author' => __( 'Author', 'elemental-theme-builder' ),
			'date' => __( 'Date', 'elemental-theme-builder' ),
			'post_tag' => __( 'Tag', 'elemental-theme-builder' ),
			'category' => __( 'Category ', 'elemental-theme-builder' ),
		];

		foreach ( $wp_taxonomies as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'product_shipping_class' !== $type ) {
				$options[ $type ] = $object->labels->name;
			}
		}

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'elementor_library' !== $type ) {
				$options[ $type ] = $object->labels->name;
			}
		}

		return $options;
	}
}
