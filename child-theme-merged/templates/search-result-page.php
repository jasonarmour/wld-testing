<?php
/*
Template Name: Search Results Page
*/
?>
<?php get_header(); ?>

<?php

$topics_filters       = null;
$focuses_filters      = null;
$present_filters      = null;
$presentation_filters = null;
$date_filters         = null;

if ( isset( $_GET['topic'] ) ) {
	$topics_filters = $_GET['topic'];
}

if ( isset( $_GET['focus'] ) ) {
	$focuses_filters = $_GET['focus'];
}

if ( isset( $_GET['type'] ) ) {
	$present_filters = $_GET['type'];
}

if ( isset( $_GET['period_type'] ) ) {
	$date_filters = $_GET['period_type'];
}

if ( isset( $_GET['presentation'] ) ) {
	$presentation_filters = $_GET['presentation'];
}

wp_enqueue_style( 'daterangepicker' );
wp_enqueue_script( 'daterangepicker' );


global $wp_query;

$terms_topic = get_terms(
	array(
		'taxonomy'   => 'topic',
		'hide_empty' => true,
	)
);

$terms_focus = get_terms(
	array(
		'taxonomy'   => 'focus',
		'hide_empty' => true,
	)
);

if ( wld_get( 'wld_search_results_page_filters_products_per_page' ) ) {
	$post_per_page = wld_get( 'wld_search_results_page_filters_products_per_page' );
} else {
	$post_per_page = 14;
}

