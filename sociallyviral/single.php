<?php
/**
 * The template for displaying all single posts.
 *
 * @package SociallyViral
 */


get_header(); ?>

<div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php sociallyviral_the_breadcrumb(); ?></div>
	<div class="single_post">
		<main id="content_box" class="site-single" role="single">

<!--added stuff-->
<?php

include("custom.php");
?>

<a  style="float:right;" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>?refid=<?php echo $current_user->ID; ?>&amp;t=<?php the_title(); ?>&amp;refid=<?php echo $current_user->ID; ?>"  title="Share on Facebook." ><button class="mysharebtn">Share on Facebook</button></a>
<!---->
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'single' );
			?>
			<?php endwhile; // end of the loop. ?>
			
			<div class="post-tags"><?php the_tags(); ?></div>

			<!-- Related Posts -->
			<div class="related-posts">
				<h4><?php _e('You may also like', 'sociallyviral') ?></h4>
				<?php global $post;
				$thePostID = $post->ID;
				$cats = get_the_category($thePostID);
				$cat_ids = array();
				foreach($cats as $individual_cat)
					$cat_ids[] = $individual_cat->cat_ID;
					$posts = new WP_Query( 
						array(
							'category__in' => $cat_ids,
							'post__not_in' => array($thePostID),
							'posts_per_page' => 6, 
							'orderby' => 'rand'
							)
						);
					while ( $posts->have_posts() ) { $posts->the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('latestpost'); ?>>
							<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" id="featured-thumbnail">
		    				    <?php echo '<div class="featured-thumbnail">';
		    				    	if ( has_post_thumbnail() ) {
											the_post_thumbnail('sociallyviral_related', array('title' => ''));
										} else { ?>
											<img src="<?php echo sociallyviral_get_thumbnail_url('sociallyviral_related'); ?>" alt="<?php the_title(); ?>" />
									<?php }
		    				    echo '</div>'; ?>
		    				</a>
							<header class="entry-header">
								<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
							</header><!-- .entry-header -->
						</article><!-- #post-## -->
				<?php } wp_reset_postdata(); ?>
			</div>

			<!-- Author Box -->
			<div class="postauthor">
				<h4><?php _e('About The Author', 'sociallyviral' ); ?></h4>
				<div class="author-wrap">
					<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '160' );  } ?>
					<h5 class="vcard"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="nofollow" class="fn"><?php the_author_meta( 'nickname' ); ?></a></h5>
					<p><?php the_author_meta('description') ?></p>
				</div>
			</div>

			<?php // If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; ?>

		</main><!-- #main -->
	<?php get_sidebar(); ?>
	</div><!-- single_post -->

<?
			
php get_footer(); ?>
