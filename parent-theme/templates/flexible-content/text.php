<?php
$title = get_sub_field( 'title' );
$text  = get_sub_field( 'text' );
$class = get_sub_field( 'columns' ); // Use field "Button Group": one-col two-cols three-cols
?>
<section class="text-section <?php echo $class; ?>">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<div class="text-content"><?php echo $text; ?></div>
	</div>
</section>
