<?php echo '</main>'; ?>
<footer class="footer">
	<div class="footer-top">
		<div class="inner">
			<div class="wrapper">
				<?php wld_the_logo( 'wld_footer_logo' ); ?>
				<?php wld_the_nav( 'Footer Main' ); ?>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="inner">
			<div class="copyright">
				<?php wld_the( 'wld_footer_copyright' ); ?>
			</div>
			<?php wld_the( 'wld_footer_social_links' ); ?>
			<div class="by">
				<?php wld_the_by(); ?>
			</div>
		</div>
	</div>
</footer>
<?php echo '</div></div>'; ?>
