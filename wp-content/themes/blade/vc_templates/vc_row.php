<?php

	$output = $out_pattern = $out_overlay = $out_image_bg = $out_video_bg = '';

	extract(
		shortcode_atts(
			array(
				'section_id'      => '',
				'font_color'      => '',
				'heading_color' => '',
				'section_type'      => 'fullwidth-background',
				'section_full_height' => 'no',
				'equal_column_height' => 'none',
				'tablet_portrait_equal_column_height' => 'none',
				'desktop_visibility' => '',
				'tablet_visibility' => '',
				'tablet_sm_visibility' => '',
				'mobile_visibility' => '',
				'bg_color'        => '',
				'bg_gradient_color_1' => 'rgba(3,78,144,0.9)',
				'bg_gradient_color_2' => 'rgba(25,180,215,0.9)',
				'bg_gradient_direction' => '90',
				'bg_type'        => '',
				'bg_image'        => '',
				'bg_image_type' => 'none',
				'bg_image_vertical_position' => 'top',
				'bg_position' => 'center-center',
				'bg_tablet_sm_position' => '',
				'parallax_sensor' => '250',
				'pattern_overlay' => '',
				'color_overlay' => '',
				'color_overlay_custom' => '#ffffff',
				'gradient_overlay_custom_1' => 'rgba(3,78,144,0.9)',
				'gradient_overlay_custom_2' => 'rgba(25,180,215,0.9)',
				'gradient_overlay_direction' => '90',
				'opacity_overlay' => '',
				'bg_video_webm' => '',
				'bg_video_mp4' => '',
				'bg_video_ogv' => '',
				'bg_video_loop' => 'yes',
				'bg_video_muted' => 'yes',
				'bg_video_url' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
				'bg_video_button' => '',
				'bg_video_button_position' => 'center-center',
				'padding_top' => '',
				'padding_bottom' => '',
				'margin_bottom' => '',
				'header_feature' => '',
				'footer_feature' => '',
				'el_class'        => '',
				'el_id'        => '',
				'css' => '',
				'scroll_section_title' => '',
				'scroll_header_style' => 'dark',
				'column_gap' => '',
			),
			$atts
		)
	);

	if ( 'image' == $bg_type || 'hosted_video' == $bg_type || 'video' == $bg_type  ) {

		if ( !empty ( $color_overlay ) && 'custom' != $color_overlay ) {

			//Overlay Classes
			$overlay_classes = array();
			$overlay_classes[] = 'grve-bg-overlay grve-bg-' . $color_overlay;
			if ( !empty ( $opacity_overlay ) ) {
				$overlay_classes[] = 'grve-opacity-' . $opacity_overlay;
			}
			$overlay_string = implode( ' ', $overlay_classes );
			$out_overlay .= '  <div class="' . esc_attr( $overlay_string ) .'"></div>';
		}

		if ( 'custom' == $color_overlay ) {
			$out_overlay .= '  <div class="grve-bg-overlay" style="background-color:' . esc_attr( $color_overlay_custom ) . '"></div>';
		}
		if ( 'gradient' == $color_overlay ) {
			$out_overlay .= '  <div class="grve-bg-overlay" style="background:' . esc_attr( $gradient_overlay_custom_1 ) . '; background: linear-gradient(' . esc_attr( $gradient_overlay_direction ) . 'deg,' . esc_attr( $gradient_overlay_custom_1 ) . ' 0%,' . esc_attr( $gradient_overlay_custom_2 ) . ' 100%);"></div>';
		}
	}

	// Pattern Overlay
	if ( !empty ( $pattern_overlay ) ) {
		$out_pattern .= '  <div class="grve-pattern"></div>';
	}

	//Background Image Classses
	$bg_image_classes = array( 'grve-bg-image' );

	if( 'horizontal-parallax-lr' == $bg_image_type || 'horizontal-parallax-rl' == $bg_image_type ){
		$bg_image_classes[] = 'grve-bg-center-' . $bg_image_vertical_position;
	}

	if( '' == $bg_image_type || 'none' == $bg_image_type || 'animated' == $bg_image_type ){
		$bg_image_classes[] = 'grve-bg-' . $bg_position;
		if ( !empty( $bg_tablet_sm_position ) ) {
			$bg_image_classes[] = 'grve-bg-tablet-sm-' . $bg_tablet_sm_position;
		}
	}

	$bg_image_string = implode( ' ', $bg_image_classes );

	//Background Image
	$img_style = blade_grve_build_shortcode_img_style( $bg_image ,$bg_image_type );

	if ( ( 'image' == $bg_type || 'hosted_video' == $bg_type || 'video' == $bg_type ) && !empty ( $bg_image ) && ('parallax' !== $bg_image_type ) ) {
		$out_image_bg .= '  <div class="' . esc_attr( $bg_image_string ) . '"  ' . $img_style . '></div>';
	}

	if ( ( 'image' == $bg_type || 'hosted_video' == $bg_type || 'video' == $bg_type ) && !empty ( $bg_image ) && ('parallax' == $bg_image_type ) ) {
		$out_image_bg .= '  <div class="' . esc_attr( $bg_image_string ) . '" ' . $img_style . '></div>';
	}

	//Background Video
	if ( 'hosted_video' == $bg_type && ( !empty ( $bg_video_webm ) || !empty ( $bg_video_mp4 ) || !empty ( $bg_video_ogv ) ) ) {

		$video_settings = array(
			'preload' => 'auto',
			'autoplay' => 'yes',
			'loop' => $bg_video_loop,
			'muted' => $bg_video_muted,
		);

		$out_video_bg .= '<div class="grve-bg-video">';
		$out_video_bg .=  '<video data-autoplay ' . blade_grve_print_media_video_settings( $video_settings ) . '>';
		if ( !empty ( $bg_video_webm ) ) {
			$out_video_bg .=  '<source src="' . esc_url( $bg_video_webm ) . '" type="video/webm">';
		}
		if ( !empty ( $bg_video_mp4 ) ) {
			$out_video_bg .=  '<source src="' . esc_url( $bg_video_mp4 ) . '" type="video/mp4">';
		}
		if ( !empty ( $bg_video_ogv ) ) {
			$out_video_bg .=  '<source src="' . esc_url( $bg_video_ogv ) . '" type="video/ogg">';
		}
		$out_video_bg .=  '</video>';
		$out_video_bg .= '</div>';
	}

	//YouTube Video
	$out_video_bg_url = '';
	$has_video_bg = ( 'video' == $bg_type && ! empty( $bg_video_url ) && vc_extract_youtube_id( $bg_video_url ) );
	if ( $has_video_bg ) {
		wp_enqueue_script( 'vc_youtube_iframe_api_js' );
		$out_video_bg_url .= '<div class="grve-bg-video-wrapper" data-vc-video-bg="' . esc_attr( $bg_video_url ) . '"></div>';
		if ( !empty( $bg_video_button ) ) {
			$out_video_bg_url .= '<a class="grve-video-popup grve-bg-video-button-' . esc_attr( $bg_video_button ) . '" href="' . esc_url( $bg_video_url ) . '">';
			$out_video_bg_url .= '<svg version="1.1" class="grve-icon-video grve-icon-' . esc_attr( $bg_video_button_position )  . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="62px" height="62px" viewBox="0 0 62 62" enable-background="new 0 0 62 62" xml:space="preserve"><g><path fill="#ffffff" d="M22.281,44.336V17.664L44.508,31L22.281,44.336z M24.219,21.086v19.828L40.742,31L24.219,21.086z"/></g><path fill="#ffffff" d="M31,1.938c16.025,0,29.062,13.037,29.062,29.062S47.025,60.062,31,60.062S1.938,47.025,1.938,31S14.975,1.938,31,1.938 M31,0C13.88,0,0,13.88,0,31c0,17.121,13.88,31,31,31c17.121,0,31-13.879,31-31C62,13.88,48.121,0,31,0L31,0z"/></svg>';
			$out_video_bg_url .= '</a>';
		}
	}

	//Section Classses
	$section_classes = array( 'grve-section' );

	$section_classes[] = 'grve-' . $section_type ;

	if( 'horizontal-parallax-lr' == $bg_image_type || 'horizontal-parallax-rl' == $bg_image_type ){
		$section_classes[] = 'grve-' . $bg_image_type;
		$section_classes[] = 'grve-bg-parallax';
	} else {
		$section_classes[] = 'grve-bg-' . $bg_image_type;
	}

	if ( !empty ( $heading_color ) ) {
		$section_classes[] = 'grve-headings-' . $heading_color;
	}
	if ( !empty ( $header_feature ) ) {
		$section_classes[] = 'grve-feature-header';
	}
	if ( !empty ( $footer_feature ) ) {
		$section_classes[] = 'grve-feature-footer';
	}
	if( 'none' != $equal_column_height ) {
		$section_classes[] = 'grve-' . $equal_column_height;
	}
	if( 'no' != $section_full_height ) {
		$section_classes[] = 'grve-' . $section_full_height;
	}

	if( 'none' != $equal_column_height || 'no' != $section_full_height ) {
		$section_classes[] = 'grve-custom-height';
		$section_classes[] = 'grve-prepare-custom-height';
	}

	if ( !empty ( $column_gap ) ) {
			$section_classes[] = 'grve-column-has-gap';
			$section_classes[] = 'grve-column-gap-' . $column_gap;
		}
	if ( !empty ( $el_class ) ) {
		$section_classes[] = $el_class;
	}

	if( vc_settings()->get( 'not_responsive_css' ) != '1') {
		if ( !empty( $desktop_visibility ) ) {
			$section_classes[] = 'grve-desktop-row-hide';
		}
		if ( !empty( $tablet_visibility ) ) {
			$section_classes[] = 'grve-tablet-row-hide';
		}
		if ( !empty( $tablet_sm_visibility ) ) {
			$section_classes[] = 'grve-tablet-sm-row-hide';
		}
		if ( !empty( $mobile_visibility ) ) {
			$section_classes[] = 'grve-mobile-row-hide';
		}
	}

	$section_string = implode( ' ', $section_classes );

	$wrapper_attributes = array();

	$wrapper_attributes[] = 'class="' . esc_attr( $section_string ) . '"';

	if ( is_page_template( 'page-templates/template-full-page.php' ) ) {
		$scrolling_lock_anchors = blade_grve_post_meta( 'grve_scrolling_lock_anchors', 'yes' );
		if( 'no' == $scrolling_lock_anchors ) {
			$section_uniqid = uniqid('grve-scrolling-section-');
			if ( !empty ( $section_id ) ) {
				$wrapper_attributes[] = 'data-anchor="' . esc_attr( $section_id ) . '"';
			} else {
				$wrapper_attributes[] = 'data-anchor="' . esc_attr( $section_uniqid ) . '"';
			}
		}
		$wrapper_attributes[] = 'data-anchor-tooltip="' . esc_attr( $scroll_section_title ) . '"';
		$wrapper_attributes[] = 'data-header-color="' . esc_attr( $scroll_header_style ) . '"';
	} else {
		if ( !empty ( $section_id ) ) {
			$wrapper_attributes[] = 'id="' . esc_attr( $section_id ) . '"';
		}
	}

	if( 'parallax' == $bg_image_type || 'horizontal-parallax-lr' == $bg_image_type || 'horizontal-parallax-rl' == $bg_image_type ){
		$wrapper_attributes[] = 'data-parallax-sensor="' . esc_attr( $parallax_sensor ) . '"';
	}

	if( 'none' != $tablet_portrait_equal_column_height ) {
		$wrapper_attributes[] = 'data-tablet-portrait-equal-columns="inherit"';
	}
	
	if ( 'gradient' != $bg_type ) {
		$bg_gradient_color_1 = $bg_gradient_color_2 = $bg_gradient_direction = "";
	}

	$style = blade_grve_build_shortcode_style(
		array(
			'bg_color' => $bg_color,
			'bg_gradient_color_1' => $bg_gradient_color_1,
			'bg_gradient_color_2' => $bg_gradient_color_2,
			'bg_gradient_direction' => $bg_gradient_direction,
			'font_color' => $font_color,
			'padding_top' => $padding_top,
			'padding_bottom' => $padding_bottom,
			'margin_bottom' => $margin_bottom,
		)
	);
	if( !empty( $style ) ) {
		$wrapper_attributes[] = $style;
	}

	if ( !empty( $column_gap ) ) {
		$content = str_replace('[vc_column ', '[vc_column column_has_gap="yes" ', $content);
		$content = str_replace('[vc_column]', '[vc_column column_has_gap="yes"]', $content);
	}

	//Section Output
	$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
	$output .= '  <div class="grve-container">';
	$output	.= '    <div class="grve-row grve-bookmark">';
	$output	.= do_shortcode( $content );
	$output	.= '    </div>';
	$output	.= '  </div>';
	$output .= '  <div class="grve-background-wrapper">';
	$output .= $out_video_bg_url;
	$output .= $out_pattern;
	$output .= $out_overlay;
	$output .= $out_image_bg;
	$output .= $out_video_bg;
	$output	.= '  </div>';

	$output	.= '</div>';

	print $output;

//Omit closing PHP tag to avoid accidental whitespace output errors.
