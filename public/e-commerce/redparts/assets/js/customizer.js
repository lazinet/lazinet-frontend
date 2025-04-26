( function( $ ) {
	'use strict';

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	wp.customize( 'redparts_settings[mobile_header_logo]', function( value ) {
		value.bind( function( to ) {
			if ( to.url === '' ) {
				$( '.th-logo--mobile' ).removeClass( 'th-logo--has-mobile-image' );
			} else {
				$( '.th-logo--mobile' ).addClass( 'th-logo--has-mobile-image' );
			}
		} );
	} );
}( jQuery ) );
