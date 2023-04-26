<?php
$url   = rawurlencode( get_the_permalink() );
$title = rawurlencode( get_the_title() );

// todo: Twitter URL
// https://twitter.com/share?text=<?php echo $title; ? >&amp;url=<?php echo $url; ? >
?>
<div class="share-enjoy-box">
	<?php wld_the( 'share_title', '<span>' ); ?>
	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" class="link-share">
		<img src="<?php echo esc_url( get_theme_file_uri( 'images/facebook-icon.svg' ) ); ?>" alt="Facebook">
	</a>
	<a href="https://twitter.com/share?text=<?php echo $title; ?>&amp;url=<?php echo $url; ?>" class="link-share">
		<img src="<?php echo esc_url( get_theme_file_uri( 'images/twitter-icon.svg' ) ); ?>" alt="Twitter">
	</a>
	<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&amp;title=<?php echo $title; ?>"
	   class="link-share">
		<img src="<?php echo esc_url( get_theme_file_uri( 'images/linkedin-icon.svg' ) ); ?>" alt="LinkedIn">
	</a>
</div>
