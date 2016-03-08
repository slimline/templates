<?php
/**
 * Slimline_WP_Theme class
 *
 * Extends WP_Theme to add a get_post_templates() function so we can 
 *
 * @since 0.1.0
 */
class Slimline_WP_Theme extends WP_Theme {

	public function get_page_templates( $post = null ) {

		return $this->get_post_templates( $post, 'page' );
	}

	/**
	 * get_post_templates function
	 *
	 * Returns the theme's post templates. Modified from WP_Theme::get_page_templates() to add multi-post-type support.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param WP_Post|null $post Optional. The post being edited, provided for context.
	 * @return array Array of post templates, keyed by filename, with the value of the translated header name.
	 */
	public function get_post_templates( $post = null, $post_type = 'page' ) {

		// If you screw up your current theme and we invalidate your parent, most things still work. Let it slide.
		if ( $this->errors() && array( 'theme_parent_invalid' ) !== $this->errors()->get_error_codes() ) {
			return array();
		}

		$post_templates = $this->cache_get( "{$post_type}_templates" );

		if ( ! is_array( $post_templates ) ) {

			$post_templates = array();

			$files = (array) $this->get_files( 'php', 1 );

			$post_template_header = ucwords( str_replace( '_', ' ', $post_type ) );

			/**
			 * Create a regex pattern to check against the template files
			 */
			if ( 'page' === $post_type ) {
				// Pages use "Template Name:" to accommodate WordPress standard naming and "Page Template:" for internal plugin consistency
				$regex = array( '|Template Name:(.*)$|mi', '|Page Template:(.*)$|mi' );
			} else {
				// e.x. "Attachment Template:", "Post Template:", etc. We are using the post_type instead of a label to keep the naming consistent cross-language
				$regex =  '|' . $post_template_header . ' Template:(.*)$|mi' );
			}

			foreach ( $files as $file => $full_path ) {

				if ( ! preg_match( $regex, file_get_contents( $full_path ), $header ) ) {
					continue;
				}

				$post_templates[ $file ] = _cleanup_header_comment( $header[1] );

			} // foreach ( $files as $file => $full_path )

			$this->cache_add( "{$post_type}_templates", $post_templates );

		} // if ( ! is_array( $post_templates ) )

		if ( $this->load_textdomain() ) {

			if ( 'page' === $post_type ) {
				foreach ( $post_templates as &$post_template ) {
					$post_template = $this->translate_header( 'Template Name', $post_template );
				}
			}

			foreach ( $post_templates as &$post_template ) {
				$post_template = $this->translate_header( "{$post_template_header} Template", $post_template );
			}

		} // if ( $this->load_textdomain() )

		if ( $this->parent() ) {
			$post_templates += $this->parent()->get_page_templates( $post );
		}

		/**
		 * Filter list of page templates for a theme.
		 *
		 * This filter does not currently allow for page templates to be added.
		 *
		 * @since 3.9.0
		 *
		 * @param array        $post_templates Array of page templates. Keys are filenames,
		 *                                     values are translated names.
		 * @param WP_Theme     $this           The theme object.
		 * @param WP_Post|null $post           The post being edited, provided for context, or null.
		 */
		$return = apply_filters( "theme_{$post_type}_templates", $post_templates, $this, $post );

		return array_intersect_assoc( $return, $post_templates );
	}
}