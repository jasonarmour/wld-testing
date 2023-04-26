<?php

class WLD_WC_BI_Query {

	public static function bi_products_query( $items_per_page ): array {
		$terms_tag   = get_sub_field( 'prod_tags' );
		$terms_topic = get_sub_field( 'prod_topics' );
		$terms_focus = get_sub_field( 'prod_focuses' );

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => $items_per_page,
		);

		$args['tax_query']['relation'] = 'AND';

		$rel_topic = wld_get( 'relation_topic' );

		if ( $terms_topic ) {
			$topics             = array();
			$topics['relation'] = $rel_topic;
			foreach ( $terms_topic as $item ) {
				$topics[] = array(
					'taxonomy' => 'topic',
					'field'    => 'term_id',
					'terms'    => $item,
				);
			}
			$args['tax_query'][] = $topics;
		}

		$rel_focus = wld_get( 'relation_focus' );

		if ( $terms_focus ) {
			$focuses             = array();
			$focuses['relation'] = $rel_focus;
			foreach ( $terms_focus as $item ) {
				$focuses[] = array(
					'taxonomy' => 'topic',
					'field'    => 'term_id',
					'terms'    => $item,
				);
			}
			$args['tax_query'][] = $focuses;
		}

		$rel_tag = wld_get( 'relation_tag' );

		if ( $terms_tag ) {
			$tags             = array();
			$tags['relation'] = $rel_tag;
			foreach ( $terms_tag as $item ) {
				$tags[] = array(
					'taxonomy' => 'product_tag',
					'field'    => 'term_id',
					'terms'    => $item,
				);
			}
			$args['tax_query'][] = $tags;
		}

		$args['meta_query']['relation'] = 'AND';

		$names = array();

		while ( wld_loop( 'wld_meta_attributes' ) ) :

			$names[] = strtolower( wld_get( 'name' ) );

		endwhile;

		foreach ( $names as $name ) {
			$values = get_sub_field( $name );
			if ( $values ) {
				$top             = array();
				$top['relation'] = 'OR';
				foreach ( $values as $item ) {
					$top[] = array(
						'key'   => '_' . strtolower( $name ),
						'value' => $item,
					);
				}
				$args['meta_query'][] = $top;
			}
		}

		return $args;
	}

}
