<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package SociallyViral
 */
?>

	</div><!-- #content -->
</div><!-- .main-container -->
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info container">
			<div class="copyrights">
				<a href="<?php /*echo esc_url( __( 'http://wordpress.org/', 'sociallyviral' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'sociallyviral' ), 'WordPress' ); */?></a>
				<span class="sep">  </span>
				<?php /* printf( __( '%1$s Theme by %2$s.', 'sociallyviral' ), 'SociallyViral', '<a href="https://mythemeshop.com" rel="designer">MyThemeShop</a>' ); */ ?>
			</div>
			<a href="#blog" class="toplink" rel="nofollow"><i class="icon-angle-up"></i></a>
			<nav id="navigation" class="footer-navigation" role="navigation">
				<?php /* wp_nav_menu( array( 'theme_location' => 'footer' ) ); */ ?>
			</nav><!-- #site-navigation -->
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>