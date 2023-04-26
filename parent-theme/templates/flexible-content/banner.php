<?php
$bg       = wld_get_sub_bg( 'background' );
$title    = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$text     = get_sub_field( 'text' );
?>
<section class="banner-section" <?php echo $bg; ?>>
	<div class="overlay"></div>
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<?php wld_the_title( $subtitle, 'subtitle' ); ?>
		<div class="text-content"><?php echo $text; ?></div>
	</div>
</section>
