<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package SociallyViral
 */

get_header(); ?>

	<section class="no-results not-found">
		<header class="page-header">
			<h1 class="big"><?php _e( '404!', 'sociallyviral' ); ?></h1>
			<h1 class="page-title"><?php _e( 'Oops! That page can\'t be found.', 'sociallyviral' ); ?></h1>
		</header><!-- .page-header -->

		<div class="page-content">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

				<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'sociallyviral' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'sociallyviral' ); ?></p>
				<?php get_search_form(); ?>

			<?php else : ?>

				<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'sociallyviral' ); ?></p>
				<?php get_search_form(); ?>

			<?php endif; ?>
		</div><!-- .page-content -->
	</section><!-- .no-results -->

<?php get_footer(); ?>
