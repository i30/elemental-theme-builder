<?php
/**
 * Template to display site header
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php
		if ( ! current_theme_supports( 'title-tag' ) ) :
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<title><?php echo wp_get_document_title(); ?></title>
			<?php
			// phpcs:enable
		endif; ?>
		<?php wp_head(); ?>
	</head>
<body <?php body_class(); ?>>
<?php

// Theme authors may render something before.
do_action( 'etb_before_render_site_header', $header_template_id );

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
echo Elementor\Plugin::$instance->frontend->get_builder_content( $header_template_id, false );
// phpcs:enable

// Theme author may render something after.
do_action( 'etb_after_render_site_header', $header_template_id );
