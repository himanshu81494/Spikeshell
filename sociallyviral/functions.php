<?php
///
function my_shortcode( $atts ) {
	if (filter_var($atts['link'], FILTER_VALIDATE_URL) === FALSE) {
    //die('Not a valid URL'.$atts['link']);
		header('Location: https://www.google.co.in/?q='.$atts['link']);
	}
	//return "link = {$atts['link']}";
	echo $atts['link'];
	header('Location: '.$atts['link']);
}
add_shortcode( 'letsrefer', 'my_shortcode' );
///
add_action('wp_logout',create_function('','wp_redirect(home_url());exit();'));

// show admin bar only for admins and editors
if (!current_user_can('edit_posts')) {
	add_filter('show_admin_bar', '__return_false');
//echo "roleeditted";
}
add_filter( 'login_url', 'my_login_page', 10, 3 );
function my_login_page( $login_url, $redirect, $force_reauth ) {
//    return home_url( '/wp_login/?redirect_to=' . $redirect );
    return home_url( '/wp-login?action=login' . $redirect );
}
// Display User IP in WordPress


function get_the_user_ip() {
if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return apply_filters( 'wpb_get_ip', $ip );
}

//add_shortcode('show_ip', 'get_the_user_ip');

/**
 * SociallyViral functions and definitions
 *
 * @package SociallyViral
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 750; /* pixels */
}

