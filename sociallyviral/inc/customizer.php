<?php
/**
 * SociallyViral Theme Customizer
 *
 * @package SociallyViral
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function sociallyviral_customize_register( $wp_customize ) {

    /*---------------------
	* Theme Options
	----------------------*/
    $wp_customize->add_section( 'sociallyviral_theme_settings', array(
        'title'          => __('Theme Options','sociallyviral'),
        'priority'       => 121,
    ) );

    // Enable/Disable Search Box
    $wp_customize->add_setting( 'sociallyviral_header_search', array(
        'default' => '1',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control( 'sociallyviral_header_search', array(
        'label'   => __('Enable Search Box','sociallyviral'),
        'section' => 'sociallyviral_theme_settings',
        'type'           => 'checkbox',
        'transport'   => 'refresh',
    ) );

    //Header Social icons
    // facebook
    $wp_customize->add_setting( 'sociallyviral_header_facebook', array(
        'default' => '#',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control( 'sociallyviral_header_facebook', array(
        'label'   => __('Facebook','sociallyviral'),
        'section' => 'sociallyviral_theme_settings',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    // twitter
    $wp_customize->add_setting( 'sociallyviral_header_twitter', array(
        'default' => '#',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control( 'sociallyviral_header_twitter', array(
        'label'   => __('Twitter','sociallyviral'),
        'section' => 'sociallyviral_theme_settings',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    // google plus
    $wp_customize->add_setting( 'sociallyviral_header_google_plus', array(
        'default' => '#',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control( 'sociallyviral_header_google_plus', array(
        'label'   => __('Google Plus','sociallyviral'),
        'section' => 'sociallyviral_theme_settings',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    // youtube
    $wp_customize->add_setting( 'sociallyviral_header_youtube', array(
        'default' => '#',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control( 'sociallyviral_header_youtube', array(
        'label'   => __('YouTube','sociallyviral'),
        'section' => 'sociallyviral_theme_settings',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

	// pagination
	$wp_customize->add_setting( 'sociallyviral_pagination_position', array(
        'default' => '1',
        'sanitize_callback' == 'esc_url_raw'
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'sociallyviral_pagination_position',
            array(
                'label'          => __('Pagination Type', 'sociallyviral'),
                'section'        => 'sociallyviral_theme_settings',
                'settings'       => 'sociallyviral_pagination_position',
                'type'           => 'radio',
                'choices'    => array(
                    '0'   => __('Next/Previous', 'sociallyviral'),
                    '1'  => __('Numbered', 'sociallyviral'),
                    ),
                'transport'   => 'refresh',
            )
        )
    );
}
add_action( 'customize_register', 'sociallyviral_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sociallyviral_customize_preview_js() {
	wp_enqueue_script( 'sociallyviral_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'sociallyviral_customize_preview_js' );