<?php namespace ElementalThemeBuilder\Documents;

use Elementor\Controls_Manager;
use Elementor\Plugin as Elementor;
use Elementor\Core\Documents_Manager;

/**
 * Manager
 */
final class Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'get_header', array( $this, 'render_site_header' ), 11 );
		add_action( 'get_footer', array( $this, 'render_site_footer' ), 11 );
		add_action( 'template_include', array( $this, 'include_template' ), 11 );
		add_action( 'elementor/documents/register', array( $this, 'register_template_types' ) );
		add_action( 'elementor/editor/after_save', array( $this, 'update_template_location' ), 11, 2 );
	}

	/**
	 * @internal Used as a callback
	 */
	public function register_template_types( Documents_Manager $manager ) {
		$manager->register_document_type( SiteHeader::TYPE, SiteHeader::class );
		$manager->register_document_type( SiteFooter::TYPE, SiteFooter::class );
	}

	/**
	 * @internal Used as a callback
	 */
	public function include_template( $template ) {
		if ( is_singular() ) {
			$document = Elementor::$instance->documents->get_doc_for_frontend( get_the_ID() );
			if ( $document ) {
				if ( $document instanceof SiteHeader || $document instanceof SiteFooter ) {
					return ELEMENTAL_THEME_BUILDER_DIR . 'src/Templates/BlankPage.php';
				}
			}
		}

		return $template;
	}

	/**
	 * @internal Used as a callback
	 */
	public function render_site_header( $name ) {
		$header_template_id = $this->has_assigned_template( SiteHeader::TYPE );

		if ( $header_template_id ) {
			require ELEMENTAL_THEME_BUILDER_DIR . 'src/Templates/SiteHeader.php';
			$templates = array( 'header.php' );
			if ( $name ) {
				$templates[] = "header-{$name}.php";
			}
			remove_all_actions( 'wp_head' );
			ob_start();
			locate_template( $templates, true );
			ob_get_clean();
		}
	}

	/**
	 * @internal Used as a callback
	 */
	public function render_site_footer( $name ) {
		$footer_template_id = $this->has_assigned_template( SiteFooter::TYPE );

		if ( $footer_template_id ) {
			require ELEMENTAL_THEME_BUILDER_DIR . 'src/Templates/SiteFooter.php';
			$templates = array( 'footer.php' );
			if ( $name ) {
				$templates[] = "footer-{$name}.php";
			}
			remove_all_actions( 'wp_footer' );
			ob_start();
			locate_template( $templates, true );
			ob_get_clean();
		}
	}

	/**
	 * @return int
	 */
	private function get_current_page_id() {
		global $wp_query;

		if ( ! $wp_query->is_main_query() ) {
			return 0;
		}

		if ( $wp_query->is_home() && ! $wp_query->is_front_page() ) {
			return (int) get_option( 'page_for_posts' );
		} elseif ( ! $wp_query->is_home() && $wp_query->is_front_page() ) {
			return (int) get_option( 'page_on_front' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			return wc_get_current_page_id( 'shop' );
		} elseif ( $wp_query->is_privacy_policy() ) {
			return (int) get_option( 'wp_page_for_privacy_policy' );
		} elseif ( ! empty( $wp_query->post->ID ) ) {
			return (int) $wp_query->post->ID;
		} else {
			return 0;
		}
	}

	/**
	 * Check if queried location has an assigned template
	 *
	 * @param string $type Template type.
	 *
	 * @return int|bool Template id or false if there's no assigned template.
	 */
	private function has_assigned_template( $type ) {
		global $wp_query;

		if ( ! $wp_query->is_main_query() ) {
			return false;
		}

		$template = false;

		if ( $wp_query->is_front_page() && $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'index' );
		} elseif ( $wp_query->is_front_page() && ! $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'front' );
		} elseif ( ! $wp_query->is_front_page() && $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'blog' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$template = $this->get_assigned_template( $type, 'wc_shop' );
		} elseif ( $wp_query->is_search() ) {
			$template = $this->get_assigned_template( $type, 'search' );
		} elseif ( $wp_query->is_404() ) {
			$template = $this->get_assigned_template( $type, 'err404' );
		} elseif ( $wp_query->is_privacy_policy() ) {
			$template = $this->get_assigned_template( $type, 'privacy' );
		} elseif ( $wp_query->is_singular() ) {
			if ( ! empty( $wp_query->post->post_type ) ) {
				$template = $this->get_assigned_template( $type, 'singular_' . $wp_query->post->post_type );
			}
			if ( ! $template ) {
				$template = $this->get_assigned_template( $type, 'singular' );
			}
		} elseif ( $wp_query->is_archive() ) {
			if ( $wp_query->is_author() ) {
				$template = $this->get_assigned_template( $type, 'archive_author' );
			} elseif ( $wp_query->is_date() ) {
				$template = $this->get_assigned_template( $type, 'archive_date' );
			} elseif ( $wp_query->is_tax() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->queried_object->taxonomy );
			} elseif ( $wp_query->is_post_type_archive() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->posts[0]->post_type );
			}
			if ( ! $template ) {
				$template = $this->get_assigned_template( $type, 'archive' );
			}
		}

		$_tpl = get_post_meta( $this->get_current_page_id(), 'elemental_theme_builder_template_' . $type, true );

		if ( $_tpl && 'inherit' !== $_tpl ) {
			if ( 'default' === $_tpl ) {
				return false;
			} else {
				$template = get_page_by_path( $_tpl, OBJECT, 'elementor_library' );
			}
		}

		if ( ! $template ) {
			$template = $this->get_assigned_template( $type, 'global' );
		}

		return $template ? $template->ID : false;
	}

	/**
	 * Get assigned template by location and page type
	 */
	private function get_assigned_template( $template_type, $page_type ) {
		global $wp_query;

		$template = get_option( get_option( 'stylesheet' ) . '_mod_etb_tpl_' . $template_type . '_' . $page_type );

		if ( $template ) {
			return get_page_by_path( $template, OBJECT, 'elementor_library' );
		}

		return false;
	}

	/**
	 * @internal Used as a callback
	 */
	public function update_template_location( $post_id, $data ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		global $wpdb;

		$template   = get_post( $post_id );
		$type       = get_post_meta( $post_id, '_elementor_template_type', true );
		$settings   = get_post_meta( $post_id, '_elementor_page_settings', true );
		$key_prefix = get_option( 'stylesheet' ) . '_mod_etb_tpl_' . $type . '_';

		if ( in_array( $type, array( SiteHeader::TYPE, SiteFooter::TYPE ), true ) ) {
			$wpdb->query( sprintf( "DELETE FROM $wpdb->options WHERE option_name LIKE '%s' AND option_value = '%s'", $wpdb->esc_like( $key_prefix ) . '%', $template->post_name ) );
			switch ( $settings['show_on'] ) {
				case 'global':
					add_option( $key_prefix . 'global', $template->post_name );
					break;
				case 'blog':
				case 'index':
					add_option( $key_prefix . 'blog', $template->post_name );
					break;
				case 'front':
					add_option( $key_prefix . 'front', $template->post_name );
					break;
				case 'search':
					add_option( $key_prefix . 'search', $template->post_name );
					break;
				case 'err404':
					add_option( $key_prefix . 'err404', $template->post_name );
					break;
				case 'wc_shop':
					add_option( $key_prefix . 'wc_shop', $template->post_name );
					break;
				case 'privacy':
					add_option( $key_prefix . 'privacy', $template->post_name );
					break;
				case 'singular':
					if ( ! empty( $settings['singular_pages'] ) ) {
						foreach ( $settings['singular_pages'] as $page_type ) {
							add_option( $key_prefix . 'singular_' . $page_type, $template->post_name );
						}
					} else {
						add_option( $key_prefix . 'singular', $template->post_name );
					}
					break;
				case 'archive':
					if ( ! empty( $settings['archive_pages'] ) ) {
						foreach ( $settings['archive_pages'] as $page_type ) {
							add_option( $key_prefix . 'archive_' . $page_type, $template->post_name );
						}
					} else {
						add_option( $key_prefix . 'archive', $template->post_name );
					}
					break;
				case 'custom':
					if ( ! empty( $settings['singular_pages'] ) ) {
						foreach ( $settings['singular_pages'] as $page_type ) {
							add_option( $key_prefix . 'singular_' . $page_type, $template->post_name );
						}
					}
					if ( ! empty( $settings['archive_pages'] ) ) {
						foreach ( $settings['archive_pages'] as $page_type ) {
							add_option( $key_prefix . 'archive_' . $page_type, $template->post_name );
						}
					}
					break;
				default:
					break;
			}
		}
	}
}
