<?php
$title = get_sub_field( 'title' );
$image = wld_get_sub_image( 'image', '500x0' );
$text  = get_sub_field( 'text' );
$class = get_sub_field( 'image_align' ); // Use field "Button Group": image-left image-right
?>
<section class="text-image-section <?php echo $class; ?>">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<div class="wrap">
			<div class="image"><?php echo $image; ?></div>
			<div class="text-content"><?php echo $text; ?></div>
		</div>
	</div>
</section>
