<?php
$url   = rawurlencode( get_the_permalink() );
$title = rawurlencode( get_the_title() );

// todo: Twitter URL
// https://twitter.com/share?text=<?php echo $title; ? >&amp;url=<?php echo $url; ? >
?>
<div class="share-this">
	<?php wld_the( 'wld_blog_share_title', 'title' ); ?>
	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>">
		<img src="<?php echo esc_url( get_theme_file_uri( 'images/facebook.svg' ) ); ?>" alt="Facebook">
	</a>
	<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&amp;title=<?php echo $title; ?>">
		<img src="<?php echo esc_url( get_theme_file_uri( 'images/linkedin.svg' ) ); ?>" alt="LinkedIn">
	</a>
</div>