$products_list = WLD_WC_BI_Product_Filters::get_products_query( 1, $_GET );
?>
<section class="search-results-page">
	<div class="inner">
		<div class="wrapper">
			<aside class="filters">
				<div class="filters-wrapper">
					<?php wld_the( 'wld_search_results_page_filters_title', 'title' ); ?>
					<button class="close"></button>
					<form id="products-filters">
						<div class="filters-block">
							<h3 class="sub-title"><?php esc_html_e( 'Topic', 'parent-theme' ); ?></h3>
							<?php if ( $terms_topic ) : ?>
								<?php foreach ( $terms_topic as $term ) : ?>
									<div class="form-row">
										<input id="<?php echo $term->slug; ?>" name="topic[]"
											   value="<?php echo $term->slug; ?>" type="checkbox"
											<?php
											if ( is_array( $topics_filters ) && in_array( $term->slug, $topics_filters, true ) ) {
												echo 'checked';
											}
											?>
										>
										<label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
						<div class="filters-block">
							<h3 class="sub-title"><?php esc_html_e( 'Focus', 'parent-theme' ); ?></h3>
							<?php if ( $terms_focus ) : ?>
								<?php foreach ( $terms_focus as $term ) : ?>
									<div class="form-row">
										<input id="<?php echo $term->slug; ?>" name="focus[]"
											   value="<?php echo $term->slug; ?>" type="checkbox"
											<?php
											if ( is_array( $focuses_filters ) && in_array( $term->slug, $focuses_filters, true ) ) {
												echo 'checked';
											}
											?>
										>
										<label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
						<?php
						$show = array(
							'type' => true,
						);
						while ( wld_loop( 'wld_meta_attributes' ) ) {
							$label = wld_get( 'name' );
							$name  = sanitize_title( $label );
							if ( isset( $show[ $name ] ) ) {

								$choices = wld_get( 'options' );

								$choices = acf_decode_choices( $choices );

								echo '<div class="filters-block">';

								printf(
									'<h3 class="sub-title">%s</h3>',
									esc_html( $label )
								);

								foreach ( $choices as $key => $choice ) {
									$is_check = '';
									if ( is_array( $present_filters ) && in_array( $key, $present_filters, true ) ) {
										$is_check = "checked";
									}

									printf(
										'
											<div class="form-row">
												<input id="%1$s-%3$s" value="%3$s" name="%1$s[]" type="checkbox" %4$s>
												<label for="%1$s-%3$s">%2$s</label>
											</div>
											',
										esc_attr( $name ),
										esc_html( $choice ),
										esc_attr( $key ),
										$is_check
									);
								}

								echo '</div>';

							}
						} ?>
						<div class="filters-block">
							<h3 class="sub-title"><?php esc_html_e( 'Date', 'parent-theme' ); ?></h3>
							<div class="form-row">
								<input type="radio" id="upcoming" name="period_type" value="1"
									<?php
									if ( '1' === $date_filters ) {
										echo "checked";
									}
									?>
								>
								<label
									for="upcoming"><?php esc_html_e( 'Upcoming', 'parent-theme' ); ?></label>
							</div>
							<div class="form-row">
								<input type="radio" id="past_week" name="period_type" value="2"
									<?php
									if ( '2' === $date_filters ) {
										echo "checked";
									}
									?>
								>
								<label
									for="past_week"><?php esc_html_e( 'Past Week', 'parent-theme' ); ?></label>
							</div>
							<div class="form-row">
								<input type="radio" id="past_month" name="period_type" value="3"
									<?php
									if ( '3' === $date_filters ) {
										echo "checked";
									}
									?>
								>
								<label
									for="past_month"><?php esc_html_e( 'Past Month', 'parent-theme' ); ?></label>
							</div>
							<div class="form-row">
								<input type="radio" id="past_year" name="period_type" value="4"
									<?php
									if ( '4' === $date_filters ) {
										echo "checked";
									}
									?>
								>
								<label
									for="past_year"><?php esc_html_e( 'Past Year', 'parent-theme' ); ?></label>
							</div>
							<div class="form-row">
								<input type="radio" id="custom_range" name="period_type" value="">
								<label
									for="custom_range"><?php esc_html_e( 'Custom Range', 'parent-theme' ); ?></label>
							</div>
						</div>
						<?php
						while ( wld_loop( 'wld_meta_attributes' ) ) {
							$label   = wld_get( 'name' );

							if ( 'presentation' !== $label ) {
								continue;
							}

							$name    = sanitize_title( $label );
							$choices = wld_get( 'options' );
							$choices = acf_decode_choices( $choices );

								echo '<div class="filters-block">';

								printf(
									'<h3 class="sub-title">%s</h3>',
									esc_html( $label )
								);

								foreach ( $choices as $key => $choice ) {
									$is_check = '';
									if ( is_array( $presentation_filters ) && in_array( $key, $presentation_filters, true ) ) {
										$is_check = "checked";
									}

									printf(
										'
										<div class="form-row">
											<input id="%1$s-%3$s" value="%3$s" name="%1$s[]" type="checkbox" %4$s>
											<label for="%1$s-%3$s">%2$s</label>
										</div>
										',
										esc_attr( $name ),
										esc_html( $choice ),
										esc_attr( $key ),
										$is_check
									);
								}

								echo '</div>';
						} ?>
					</form>
				</div>
			</aside>
			<section class="products module-products-two-columns">
				<button id="open-filters" class="filters-btn"><?php esc_html_e( 'Filter', 'parent-theme' ); ?></button>
				<?php if ( $products_list->have_posts() ): ?>
					<div class="wrapper">
						<?php while ( $products_list->have_posts() )  : ?>
					   
							
						
							<?php $products_list->the_post(); ?>
						
						
							
							<?php get_template_part( 'templates/template-part/filter-product' ); ?>
						<?php endwhile; ?>
					</div>
				<?php else: ?>
					<div class="wrapper"><span>Products not found</span></div>
				<?php endif; ?>

				<?php wp_reset_postdata(); ?>
				<?php
				if ( $products_list->max_num_pages > 1 ) {
					echo '<a href="#" id="see-more" class="btn" data-page="1" data-max_page="' . $products_list->max_num_pages . '" data-product-per-page="' . $post_per_page . '">' . esc_html__( 'See more', 'parent-theme' ) . '</a>';
				} else {
					echo '<a href="#" id="see-more" class="btn" style="display:none" data-page="1" data-max_page="' . $products_list->max_num_pages . '" data-product-per-page="' . $post_per_page . '">' . esc_html__( 'See more', 'parent-theme' ) . '</a>';
				}
				?>
			</section>
		</div>
	</div>
</section>

<?php get_footer(); ?>
