;(function ($) {
jQuery(document).on('ready', function () {
"use strict";

	if( related_upsells_data.enable_related_slider === '1' ){
		
	    var productCarousel = $('.related ul.products');
        var productCarousel_P = productCarousel.find('> li');

        if (productCarousel_P.length > 1) {
            productCarousel.owlCarousel({
                smartSpeed     : 1000,
                loop           : true,
                nav            : true,
                center         : false,
                dots           : false,
                navText        : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                autoplay       : true,
                autoplayTimeout: 3000,
                margin         : 30,
                responsiveClass: true,
                responsive     : {
                    0    : {
                        items: 1
                    },
                    600  : {
                        items: 1
                    },
                    1000 : {
                        items: 3
                    },
                    1200 : {
                        items: 4
                    }
                }
            });
        }
	}

	if( related_upsells_data.enable_upsells_slider === '1' ){

	    var productCarousel = $('.up-sells.upsells ul.products');
        var productCarousel_P = productCarousel.find('> li');

        if (productCarousel_P.length > 1) {
            productCarousel.owlCarousel({
                smartSpeed     : 1000,
                loop           : true,
                nav            : true,
                center         : false,
                dots           : false,
                navText        : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                autoplay       : true,
                autoplayTimeout: 3000,
                margin         : 30,
                responsiveClass: true,
                responsive     : {
                    0    : {
                        items: 1
                    },
                    600  : {
                        items: 1
                    },
                    1000 : {
                        items: 3
                    },
                    1200 : {
                        items: 4        
                    }
                }
            });
        }
	}

	
});
})(jQuery);
