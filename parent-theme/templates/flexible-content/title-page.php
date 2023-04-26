<?php
$pre_title = get_sub_field( 'pre_title' );
$title     = get_sub_field( 'title' );
$subtitle  = get_sub_field( 'subtitle' );
$text      = get_sub_field( 'text' );
?>
<section class="title-page-section">
	<div class="inner">
		<div class="pretitle"><?php echo $pre_title; ?></div>
		<?php wld_the_title( $title, 'section-title' ); ?>
		<?php wld_the_title( $subtitle, 'subtitle' ); ?>
		<div class="text-content"><?php echo $text; ?></div>
	</div>
</section>
