<?php
/**
 * Plugin Name: Slimline Term and User Templates
 * Plugin URI: http://www.michaeldozark.com/slimline/templates/
 * Description: Adds custom templates to terms and users.
 * Author: Michael Dozark
 * Author URI: http://www.michaeldozark.com/
 * Version: 0.2.0
 * License: GPL2
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2.0, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package    Slimline\Templates
 * @version    0.2.0
 * @author     Michael Dozark <michael@michaeldozark.com>
 * @copyright  Copyright (c) 2017, Michael Dozark
 * @link       http://www.michaeldozark.com/wordpress/slimline/templates/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if accessed directly
}

/**
 * Call initialization function.
 *
 * @link https://developer.wordpress.org/reference/hooks/plugins_loaded/
 *       Documentation of `plugins_loaded` hook
 */
add_action( 'plugins_loaded', 'slimline_templates' );

/**
 * Initialize plugin
 *
 * @link  https://github.com/slimline/tinymce/wiki/slimline_templates()
 * @since 0.1.0
 */
function slimline_templates() {


}

function slimline_templates_custom_template( $templates ) {

	/**
	 * Get the current term object
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_queried_object/
	 *       Documentation of the `get_queried_object` function
	 */
	$object = get_queried_object();

	if ( $object instanceof WP_Term ) {
		$template = slimline_templates_get_term_template_slug( $object );
	} elseif ( $object instanceof WP_User ) { // if ( $object instanceof WP_Term )
		$template = slimline_templates_get_user_template_slug( $object );
	} // if ( $object instanceof WP_Term )

	/**
	 * If we have a custom template and there are no obvious errors with the file,
	 * add it to our template hierarchy
	 *
	 * @link https://developer.wordpress.org/reference/functions/validate_file/
	 *       Documentation of the `validate_file` function
	 */
	if ( $template && 0 === validate_file( $template ) ) {

		/**
		 * Prepend the custom template to the template hierarchy
		 *
		 * @link http://php.net/manual/en/function.array-unshift.php
		 *       Documentation of the PHP `array_unshift` function
		 */
		array_unshift( $templates, $template );

	} // if ( $template && 0 === validate_file( $template ) )

	return $templates;
}

function slimline_templates_get_term_template_slug( $object = null ) {

	$term = slimline_templates_get_term( $object );

	if ( ! $term instanceof WP_Term ) {
		return false;
	} // if ( ! $term instanceof WP_Term )

	$template = get_term_meta( $term->term_id, '_wp_term_template', true );

	if ( ! $template || 'default' == $template ) {
		return '';
	} // if ( ! $template || 'default' == $template )

	return $template;
}

function slimline_templates_get_user_template_slug( $object = null ) {

	$user = slimline_templates_get_user( $object );

	if ( ! $user instanceof WP_User ) {
		return false;
	} // if ( ! $user instanceof WP_User )

	$template = get_user_meta( $user->ID, '_wp_user_template', true );

	if ( ! $template || 'default' == $template ) {
		return '';
	} // if ( ! $template || 'default' == $template )

	return $template;
}

function slimline_templates_get_term( $term = null ) {

	/**
	 * If we've already been provided a term, return it now
	 */
	if ( $term instanceof WP_Term ) {

		return $term;

	/**
	 * If no term information has been provided, check for a queried term
	 *
	 * @link http://php.net/manual/en/function.is-null.php
	 *       Documentation of the PHP `is_null` function
	 */
	} elseif ( is_null( $term ) ) { // if ( $term instanceof WP_Term )

		/**
		 * Check if this is term archive
		 *
		 * @link https://developer.wordpress.org/reference/functions/is_category/
		 *       Documentation of the `is_category` function
		 * @link https://developer.wordpress.org/reference/functions/is_tag/
		 *       Documentation of the `is_tag` function
		 * @link https://developer.wordpress.org/reference/functions/is_tax/
		 *       Documentation of the `is_tax` function
		 */
		if ( is_category() || is_tag() || is_tax() ) {

			/**
			 * Return the currently queried term
			 *
			 * @link https://developer.wordpress.org/reference/functions/get_queried_object/
			 *       Documentation of the `get_queried_object` function
			 */
			return get_queried_object();

		} // if ( is_category() || is_tag() || is_tax() )

	/**
	 * If we have some information passed, try to retrieve the term
	 */
	} elseif ( $term ) { // if ( $term instanceof WP_Term )

		/**
		 * Try to retrieve the term based on the information provided
		 *
		 * @link https://developer.wordpress.org/reference/functions/get_term/
		 *       Documentation of the `get_term` function
		 */
		return get_term( $term );

	} // if ( $term instanceof WP_Term )

	/**
	 * Return FALSE (no term retrieved)
	 */
	return false;
}

function slimline_templates_get_user( $user = null ) {

	if ( $user instanceof WP_User ) {
		return $user;
	} elseif ( is_null( $user ) && is_author() ) {
		return get_queried_object();
	} elseif ( is_numeric( $user ) ) {
		return get_user_by( 'ID', $user );
	} elseif ( is_email( $user ) ) {
		return get_user_by( 'email', $user );
	} elseif ( $user ) {
		return get_user_by( 'login', $user );
	}

	return false;
}

function slimline_templates_add_blog_archive_template( $templates ) {

	if ( ! is_tax() || is_tax( 'post_format' ) ) {

		$offset = array_search( $templates, 'archive.php' );

		$templates = array_slice( $templates, 0, $offset - 1 ) + [ 'blog.php' ] + array_slice( $templates, $offset );

	} // if ( ! is_tax() || is_tax( 'post_format' ) )

	return $templates;

}

function slimline_templates_add_blog_home_template( $templates ) {

	$offset = array_search( $templates, 'home.php' );

	$templates = array_slice( $templates, 0, $offset ) + [ 'blog.php' ] + array_slice( $templates, $offset + 1 );

	return $templates;

}

function slimline_templates_add_user_role_template( $templates ) {

	$user = slimline_templates_get_user();

	$roles = $user->roles;

	foreach ( $roles as $role ) {
		$role_templates[] = "author-{$role}.php";
	} // foreach ( $roles as $role )

	$offset = array_search( $templates, 'archive.php' );

	$templates = array_slice( $templates, 0, $offset - 1 ) + $role_templates + array_slice( $templates, $offset );

	return $templates;

}