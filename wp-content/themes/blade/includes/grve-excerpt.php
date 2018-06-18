<?php

/*
 *	Excerpt functions
 *
 * 	@version	1.0
 * 	@author		Greatives Team
 * 	@URI		http://greatives.eu
 */


 /**
 * Custom excerpt
 */
if ( !function_exists('blade_grve_excerpt') ) {
	function blade_grve_excerpt( $limit, $more = '0' ) {
		global $post;
		$post_id = $post->ID;
		$excerpt = "";

		if ( has_excerpt( $post_id ) ) {
			if ( 0 != $limit ) {
				$excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
			}
			if ( '1' == $more ) {
				$excerpt .= blade_grve_read_more( $post_id );
			}
		} else {
			$content = get_the_content('');
			$content = do_shortcode( $content );
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]>', $content);
			if ( 0 != $limit ) {
				$excerpt = '<p>' . wp_trim_words( $content, $limit ) . '</p>';
			}
			if ( '1' == $more ) {
				$excerpt .= blade_grve_read_more( $post_id );
			}
		}
		return	$excerpt;
	}
}

 /**
 * Custom more
 */
if ( !function_exists('blade_grve_read_more') ) {
	function blade_grve_read_more( $post_id = '') {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		$more_button = '<a class="grve-read-more grve-link-text" href="' . esc_url( get_permalink( $post_id ) ) . '"><span>' . esc_html__( 'read more', 'blade' ) . '</span></a>';
		return $more_button;
	}
}

//Omit closing PHP tag to avoid accidental whitespace output errors.
