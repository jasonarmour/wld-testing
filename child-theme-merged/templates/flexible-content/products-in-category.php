<?php
$now_date     = date_create();
$period_start = $now_date->modify( 'today' )->format( 'Y-m-d' );
$period_end   = $now_date->modify( '+30 day' )->format( 'Y-m-d' );


$args1 = array(
	'post_type'      => 'product',
	'posts_per_page' => 12,
	'tax_query'      => array(
		array(
			'taxonomy' => 'topic',
			'field'    => 'term_id',
			'terms'    => array( get_sub_field( 'prod_topic' ) ),
			'operator' => 'AND'
		)
	),
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'key'     => '_presentation',
			'value'   => 'featured',
			'compare' => 'LIKE',
		)
	),
	'order'          => 'ASC',
	'orderby'        => 'meta_value',
	'meta_key'       => '_date_published',
);

$posts_in_category1 = get_posts( $args1 );
$count              = count( $posts_in_category1 );
$all_posts          = $posts_in_category1;

if ( $count < 12 ) {
	$args2 = array(
		'post_type'      => 'product',
		'posts_per_page' => 12 - $count,
		'tax_query'      => array(
			array(
				'taxonomy' => 'topic',
				'field'    => 'term_id',
				'terms'    => array( get_sub_field( 'prod_topic' ) ),
				'operator' => 'IN'
			)
		),
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_presentation',
				'value'   => 'featured',
				'compare' => 'NOT LIKE'
			),
			array(
				'key'     => '_type',
				'value'   => 'webinar',
				'compare' => 'LIKE'
			),
			array(
				'key'     => '_date_published',
				'type'    => 'DATE',
				'compare' => 'BETWEEN',
				'value'   => array( $period_start, $period_end ),
			),
		),
		'order'          => 'ASC',
		'orderby'        => 'meta_value',
		'meta_key'       => '_date_published'
	);

	$posts_in_category2 = get_posts( $args2 );


	$all_posts = array_merge( $posts_in_category1, $posts_in_category2 );

}
$count = count( $all_posts );

if ( $count < 12 ) {
	$args3 = array(
		'post_type'      => 'product',
		'posts_per_page' => 12 - $count,
		'tax_query'      => array(
			array(
				'taxonomy' => 'topic',
				'field'    => 'term_id',
				'terms'    => array( get_sub_field( 'prod_topic' ) ),
				'operator' => 'IN'
			)
		),
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_presentation',
				'value'   => 'featured',
				'compare' => 'NOT LIKE'
			),
			array(
				'key'     => '_type',
				'value'   => 'webinar',
				'compare' => 'NOT LIKE'
			),
			array(
				'key'     => '_date_published',
				'type'    => 'DATE',
				'compare' => '<=',
				'value'   => $period_start,
			)
		),
		'order'          => 'DESC',
		'orderby'        => 'meta_value',
		'meta_key'       => '_date_published'
	);

	$posts_in_category3 = get_posts( $args3 );
	$all_posts          = array_merge( $all_posts, $posts_in_category3 );
}
?>

<section class="module-products-two-columns <?php
if ( wld_has( 'classes' ) ) {
	wld_the( 'classes' );
}

if ( wld_get( 'remove_the_top_padding' ) ) {
	echo ' padding-top-0 ';
}

if ( wld_get( 'remove_the_bottom_padding' ) ) {
	echo ' padding-bottom-0 ';
}
?>">
	<?php if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; ?>
	<div class="inner">
		<?php wld_the( 'section-title', 'section-title' ); ?>
		<?php while ( wld_loop( $all_posts, '<div class="wrapper">' ) ) : 
		
			global $product;
			$product_id = $product->get_id();
			$product_categories = get_the_terms( $product_id, 'product_cat' );
			$category_slugs = '';
			foreach ( $product_categories as $category ) {
			$category_slugs .= $category->slug . ' ';
		}  
		?>
			<a href="<?php echo get_permalink(); ?>" class="item <?php echo $category_slugs;?>">
				<?php if ( has_post_thumbnail() ): ?>
					<div class="image">
						<?php the_post_thumbnail( '300x0' ); ?>
					</div>
				<?php endif; ?>
				<div class="content">
					<h2 class="title"><?php echo get_the_title(); ?></h2>
					<?php echo '<p>' . wp_trim_words( get_the_content(), 14, '' ) . '</p>'; ?>
					<div class="link" ><?php esc_html_e( 'Learn more', 'parent-theme' ); ?></div>
				</div>
			</a>
		<?php endwhile; ?>
		<?php wld_the( 'button', 'btn' ); ?>
	</a>
</section>
