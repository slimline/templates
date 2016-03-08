<?php

/**
 * slimline_templates_add_post_template_meta_box function
 *
 * @since 0.1.0
 */
function slimline_templates_add_post_template_meta_box( $post_type ) {
	global $slimline_wp_theme;

	if ( 'page' !== $post_type && 0 !== count( $slimline_wp_theme->get_post_templates( $post_type ) ) ) {
		add_meta_box( 'slimline-templates-div', __( 'Templates', 'slimline-templates' ), 'slimline_templates_metabox', $post_type, 'side', 'core', array( 'post_type' => $post_type ) );
	}
}

/**
 * slimline_save_custom_templates function
 *
 * 
 *
 * @since 0.1.0
 */
function slimline_save_custom_templates( $post_id ) {
	global $slimline_wp_theme;

	if ( isset( $_REQUEST[ 'slimline_template' ] ) && 'default' !== $_REQUEST[ 'slimline_template' ] ) {

		$post_type = get_post_type( $post_id );
		$templates = $slimline_wp_theme->get_post_templates( $post_type );
		$post_template = esc_attr( $_REQUEST[ 'slimline_template' ] );

		if ( isset( $templates[ $post_template ] ) ) {
			update_post_meta( $post_id, "_wp_{$post_type}_template", $post_template );
		} else {
			return new WP_Error( 'invalid_page_template', __( 'The selected post template does not exist.', 'slimline-templates' ) );
		}


	} // if ( isset( $_REQUEST[ 'slimline_template' ] ) )
}

/**
 * slimline_templates_metabox callback
 *
 * Outputs the content for the Slimline Templates meta box.
 *
 * @since 0.1.0
 */
function slimline_templates_metabox( $args = '' ) {
	global $slimline_wp_theme;

	extract(
		wp_parse_args(
			$args,
			array(
				'post_id'   => ( isset( $_REQUEST[ 'post' ] ) ? absint( $_REQUEST[ 'post' ] ) : 0 ),
				'post_type' => 'page',
		)
	);

	if ( ( $templates = $slimline_wp_theme->get_post_templates( $post_type ) ) ) {

		// get the current post template if one has been selected. We will use this in the foreach() loop below
		$current_template = get_post_meta( $post_id, "_wp_{$post_type}_template", true );

		echo "<select name='slimline_template' id='slimline_template'>";
		echo "<option value='default'>", __( 'Default', 'slimline-templates' ), "</option>";

		foreach ( $templates as $template_name => $template_file ) {
			echo "<option ", selected( $template_file, $current_template, false ), " value='{$template_file}'>{$template_name}</option>";
		} // foreach ( $templates as $template_name => $template_file )

		echo "</select>";

	} // if ( ( $templates = $slimline_wp_theme->get_post_templates( $post_type ) ) )

}