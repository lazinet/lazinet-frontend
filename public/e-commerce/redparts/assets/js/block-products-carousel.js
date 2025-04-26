( function( $ ) {
	'use strict';

	const isRtl = ( function() {
		let direction = null;

		return function() {
			if ( null === direction ) {
				direction = getComputedStyle( document.body ).direction;
			}

			return 'rtl' === direction;
		};
	}() );

	const options = {
		dots: false,
		margin: 20,
		loop: false,
		rtl: isRtl(),
	};
	const layoutOptions = {
		'grid-4': {
			items: 4,
		},
		'grid-4-sidebar': {
			items: 4,
			responsive: {
				1400: { items: 4 },
				1200: { items: 3 },
				992: { items: 3, margin: 16 },
				768: { items: 3, margin: 16 },
				576: { items: 2, margin: 16 },
				460: { items: 2, margin: 16 },
				0: { items: 1 },
			},
		},
		'grid-5': {
			items: 5,
			responsive: {
				1400: { items: 5 },
				1200: { items: 4 },
				992: { items: 4, margin: 16 },
				768: { items: 3, margin: 16 },
				576: { items: 2, margin: 16 },
				460: { items: 2, margin: 16 },
				0: { items: 1 },
			},
		},
		'grid-6': {
			items: 6,
			margin: 16,
			responsive: {
				1400: { items: 6 },
				1200: { items: 4 },
				992: { items: 4, margin: 16 },
				768: { items: 3, margin: 16 },
				576: { items: 2, margin: 16 },
				460: { items: 2, margin: 16 },
				0: { items: 1 },
			},
		},
		horizontal: {
			items: 4,
			responsive: {
				1400: { items: 4, margin: 14 },
				992: { items: 3, margin: 14 },
				768: { items: 2, margin: 14 },
				0: { items: 1, margin: 14 },
			},
		},
		'horizontal-sidebar': {
			items: 3,
			responsive: {
				1400: { items: 3, margin: 14 },
				768: { items: 2, margin: 14 },
				0: { items: 1, margin: 14 },
			},
		},
	};

	$( '.th-block-products-carousel' ).each( function() {
		const block = $( this );
		const layout = block.data( 'layout' );
		const owlCarousel = block.find( '.owl-carousel' );
		const owlOptions = $.extend( {}, options, layoutOptions[ layout ] );

		owlCarousel.on( 'initialized.owl.carousel', function() {
			owlCarousel.find( '.owl-item.cloned .th-product-card' ).trigger( 'th-product-card.init' );
		} );

		owlCarousel.owlCarousel( owlOptions );

		block.find( '.th-section-header__arrow--prev' ).on( 'click', function() {
			owlCarousel.trigger( 'prev.owl.carousel', [ 500 ] );
		} );
		block.find( '.th-section-header__arrow--next' ).on( 'click', function() {
			owlCarousel.trigger( 'next.owl.carousel', [ 500 ] );
		} );

		function setCurrentSlidesToShow( slidesToShow ) {
			if ( slidesToShow >= owlCarousel.find( '.owl-item:not(.cloned)' ).length ) {
				block.find( '.th-section-header' ).addClass( 'th-section-header--hide-arrows' );
			} else {
				block.find( '.th-section-header' ).removeClass( 'th-section-header--hide-arrows' );
			}
		}

		if ( owlOptions.responsive && Object.keys( owlOptions.responsive ).length ) {
			const breakpoints = Object.keys( owlOptions.responsive ).map( function( item ) {
				return parseFloat( item );
			} ).sort( function( a, b ) {
				return a - b;
			} );

			function createMedia( query, slidesToShow ) {
				const media = matchMedia( query );

				const onChange = function() {
					const { matches } = media;

					if ( matches && slidesToShow ) {
						setCurrentSlidesToShow( slidesToShow );
					}
				};

				if ( media.addEventListener ) {
					media.addEventListener( 'change', onChange );
				} else {
					media.addListener( onChange );
				}

				onChange();
			}

			breakpoints.forEach( function( breakpoint, idx ) {
				const nextBreakpoint = breakpoints[ idx + 1 ];

				const query = [
					'(min-width: ' + breakpoint + 'px)',
				];

				if ( nextBreakpoint ) {
					query.push( '(max-width: ' + ( nextBreakpoint - 0.02 ).toFixed( 2 ) + 'px)' );
				}

				createMedia( query.join( ' and ' ), owlOptions.responsive[ breakpoint ].items );
			} );
		}
	} );
}( jQuery ) );
