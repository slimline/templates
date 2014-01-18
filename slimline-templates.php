<?php
/**
 * Plugin Name: Slimline Post Type, Author and Term Templates
 * Plugin URI: http://www.michaeldozark.com/slimline/post-type-author-term-templates/
 * Description: Creates a UI for choosing specific templates for post types, users and terms similar to page templates.
 * Author: Michael Dozark
 * Author URI: http://www.michaeldozark.com/
 * Version: 0.1.0
 * License: GPL2
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
 * @package Slimline
 * @subpackage Post Type, Author and Term Templates
 * @version 0.1.0
 * @author Michael Dozark <michael@michaeldozark.com>
 * @copyright Copyright (c) 2014, Michael Dozark
 * @link http://www.michaeldozark.com/slimline/post-type-author-term-templates/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly

/**
 * Initialize the plugin. This should be the only instance of add_action() outside of a defined function.
 */
add_action( 'init', 'slimline_templates_init' );

/**
 * slimline_templates_init function
 *
 * @since 0.1.0
 */
function slimline_templates_init() {

	define( 'SLIMLINE_TEMPLATES_DIR', plugin_dir_path( __FILE__ ) );
	define( 'SLIMLINE_TEMPLATES_INC', trailingslashit( SLIMLINE_TEMPLATES_DIR ) . 'inc' );

	add_action( 'load-edit-tags.php', 'slimline_templates_taxonomies' );
	add_action( 'load-post.php', 'slimline_templates_single_posts' );
	add_action( 'load-post-new.php', 'slimline_templates_single_posts' );
	add_action( 'load-profile.php', 'slimline_templates_users' );
	add_action( 'load-users.php', 'slimline_templates_users' );

	if ( ! defined( 'SLIMLINE_VERSION' ) ) {

		include( trailingslashit( SLIMLINE_TEMPLATES_INC ) . 'template.php' );

		add_filter( 'attachment_template', 'slimline_templates_single_template', 999 ); // add custom attachment templates to template hierarchy | inc/template.php
		add_filter( 'author_template', 'slimline_templates_author_template', 999 ); // add custom user templates to template hierarchy  | inc/template.php
		add_filter( 'category_template', 'slimline_templates_taxonomy_template', 999 ); // add custom category templates to template hierarchy  | inc/template.php
		add_filter( 'single_template', 'slimline_templates_single_template', 999 ); // add custom single post and custom post type templates to template hierarchy  | inc/template.php
		add_filter( 'tag_template', 'slimline_templates_taxonomy_template', 999 ); // add custom tag templates to template hierarchy  | inc/template.php
		add_filter( 'taxonomy_template', 'slimline_templates_taxonomy_template', 999 ); // add custom taxonomy templates to template hierarchy  | inc/template.php

	}
}

/**
 * slimline_templates_single_posts function
 *
 * @since 0.1.0
 */
function slimline_templates_single_posts() {

	include( trailingslashit( SLIMLINE_TEMPLATES_INC ) . 'meta-boxes.php' );

	add_action( 'add_meta_boxes', 'slimline_templates_single_posts_add_meta_box' );
}