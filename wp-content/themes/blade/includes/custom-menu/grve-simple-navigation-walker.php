<?php

/*
*	Main Navigation Walker
*
* 	@version	1.0
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/

if ( !class_exists('Blade_Grve_Simple_Navigation_Walker') ) {

	class Blade_Grve_Simple_Navigation_Walker extends Walker_Nav_Menu {

		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';


			//Add Link Class
			if ( isset( $item->grve_link_classes ) && !empty( $item->grve_link_classes ) ) {
				$atts['class'] = $item->grve_link_classes;
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before;

			//Add Menu icon
			if ( isset( $item->grve_icon_fontawesome ) && !empty( $item->grve_icon_fontawesome ) ) {
				$item_output .= '<i class="grve-menu-icon ' . esc_attr( $item->grve_icon_fontawesome ) . '"></i>';
			}
			$item_output .= apply_filters( 'the_title', $item->title, $item->ID );

			$item_output .= $args->link_after;

			$item_output .= '</a>';

			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}


	}
}