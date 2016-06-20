<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package SociallyViral
 */

get_header(); ?>

	<div id="primary" class="content-archive">
		<main id="main" class="site-main" role="main">
            <header class="page-header">
				<?php
					sociallyviral_archive_title( '<h1 class="page-title">', '</h1>' );
					sociallyviral_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php if ( have_posts() ) : 
				$j = 0; // counter
				while ( have_posts() ) : the_post();
            ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('latestpost'); ?>>
					<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" id="featured-thumbnail">
    				    <?php echo '<div class="featured-thumbnail">';
    				    
    				    if ( has_post_thumbnail() ) {
								the_post_thumbnail('sociallyviral_featured', array('title' => ''));
							} else { ?>
								<img src="<?php echo sociallyviral_get_thumbnail_url('sociallyviral_featured'); ?>" alt="<?php the_title(); ?>" />
						<?php }
    				    echo '</div>'; ?>

    				</a>
					<header class="entry-header">

						<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
						<div class="entry-meta post-info">
                            <span class="thetime updated"><i class="demo-icon icon-calendar"></i> <span itemprop="datePublished"><?php the_time( get_option( 'date_format' ) ); ?></span></span>
                            <span class="thecategory"><i class="demo-icon icon-tags"></i> <?php the_category(', ') ?></span>
						</div><!-- .entry-meta -->
					</header><!-- .entry-header -->
<?php include("custom.php"); ?>
<a  style="float:right;" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>?refid=<?php echo $current_user->ID; ?>&amp;t=<?php the_title(); ?>&amp;refid=<?php echo $current_user->ID; ?>"  title="Share on Facebook." ><button class="mysharebtn">Share on Facebook</button></a>

				</article><!-- #post-## -->
			<?php endwhile; ?>
			<?php sociallyviral_post_navigation(); ?>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_footer(); ?>
