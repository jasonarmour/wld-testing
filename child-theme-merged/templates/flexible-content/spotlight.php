<?php 
$product_type = wld_get('product_type');
$focus = wld_get('focus');
$tags = wld_get('tags');

// Query all products that have the indicated product type, focus, and tag
$args = array(
    'post_type' => 'product',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'wld_product_cat',
            'field'    => 'slug',
            'terms'    => $product_type,
        ),
        array(
            'taxonomy' => 'wld_focus',
            'field'    => 'slug',
            'terms'    => $focus,
        ),
        array(
            'taxonomy' => 'wld_product_tag',
            'field'    => 'slug',
            'terms'    => $tags,
        ),
    ),
);
$query = new WP_Query($args);
?>

<section class="section-featured-content custom-html background-<?php echo wld_get('background_color'); ?> padding-<?php echo wld_get('padding'); ?> <?php echo wld_get('custom_class'); ?>">
	<div class="inner">
		<div class="wrapper">
			Spotlight:
			<?php if ($product_type): ?>
				<?php $get_terms_args = array(
					'taxonomy' => 'wld_product_cat',
					'hide_empty' => 0,
					'include' => $product_type,
				); ?>
				<?php $terms = get_terms($get_terms_args); ?>
				<?php if ($terms): ?>
					<?php foreach ($terms as $term): ?>
						<a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php wld_the('button', 'btn'); ?>
	</div>
</section>

<?php if ($query->have_posts()): ?>
	<?php while ($query->have_posts()): $query->the_post(); ?>
		<?php // Output the products here ?>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>
