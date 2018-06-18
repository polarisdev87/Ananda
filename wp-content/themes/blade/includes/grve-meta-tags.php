<?php

/*
*	Meta Tags
*
* 	@version	1.0
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/

if ( !function_exists('blade_grve_opengraph_add_prefix') ) {
	function blade_grve_opengraph_add_prefix( $output ) {

		if ( blade_grve_visibility( 'meta_opengraph' ) ) {
			$prefixes = array(
				'og' => 'http://ogp.me/ns#',
			);
			$prefixes = apply_filters( 'blade_grve_opengraph_prefixes', $prefixes );

			$prefix_str = '';
			foreach ( $prefixes as $k => $v ) {
				$prefix_str .= $k . ': ' . $v . ' ';
			}
			$prefix_str = trim( $prefix_str );

			if ( preg_match( '/(prefix\s*=\s*[\"|\'])/i', $output ) ) {
				$output = preg_replace( '/(prefix\s*=\s*[\"|\'])/i', '${1}' . $prefix_str, $output );
			} else {
				$output .= ' prefix="' . esc_attr( $prefix_str ) . '"';
			}
		}

		return $output;
	}
}
add_filter( 'language_attributes', 'blade_grve_opengraph_add_prefix' );

if ( !function_exists('blade_grve_print_meta_tags') ) {
	function blade_grve_print_meta_tags() {

		$meta_name_tags = array();
		$meta_property_tags = array();

		//Viewport
		$meta_viewport = 'width=device-width, initial-scale=1';
		if ( blade_grve_visibility( 'meta_viewport_responsive', '1' ) ) {
			$meta_viewport = 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no';
		}
		$meta_name_tags['viewport'] = $meta_viewport;

		//Application Name
		$meta_name_tags['application-name'] = get_bloginfo( 'name' );

		if ( blade_grve_visibility( 'meta_opengraph' ) || blade_grve_visibility( 'meta_twitter' ) ) {
			$meta_title = $meta_description = $meta_url = $meta_image_url = "";

			if ( is_front_page() || is_home() ) {
				$meta_type = 'website';
				$meta_title = get_bloginfo( 'name' );
				$meta_description = get_bloginfo( 'description' );
				$meta_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			} else {
				$meta_type = 'article';
			}

			if ( is_singular() ) {
				//Meta title
				if( empty( $meta_title ) ) {
					$meta_title = get_the_title();
				}
				//Meta Description
				if( empty( $meta_description ) ) {
					$post = get_queried_object();
					if ( ! empty( $post->post_excerpt ) ) {
						$content = $post->post_excerpt;
					} else {
						$content = $post->post_content;
					}
					$content = strip_tags( strip_shortcodes( $content ) );
					$meta_description = wp_trim_words( $content, 30 );
				}
				//Meta URL
				if( empty( $meta_url ) ) {
					$meta_url = get_permalink();
				}
				//Meta Image
				if ( $thumbnail_id = get_post_thumbnail_id() ) {
					$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, 'large' );
					if ( $thumbnail_src ) {
						$meta_image_url = $thumbnail_src[0];
					}
				}
			}

			if ( is_singular() || is_front_page() || is_home() ) {

				//Opengraph
				if ( blade_grve_visibility( 'meta_opengraph' ) ) {
					$locale = get_locale();
					$meta_property_tags['og:locale'] = $locale;
					$meta_property_tags['og:site_name'] = get_bloginfo( 'name' );
					$meta_property_tags['og:type'] = $meta_type;
					$meta_property_tags['og:title'] = $meta_title;
					$meta_property_tags['og:description'] = $meta_description;
					$meta_property_tags['og:url'] = $meta_url;
					$meta_property_tags['og:image'] = $meta_image_url;
				}
				//Twitter Cards
				if ( blade_grve_visibility( 'meta_twitter' ) ) {
					if( !empty( $meta_image_url ) ) {
						$meta_name_tags['twitter:card'] = "summary_large_image";
					} else {
						$meta_name_tags['twitter:card'] = "summary";
					}
					$meta_name_tags['twitter:title'] = $meta_title;
					$meta_name_tags['twitter:description'] = $meta_description;
					$meta_name_tags['twitter:url'] = $meta_url;
					$meta_name_tags['twitter:image'] = $meta_image_url;

				}
			}
		}

		$meta_name_tags = apply_filters( 'blade_grve_meta_name_tags', $meta_name_tags );
		$meta_property_tags = apply_filters( 'blade_grve_meta_property_tags', $meta_property_tags );

		// Output name tags
		if ( isset( $meta_name_tags ) && is_array( $meta_name_tags ) && !empty( $meta_name_tags ) ) {
			foreach ( $meta_name_tags as $meta_name => $meta_content ) {
				if ( !empty( $meta_content ) ) {
					if ( stripos( $meta_content, ':url' ) != 0 || stripos( $meta_content, ':image' ) != 0 ) {
						echo '<meta name="' . esc_attr( $meta_name ) . '" content="' . esc_url( $meta_content ) . '">' . "\n";
					} else {
						echo '<meta name="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $meta_content ) . '">' . "\n";
					}
				}
			}
		}
		// Output property tags
		if ( isset( $meta_property_tags ) && is_array( $meta_property_tags ) && !empty( $meta_property_tags ) ) {
			foreach ( $meta_property_tags as $meta_name => $meta_content ) {
				if ( !empty( $meta_content ) ) {
					if ( stripos( $meta_content, ':url' ) != 0 || stripos( $meta_content, ':image' ) != 0 ) {
						echo '<meta property="' . esc_attr( $meta_name ) . '" content="' . esc_url( $meta_content ) . '">' . "\n";
					} else {
						echo '<meta property="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $meta_content ) . '">' . "\n";
					}
				}
			}
		}

	}
}
add_action( 'wp_head', 'blade_grve_print_meta_tags', 5 );

//Omit closing PHP tag to avoid accidental whitespace output errors.
