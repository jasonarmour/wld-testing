<?php

class WLD_WC_BI_Product_Filters {
	public static function init() : void {

		add_action(
			'wp_ajax_loadMoreProducts',
			array( self::class, 'loadMoreProducts' )
		);

		add_action(
			'wp_ajax_nopriv_loadMoreProducts',
			array( self::class, 'loadMoreProducts' )
		);

		add_action(
			'wp_enqueue_scripts',
			array( self::class, 'bi_enqueue_scripts_action' )
		);

	}

	public static function bi_enqueue_scripts_action() {

		wp_register_style( 'daterangepicker', get_stylesheet_directory_uri() . '/plugins/css/daterangepicker.css', array(), '1' );
		wp_register_script( 'daterangepicker', get_stylesheet_directory_uri() . '/plugins/js/daterangepicker.min.js', array( 'moment' ), '1' );
		wp_add_inline_script(
			'daterangepicker',
			'
                const $input = $( "#custom_range" );


                $input.daterangepicker( {}, function( start, end ) {
	                $input.val( start.format( "m/d/yy" ) + " - " + end.format( "m/d/yy" ) );
                } ).on("apply.daterangepicker", function(e, picker) {
					$input.trigger( "change", [ true ] ).prop("checked", true);
                }).on("show.daterangepicker", function() {
					console.info("show");
					if ( $input.is(":checked")) {
						setTimeout( () => $( ".cancelBtn" ).trigger("click"), 200);
					}
                }).on("cancel.daterangepicker", function() {
					$input.prop("checked", false).trigger( "change", [ true ] );
                });
            '
		);
	}

