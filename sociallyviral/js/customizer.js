/**
 * Theme Name: SociallyViral
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'sociallyviral_header_facebook', function( value ) {
		value.bind( function( to ) {
			$( '.header-facebook' ).text( to );
		} );
	} );
	wp.customize( 'sociallyviral_header_twitter', function( value ) {
		value.bind( function( to ) {
			$( '.header-twitter' ).text( to );
		} );
	} );
	wp.customize( 'sociallyviral_header_google_plus', function( value ) {
		value.bind( function( to ) {
			$( '.header-google-plus' ).text( to );
		} );
	} );
	wp.customize( 'sociallyviral_header_youtube', function( value ) {
		value.bind( function( to ) {
			$( '.header-youtube' ).text( to );
		} );
	} );
} )( jQuery );