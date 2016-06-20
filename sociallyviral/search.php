<?php
/**
 * The template for displaying search results pages.
 *
 * @package SociallyViral
 */

get_header(); ?>
	<div id="primary" class="search-area">
		<main id="main" class="site-main" role="main">
			<header class="page-header">
				<h1 class="page-title"><?php _e("Search Results for:", 'sociallyviral' ); ?></span> <?php the_search_query(); ?></h1>
			</header><!-- .page-header -->
			<?php if ( have_posts() ) : 
				while ( have_posts() ) : the_post();
            ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('latestpost post'); ?>>
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
						<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
						<div class="entry-meta post-info">
                            <span class="thetime updated"><i class="demo-icon icon-calendar"></i> <span itemprop="datePublished"><?php the_time( get_option( 'date_format' ) ); ?></span></span>
                            <span class="thecategory"><i class="demo-icon icon-tags"></i> <?php the_category(', ') ?></span>
						</div><!-- .entry-meta -->
					</header><!-- .entry-header -->
				</article><!-- #post-## -->
			<?php endwhile; ?>
			<?php sociallyviral_post_navigation(); ?>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