	public static function get_products_query( $needed_page, $data ) : WP_Query {
		if ( wld_get( 'wld_search_results_page_filters_products_per_page' ) ) {
			$post_per_page = wld_get( 'wld_search_results_page_filters_products_per_page' );
		} else {
			$post_per_page = 14;
		}

		$moreProducts_list_arg = array(
			'post_type'      => 'product',
			'posts_per_page' => $post_per_page,
			'post_status'    => 'publish',
			'paged'          => $needed_page,
			'order'          => 'DESC',
			'orderby'        => 'meta_value',
			'meta_key'       => '_date_published',
		);

		if ( isset( $data['topic'] ) ) {
			$moreProducts_list_arg['tax_query']['relation'] = 'AND';
			$moreProducts_list_arg['tax_query'][]           = array(
				'taxonomy' => 'topic',
				'field'    => 'slug',
				'terms'    => $data['topic'],
				'operator' => 'IN'
			);

		}

		if ( isset( $data['focus'] ) ) {
			$moreProducts_list_arg['tax_query']['relation'] = 'AND';
			$moreProducts_list_arg['tax_query'][]           = array(
				'taxonomy' => 'focus',
				'field'    => 'slug',
				'terms'    => $data['focus'],
				'operator' => 'IN'
			);

		}

		if ( isset( $data['type'] ) ) {
			$moreProducts_list_arg['meta_query']['relation'] = 'AND';

			$moreProducts_list_arg['meta_query'][] = array(
				'key'   => '_' . 'type',
				'value' => $data['type']
			);
		}


		if ( isset( $data['period_type'] ) ) {
			$moreProducts_list_arg['meta_query']['relation'] = 'AND';

			$period_type = $data['period_type'];
			$period_type = str_replace( ' - ', '_', $period_type );
			$now_date    = date_create();

			switch ( $period_type ) {
				case '1':
					$period_start = $now_date->modify( '+1 day' )->format( 'Y-m-d' );
					$period_end   = $now_date->modify( '+1 month' )->format( 'Y-m-d' );
					break;
				case '2':
					$period_end   = $now_date->format( 'Y-m-d' );
					$period_start = $now_date->modify( '-7 day' )->format( 'Y-m-d' );
					break;
				case '3':
					$period_end   = $now_date->format( 'Y-m-d' );
					$period_start = $now_date->modify( '-1 month' )->format( 'Y-m-d' );
					break;
				case '4':
					$period_end   = $now_date->format( 'Y-m-d' );
					$period_start = $now_date->modify( '-1 year' )->format( 'Y-m-d' );
					break;
				default:
					$period_dates = explode( '_', $period_type ); // 2022-10-11_2022-12-30
					$period_start = $period_dates[0];
					$period_end   = $period_dates[1];
			}

			$moreProducts_list_arg['meta_query'][] = array(
				'key'      => '_date_published',
				'type'     => 'DATE',
				'compare'  => 'BETWEEN',
				'value'    => array( $period_start, $period_end ),
				'orderby'  => 'meta_value',
				'meta_key' => '_date_published',
			);
		} else {
			$empty_filter = empty( $data['topic'] ) && empty( $data['focus'] ) && empty( $data['type'] );
			$now_date     = date_create_immutable();

			$post_in_presentation = get_posts(
				array_merge(
					array_merge_recursive(
						$moreProducts_list_arg,
						array(
							'meta_query' => array(
								array(
									'key'     => '_presentation',
									'value'   => $data['presentation'] ?? array( 'nothing' ),
									'compare' => 'IN',
								),
								array(
									'key'     => '_date_published',
									'compare' => 'EXISTS',
								)
							),
						)
					),
					array(
						'fields'         => 'ids',
						'posts_per_page' => - 1,
						'order'          => 'ASC',
					)
				)
			);

			if ( $empty_filter ) {
				$post_in_events = get_posts(
					array_merge(
						$moreProducts_list_arg,
						array(
							'meta_query'     => array(
								'relation' => 'AND',
								array(
									'key'     => '_type',
									'value'   => array( 'webinar' ),
									'compare' => 'IN',
								),
								array(
									'key'     => '_date_published',
									'type'    => 'DATE',
									'compare' => 'BETWEEN',
									'value'   => array(
										$now_date->format( 'Y-m-d' ),
										$now_date->modify( '+30 days' )->format( 'Y-m-d' )
									),
								)
							),
							'fields'         => 'ids',
							'posts_per_page' => - 1,
							'order'          => 'ASC',
							'post__not_in'   => $post_in_presentation,
						)
					)
				);

				$post_in_other = get_posts(
					array_merge(
						$moreProducts_list_arg,
						array(
							'fields'         => 'ids',
							'posts_per_page' => - 1,
							'order'          => 'DESC',
							'post__not_in'   => array_merge( $post_in_presentation, $post_in_events ),
						)
					)
				);

				$moreProducts_list_arg = array(
					'post_type'      => 'product',
					'orderby'        => 'post__in ',
					'order'          => 'ASC',
					'posts_per_page' => $post_per_page,
					'paged'          => $needed_page,
					'post__in'       => array_merge( $post_in_presentation, $post_in_events, $post_in_other )
				);
			} else {
				$post_in_filter = get_posts(
					array_merge(
						$moreProducts_list_arg,
						array(
							'fields'         => 'ids',
							'posts_per_page' => - 1,
							'order'          => 'ASC',
							'post__not_in'   => $post_in_presentation,
						)
					)
				);

				$moreProducts_list_arg = array(
					'post_type'      => 'product',
					'orderby'        => 'post__in ',
					'posts_per_page' => $post_per_page,
					'order'          => 'ASC',
					'paged'          => $needed_page,
					'post__in'       => array_merge( $post_in_presentation, $post_in_filter )
				);
			}
		}

		return new WP_Query( $moreProducts_list_arg );
	}

	public static function loadMoreProducts() {
		$outputTemplate    = '';
		$response          = array();
		$moreProducts_list = static::get_products_query( $_POST['paged'], $_POST );
		if ( $moreProducts_list->have_posts() ) {
			$response['max_pages'] = $moreProducts_list->max_num_pages;

			$response['status'] = 'success';

			ob_start();

			while ( $moreProducts_list->have_posts() ) {
				$moreProducts_list->the_post();
				get_template_part( 'templates/template-part/filter-product' );
			}
			$outputTemplate .= ob_get_contents();
			ob_end_clean();

			$response['output'] = $outputTemplate;
		} else {
			$response['status'] = 'error';
			$response['output'] = 'Objects no found';
		}

		wp_reset_postdata();

		echo json_encode( $response );
		wp_die();
	}

}

