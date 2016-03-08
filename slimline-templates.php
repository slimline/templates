<?php
/**
 * Plugin Name: Slimline Post & Term Templates
 * Plugin URI: http://www.michaeldozark.com/slimline/post-term-templates/
 * Description: Custom templates for terms and non-page posts.
 * Author: Michael Dozark
 * Author URI: http://www.michaeldozark.com/
 * Version: 0.1.0
 * Text Domain: slimline-templates
 * Domain Path: /lang
 * License: GNU General Public License version 2.0
 * License URI: LICENSE
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
 * @package Slimline Templates
 * @subpackage Plugin
 * @version 0.1.0
 * @author Michael Dozark <michael@michaeldozark.com>
 * @copyright Copyright (c) 2014, Michael Dozark
 * @link http://www.michaeldozark.com/slimline/post-term-templates/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly

/**
 * Fire the initialization function. This should be the only instance of add_action that
 * is not contained within a defined function.
 */
add_action( 'plugins_loaded', 'slimline_templates_core' );

/**
 * slimline_templates_core function
 *
 * @since 0.1.0
 */
function slimline_templates_core() {

	define( 'SLIMLINE_TEMPLATES_DIR', plugin_dir_path( __FILE__ ) );
	define( 'SLIMLINE_TEMPLATES_INC', trailingslashit( SLIMLINE_TEMPLATES_DIR ) . 'inc' );

	include( trailingslashit( SLIMLINE_TEMPLATES_INC ) . 'class-slimline-wp-theme.php' );
	include( trailingslashit( SLIMLINE_TEMPLATES_INC ) . 'template.php' );

	add_action( 'wp_loaded', 'slimline_templates_admin' );

	add_filter( 'attachment_template', 'slimline_templates_single_template', 0 ); // standardize attachment templates hierarchy and add custom templates | inc/template.php
	add_filter( 'author_template', 'slimline_templates_author_template', 0 ); // standardize user templates hierarchy and add custom templates | inc/template.php
	add_filter( 'category_template', 'slimline_templates_taxonomy_template', 0 ); // standardize category templates hierarchy and add custom templates | inc/template.php
	add_filter( 'single_template', 'slimline_templates_single_template', 0 ); // standardize single post and custom post type templates hierarchy and add custom templates | inc/template.php
	add_filter( 'tag_template', 'slimline_templates_taxonomy_template', 0 ); // standardize tag templates hierarchy and add custom templates | inc/template.php
	add_filter( 'taxonomy_template', 'slimline_templates_taxonomy_template', 0 ); // standardize taxonomy templates hierarchy and add custom templates | inc/template.php

}

function slimline_templates_admin() {

	if ( ! is_admin() ) {
		return;
	}

	include( trailingslashit( SLIMLINE_TEMPLATES_INC ) . 'admin.php' );

	add_action( 'add_meta_boxes', 'slimline_templates_add_post_template_meta_box' ); // adds meta boxes with template drop-downs to all post types | inc/admin.php
	add_action( 'save_post', 'slimline_save_custom_templates' ); // saves custom post template as post meta | inc/admin.php
}