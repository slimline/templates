<?php
/**
 * Meta Boxes
 *
 * @package Slimline
 * @subpackage
 */

/**
 * slimline_templates_single_posts_add_meta_box function
 *
 * @global $post
 * @uses add_meta_box()
 * @since 0.1.0
 */
function slimline_templates_single_posts_add_meta_box() {
	global $post;

	$post_type = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );
	$singular_name = ( isset( $post_type_object->labels->singular_name ) ? $post_type_object->labels->singular_name : $post_type_object->labels->name );

	$theme = wp_get_theme();

	$post_templates = $theme->cache_get( "{$post_type}_templates" );

	if ( ! is_array( $post_templates ) ) {

		$files = (array) $theme->get_files( 'php', 1 );

		foreach ( $files as $file => $full_path ) {
			if ( preg_match( "|{$singular_name} Template Name:(.*)$|mi", file_get_contents( $full_path ), $header ) ) {
				$post_templates[ $file ] = _cleanup_header_comment( $header[ 1 ] );
			}
		}

		$theme->cache_add( "{$post_type}_templates", $post_templates );
	}
	
	add_meta_box( sprintf( '%1$s_template', sanitize_key( $post_type ) ), sprintf( _x( '%1$s Template', 'meta box title', 'slimline' ), $singular_name ), 'slimline_templates_single_posts_meta_box', $post_type, 'side', 'default', $callback_args );
}