if ( ! function_exists( 'sociallyviral_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sociallyviral_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on SociallyViral, use a find and replace
	 * to change 'sociallyviral' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'sociallyviral', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 370, 297, true );
	add_image_size( 'sociallyviral_featured', 370, 297, true ); //featured
    add_image_size( 'sociallyviral_featuredbig', 770, 297, true); //featured big
	add_image_size( 'sociallyviral_related', 235, 190, true ); //related
	add_image_size( 'sociallyviral_widgetthumb', 75, 75, true ); //widget
    add_image_size( 'sociallyviral_widgetfull', 300, 172, true ); //sidebar full width

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Top Navigation Menu', 'sociallyviral' ),
		'footer' => __( 'Footer Menu', 'sociallyviral' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );


	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'sociallyviral_custom_background_args', array(
		'default-color' => 'F0EDE9',
		'default-image' => '',
	) ) );

    /* Display a admin notice */

    add_action('admin_notices', 'sociallyviral_admin_notice');
    function sociallyviral_admin_notice() {
        global $current_user ;
            $user_id = $current_user->ID;
            /* Check that the user hasn't already clicked to ignore the message */
        if ( ! get_user_meta($user_id, 'sociallyviral_ignore_notice') ) {
            echo '<div class="updated notice-info point-notice" style="position:relative;"><p>'; 
            printf(__('Like SociallyViral theme? You will <strong>LOVE SociallyViral Pro</strong>!','sociallyviral').'<a href="https://mythemeshop.com/themes/sociallyviral/?utm_source=SociallyViral+Free&utm_medium=Notification+Link&utm_content=SociallyViral+Pro+LP&utm_campaign=WordPressOrg" target="_blank">&nbsp;'.__('Click here for all the exciting features.','sociallyviral').'</a><a href="%1$s" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>', '?sociallyviral_notice_ignore=0');
            echo "</p></div>";
        }
    }

    add_action('admin_init', 'sociallyviral_notice_ignore');
    function sociallyviral_notice_ignore() {
        global $current_user;
            $user_id = $current_user->ID;
            /* If user clicks to ignore the notice, add that to their user meta */
            if ( isset($_GET['sociallyviral_notice_ignore']) && '0' == $_GET['sociallyviral_notice_ignore'] ) {
                add_user_meta($user_id, 'sociallyviral_ignore_notice', 'true', true);
        }
    }

    add_action( 'customize_controls_print_footer_scripts', 'sociallyviral_pro_banner' );
    function sociallyviral_pro_banner() {
        echo '<a href="https://mythemeshop.com/themes/sociallyviral/?utm_source=SociallyViral+Free&utm_medium=Banner+CPC&utm_content=SociallyViral+Pro+LP&utm_campaign=WordPressOrg" id="pro-banner" style="display: none; margin-top: 10px; background: #fff;" target="_blank"><img src="'.get_template_directory_uri().'/images/sociallyviral-pro.jpg" /></a>';
        echo '<script type="text/javascript">jQuery(document).ready(function($) { $("#pro-banner").appendTo("#customize-info").css("display", "block"); });</script>';
    }
}
endif; // sociallyviral_setup
add_action( 'after_setup_theme', 'sociallyviral_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function sociallyviral_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'sociallyviral' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'sociallyviral_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function sociallyviral_scripts() {
	wp_enqueue_style( 'sociallyviral-style', get_stylesheet_uri() );
	wp_enqueue_script('jquery');

	wp_enqueue_script( 'sociallyvrial-customscripts', get_template_directory_uri() . '/js/customscripts.js', array(), '20120212', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sociallyviral_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load recommended plugins file.
 */
require get_template_directory() . '/inc/plugin-activation.php';


/*-----------------------------------------------------------------------------------*/
/*  Load Widgets, Actions and Libraries
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget
include_once( "functions/widget-ad125.php" );

// Add the 300x250 Ad Block Custom Widget
include_once( "functions/widget-ad300.php" );

// Add Recent Posts Widget
include_once( "functions/widget-recentposts.php" );


/*-----------------------------------------------------------------------------------*/
/*	Excerpt
/*-----------------------------------------------------------------------------------*/

// Increase max length
function sociallyviral_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'sociallyviral_excerpt_length', 20 );

// Remove [...] and shortcodes
function sociallyviral_custom_excerpt( $output ) {
  return preg_replace( '/\[[^\]]*]/', '', $output );
}
add_filter( 'get_the_excerpt', 'sociallyviral_custom_excerpt' );

// Truncate string to x letters/words
function sociallyviral_truncate( $str, $length = 40, $units = 'letters', $ellipsis = '&nbsp;&hellip;' ) {
    if ( $units == 'letters' ) {
        if ( mb_strlen( $str ) > $length ) {
            return mb_substr( $str, 0, $length ) . $ellipsis;
        } else {
            return $str;
        }
    } else {
        $words = explode( ' ', $str );
        if ( count( $words ) > $length ) {
            return implode( " ", array_slice( $words, 0, $length ) ) . $ellipsis;
        } else {
            return $str;
        }
    }
}

if ( ! function_exists( 'sociallyviral_excerpt' ) ) {
    function sociallyviral_excerpt( $limit = 40 ) {
      return esc_html( sociallyviral_truncate( get_the_excerpt(), $limit, 'words' ) );
    }
}

/*------------[ breadcrumb ]-------------*/
if (!function_exists('sociallyviral_the_breadcrumb')) {
    function sociallyviral_the_breadcrumb() {
        echo '<div typeof="v:Breadcrumb" class="root"><a rel="v:url" property="v:title" href="';
        echo home_url();
        echo '" rel="nofollow">'.sprintf( __( "Home", 'sociallyviral' ));
        echo '</a></div><div><i class="demo-icon icon-angle-double-right"></i></div>';
        if (is_single()) {
            $categories = get_the_category();
            if ( $categories ) {
                $level = 0;
                $hierarchy_arr = array();
                foreach ( $categories as $cat ) {
                    $anc = get_ancestors( $cat->term_id, 'category' );
                    $count_anc = count( $anc );
                    if (  0 < $count_anc && $level < $count_anc ) {
                        $level = $count_anc;
                        $hierarchy_arr = array_reverse( $anc );
                        array_push( $hierarchy_arr, $cat->term_id );
                    }
                }
                if ( empty( $hierarchy_arr ) ) {
                    $category = $categories[0];
                    echo '<div typeof="v:Breadcrumb"><a href="'. esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div><i class="demo-icon icon-angle-double-right"></i></div>';
                } else {
                    foreach ( $hierarchy_arr as $cat_id ) {
                        $category = get_term_by( 'id', $cat_id, 'category' );
                        echo '<div typeof="v:Breadcrumb"><a href="'. esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div><i class="demo-icon icon-angle-double-right"></i></div>';
                    }
                }
            }
            echo "<div><span>";
            the_title();
            echo "</span></div>";
        } elseif (is_page()) {
            global $post;
            if ( $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ( $parent_id ) {
                    $page = get_page( $parent_id );
                    $breadcrumbs[] = '<div typeof="v:Breadcrumb"><a href="'.esc_url( get_permalink( $page->ID ) ).'" rel="v:url" property="v:title">'.esc_html( get_the_title($page->ID) ). '</a></div><div><i class="demo-icon icon-angle-double-right"></i></div>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse( $breadcrumbs );
                foreach ( $breadcrumbs as $crumb ) { echo $crumb; }
            }
            echo "<div><span>";
            the_title();
            echo "</span></div>";
        } elseif (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $this_cat_id = $cat_obj->term_id;
            $hierarchy_arr = get_ancestors( $this_cat_id, 'category' );
            if ( $hierarchy_arr ) {
                $hierarchy_arr = array_reverse( $hierarchy_arr );
                foreach ( $hierarchy_arr as $cat_id ) {
                    $category = get_term_by( 'id', $cat_id, 'category' );
                    echo '<div typeof="v:Breadcrumb"><a href="'.esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div><i class="demo-icon icon-angle-double-right"></i></div>';
                }
            }
            echo "<div><span>";
            single_cat_title();
            echo "</span></div>";
        } elseif (is_author()) {
            echo "<div><span>";
            if(get_query_var('author_name')) :
                $curauth = get_user_by('slug', get_query_var('author_name'));
            else :
                $curauth = get_userdata(get_query_var('author'));
            endif;
            echo esc_html( $curauth->nickname );
            echo "</span></div>";
        } elseif (is_search()) {
            echo "<div><span>";
            the_search_query();
            echo "</span></div>";
        } elseif (is_tag()) {
            echo "<div><span>";
            single_tag_title();
            echo "</span></div>";
        }
    }
}

/*-----------------------------------------------------------------
* Google Fonts
-----------------------------------------------------------------*/
function sociallyviral_fonts_url() {
    $fonts_url = '';
 
    /* Translators: If there are characters in your language that are not
    * supported by Open Sans, translate this to 'off'. Do not translate
    * into your own language.
    */
    $roboto = _x( 'on', 'Roboto font: on or off', 'sociallyviral' );
 
    if ( 'off' !== $roboto ) {
        $font_families = array();
 
        if ( 'off' !== $roboto ) {
            $font_families[] = 'Roboto:400,700';
        }
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 
        $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
    }
 
    return $fonts_url;
}

function sociallyviral_scripts_styles() {
    wp_enqueue_style( 'theme-slug-fonts', sociallyviral_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'sociallyviral_scripts_styles' );

/*-----------------------------------------------------------------
* Default Thumbnails
-----------------------------------------------------------------*/
function sociallyviral_get_thumbnail_url( $size = 'full' ) {
    global $post;
    if (has_post_thumbnail( $post->ID ) ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
        return $image[0];
    }
    
    // use first attached image
    $images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );
    if (!empty($images)) {
        $image = reset($images);
        $image_data = wp_get_attachment_image_src( $image->ID, $size );
        return $image_data[0];
    }
        
    // use no preview fallback
    if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) )
        return get_template_directory_uri().'/images/nothumb-'.$size.'.png';
    else
        return '';
}
/*
add_action('set_current_user', 'cc_hide_admin_bar');
function cc_hide_admin_bar() {
  if (!current_user_can('edit_posts')) {
    show_admin_bar(false);
  }
}
*/
/*
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
function add_login_logout_link($items, $args) {
	ob_start();
	wp_loginout('index.php');
	$loginoutlink = ob_get_contents();
	ob_end_clean();
	$items .= '<li class="mysharebtn">'.$loginoutlink.'</li>';
	return $items;
}

*/
