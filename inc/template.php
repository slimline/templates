<?php
/**
 * Template Loading Functions
 *
 * @package Slimline Templates
 * @subpackage Includes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly

/**
 * slimline_templates_author_template filter
 *
 * Adjusts the user template hierarchy by adding additional role-based templates.
 *
 * @since 0.1.0
 */
function slimline_templates_author_template() {

	$user = get_queried_object();

	/**
	 * Set up the user-specific templates
	 *
	 * Examples:
	 * author-admin.php
	 * author-1.php
	 */
	$templates = array(
		"author-{$user->data->user_nicename}.php",
		"author-{$user->ID}.php",
	);

	/**
	 * Add role-specific templates
	 *
	 * Examples:
	 * author-administrator.php
	 */
	if ( is_array( $user->roles ) ) {
		foreach ( $user->roles as $role ) {
			$templates[] = "author-{$role}.php";
		} // foreach ( $user->roles as $role )
	} // if ( is_array( $user->roles ) )

	/**
	 * Add generic templates
	 */
	$templates = array_merge(
		$templates,
		array(
			'author.php',
			'archive.php',
		)
	);

	if ( is_paged() ) {
		$templates[] = 'paged.php';
	} // if ( is_paged() )

	$templates[] = 'index.php';

	/**
	 * Allow for custom templates. If found, prepend to the templates array
	 */
	if ( $custom_template = get_user_meta( $user_id, '_wp_user_template', true ) ) {
		array_unshift( $templates, $custom_template );
	} // if ( $custom_template = get_user_meta( $user_id, '_wp_user_template', true ) )

	return locate_template( $templates );
}

/**
 * slimline_templates_single_template filter
 *
 * Standardizes single post template hierarchy and adds post-specific template support to all post
 * types.
 *
 * @since 0.1.0
 */
function slimline_templates_single_template() {
	global $post;

	/**
	 * Set up basic template array. This ensures that post-specific templates trump post-type templates.
	 */
	$templates = array(
		"{$post->post_type}-{$post->post_name}.php",
		"{$post->post_type}-{$post->ID}.php",
		"single-{$post->ID}.php",
	);

	/**
	 * Attachments get a few extra templates based on mime type. Note that the order below is reversed from
	 * the standard WordPress order so that more specific templates will trump less specific templates. We
	 * have also added both mime-type and attachment-mime-type templates so developers can use whichever
	 * naming scheme is more intuitive for them.
	 */
	if ( is_attachment() ) {

		$mime_type = explode( '/', $post->post_mime_type );

		/**
		 * Example:
		 * image-jpeg.php
		 * attachment-image-jpeg.php
		 * jpeg.php
		 * attachment-jpeg.php
		 * image.php
		 * attachment-image.php
		 */
		$templates = array_merge(
			$templates,
			array(
				"{$mime_type[0]}_{$mime_type[1]}.php",
				"attachment-{$mime_type[0]}_{$mime_type[1]}.php",
				"{$mime_type[1]}.php",
				"attachment-{$mime_type[1]}.php",
				"{$mime_type[0]}.php",
				"attachment-{$mime_type[0]}.php",
			)
		)
	} // if ( is_attachment() )

	/**
	 * Include general template types. Allows both a {$post_type}.php template (e.g., page.php) and a 
	 * single-{$post_type}.php template for compatibility and to allow developers to use whichever is
	 * more intuitive to them.
	 *
	 * Example:
	 * attachment.php
	 * single-attachment.php
	 * single.php
	 * index.php
	 */
	$templates = array_merge(
		$templates,
		array(
			"{$post->post_type}.php",
			"single-{$post->post_type}.php",
			'single.php',
			'index.php'
		)
	);

	/**
	 * Allow custom templates. If found, prepend to the templates array
	 */
	if ( $custom_template = get_post_meta( get_queried_object_id(), "_wp_{$post_type}_template", true ) ) {
		array_unshift( $templates, $custom_template );
	} // if ( $custom_template = get_post_meta( get_queried_object_id(), "_wp_{$post_type}_template", true ) )

	return locate_template( $templates );
}

/**
 * slimline_templates_taxonomy_template filter
 *
 * Standardizes taxonomy templates to use taxonomy-{$taxonomy}.php template names, allow for term-specific 
 * templates by ID and add custom templates if a term meta plugin is being used.
 *
 * @since 0.1.0
 */
function slimline_templates_taxonomy_template() {

	$term = get_queried_object();

	/**
	 * Set up basic templates. This enforces the standard taxonomy hierarchy but also allows templates that
	 * use term IDs instead of term slugs.
	 *
	 * Examples:
	 * taxonomy-category-uncategorized.php
	 * taxonomy-category-1.php
	 * taxonomy-category.php
	 */
	$templates = array(
		"taxonomy-{$term->taxonomy}-{$term->slug}.php",
		"taxonomy-{$term->taxonomy}-{$term->term_id}.php",
		"taxonomy-{$term->taxonomy}.php",
	);

	/**
	 * Category and tag templates for compatibility and because many developers are used to using them.
	 * 
	 * Examples:
	 * category-uncategorized.php
	 * category-1.php
	 * category.php
	 */
	if ( is_category() || is_tag() ) {

		$templates = array_merge(
			array(
				"{$taxonomy}-{$term->slug}.php",
				"{$taxonomy}-{$term->term_id}.php",
				"{$taxonomy}.php",
			),
			$templates
		)
	} // if ( is_category() || is_tag() )

	/**
	 * Standard, catch-all templates
	 */
	$templates[] = 'taxonomy.php';
	$templates[] = 'archive.php';

	if ( is_paged() ) {
		$templates[] = 'paged.php';
	} // if ( is_paged() )

	$templates[] = 'index.php';

	/**
	 * If the site is also using the term meta plugin, allow for custom term templates.  If found, prepend 
	 * to the templates array
	 */
	if ( function_exists( 'slimline_get_term_taxonomy_meta' ) && ( $custom_template = slimline_get_term_taxonomy_meta( $term->term_taxonomy_id, "_wp_{$term->taxonomy}_template", true ) ) ) {
		array_unshift( $templates, $custom_template );
	} // if ( function_exists( 'slimline_get_term_taxonomy_meta' ) && ( $custom_template = slimline_get_term_taxonomy_meta( $term->term_taxonomy_id, "_wp_{$term->taxonomy}_template", true ) ) )

	return locate_template( $templates );
}