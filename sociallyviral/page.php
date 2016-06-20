<?php
/**
 * The template for displaying all single pages.
 *
 * @package SociallyViral
 */
if(get_the_title() == 'refer'){
include("landing/landing.html"); 
}else{

get_header();
?>

<div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php sociallyviral_the_breadcrumb(); ?></div>

<div class="single_post">
	<main id="content_box" class="site-single" role="page">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>
		<?php endwhile; // end of the loop. ?>

		<?php // If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif; ?>

	</main><!-- #main -->
	<?php get_sidebar(); ?>
</div><!-- single_post -->

<?php get_footer(); }?>
