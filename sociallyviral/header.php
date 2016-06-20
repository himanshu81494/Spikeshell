<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package SociallyViral
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--iOS/android/handheld specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta itemprop="name" content="<?php bloginfo( 'name' ); ?>" />
	<meta itemprop="url" content="<?php echo site_url(); ?>" />
	<?php if ( is_singular() ) { ?>
	<meta itemprop="creator accountablePerson" content="<?php $user_info = get_userdata($post->post_author); echo $user_info->first_name.' '.$user_info->last_name; ?>" />
	<?php }
	wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<div class="main-container">
			<header id="masthead" class="site-header" role="banner">
				<div class="site-branding">
					<?php $header_image = get_header_image(); 
					if (!empty($header_image)) { ?>
						<?php if( is_front_page() || is_home() || is_404() ) { ?>
							<h1 id="logo" class="image-logo" itemprop="headline">
								<a href="<?php echo esc_url(home_url()); ?>"><img src="<?php header_image(); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
							</h1><!-- END #logo -->
						<?php } else { ?>
						    <h2 id="logo" class="image-logo" itemprop="headline">
								<a href="<?php echo esc_url(home_url()); ?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
							</h2><!-- END #logo -->
						<?php } ?>
					<?php } else { ?>
						<?php if( is_front_page() || is_home() || is_404() ) { ?>
							<h1 id="logo" class="text-logo" itemprop="headline">
								<a href="<?php echo esc_url(home_url()); ?>"><?php bloginfo( 'name' ); ?></a>
							</h1><!-- END #logo -->
						<?php } else { ?>
						    <h2 id="logo" class="text-logo" itemprop="headline">
								<a href="<?php echo esc_url(home_url()); ?>"><?php bloginfo( 'name' ); ?></a>
							</h2><!-- END #logo -->
						<?php } ?>
					<?php } ?>
</div><!-- .site-branding -->
<div style='float:right' class='topbtn'>
<?php
/*wp_loginout();*/ 
if(is_user_logged_in())
 wp_loginout();
?>
</div>

<?php /* echo "<a href='http://www.spikeshell.in/wp-login.php?action=register'>"; */ ?>
<!-- <div style='float:right' class='topbtn'>
Register
</div> -->
<?php echo "</a>"; ?>

				<?php $headerSearchEnable = get_theme_mod('sociallyviral_header_search');
				if( $headerSearchEnable == 1 ) { ?>
					<div class="header-search"><?php get_search_form( ); ?></div>
				<?php } ?>

				<div class="header-social">
					<?php 
					$sociallyviral_fb_url = get_theme_mod('sociallyviral_header_facebook');
					$sociallyviral_twt_url = get_theme_mod('sociallyviral_header_twitter');
					$sociallyviral_gp_url = get_theme_mod('sociallyviral_header_google_plus');
					$sociallyviral_yt_url = get_theme_mod('sociallyviral_header_youtube');

					if(!empty($sociallyviral_fb_url)) { ?>
						<a href="<?php echo esc_url(get_theme_mod('sociallyviral_header_facebook')); ?>" class="header-facebook" style="background: #375593">
							<i class="icon-facebook"></i>
						</a>
					<?php } 

					if(!empty($sociallyviral_twt_url)) { ?>
						<a href="<?php echo esc_url(get_theme_mod('sociallyviral_header_twitter')); ?>" class="header-twitter">
							<i class="icon-twitter"></i>
						</a>/
					<?php }

					if(!empty($sociallyviral_gp_url)) { ?>
						<a href="<?php echo esc_url(get_theme_mod('sociallyviral_header_google_plus')); ?>" class="header-google-plus">
							<i class="icon-google-plus"></i>
						</a>
					<?php }

					if(!empty($sociallyviral_yt_url)) { ?>
						<a href="<?php echo esc_url(get_theme_mod('sociallyviral_header_youtube')); ?>" class="header-youtube-play">
							<i class="icon-youtube-play"></i>
						</a>
					<?php } ?>
				</div>

				<a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu', 'sociallyviral'); ?></a>
				<nav id="navigation" class="primary-navigation mobile-menu-wrapper" role="navigation">
					<?php /*if(get_the_title() != 'home' || is_user_logged_in())*/ if ( has_nav_menu( 'primary' ) ) { ?>
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu clearfix', 'container' => '' ) ); ?>
					<?php } else { ?>
						<ul class="menu clearfix">
							<?php wp_list_categories('title_li='); ?>
						</ul>
					<?php } ?>
				</nav><!-- #site-navigation -->
			</header><!-- #masthead -->

			<div id="content" class="site-content">

