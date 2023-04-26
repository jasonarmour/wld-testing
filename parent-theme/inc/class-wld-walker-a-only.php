<?php

class WLD_Walker_A_Only extends Walker_Nav_Menu {

	public $first = true;

	public function start_lvl( &$output, $depth = 0, $args = null ): void {
	}

	public function end_lvl( &$output, $depth = 0, $args = null ): void {
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
		$item_output    = '';
		$classes        = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[]      = 'menu-item-' . $item->ID;
		$args           = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
		$class_names    = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names    = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$item_id        = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$attr_id        = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes     = '';
		foreach ( (array) $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				if ( 'href' === $attr ) {
					$value = esc_url( $value );
				} else {
					$value = esc_attr( $value );
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		/** @noinspection PhpUndefinedFieldInspection */
		$title  = apply_filters( 'the_title', $item->title, $item->ID );
		$title  = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		$before = '';
		if ( isset( $args->empty_first_before ) && $args->empty_first_before && $this->first ) {
			$this->first = false;
		} else {
			$before = $args->before;
		}
		$item_output .= $before;
		$item_output .= '<a' . $attributes . $attr_id . $class_names . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a> ';
		$item_output .= $args->after;
		$output      .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ): void {
	}
}
