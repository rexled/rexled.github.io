jQuery(document).ready(function () {
    "use strict";
    $ = jQuery.noConflict();

    $("select.orderby").select2({
        allowClear: true,
        minimumResultsForSearch: Infinity
    });

    if ((jQuery().isotope )) {

        // blog masonry

        var $shopContainer = $('.main-prodlist'), // object that will keep track of options
            isotopeOptions = {}, // defaults, used if not explicitly set in hash
            defaultOptions = {
                itemSelector: 'li.product',
                // set columnWidth to a percentage of container width
                masonry: { }
            };

        // set up Isotope
        $shopContainer.isotope(defaultOptions, function () {

            // fix for height dynamic content
            setTimeout(function () {
                $shopContainer.isotope('reLayout');
            }, 1000);

        });
    }

    if(($.flexslider)){
        // The slider being synced must be initialized first
        $('.flexslider.woo_flexslider_thumbs').flexslider({
            animation: "slide",
            direction: "horizontal",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: 78,
            itemHeight: 110,
            itemMargin: 2,
            prevText: "",
            nextText: "",
            asNavFor: '.flexslider.woo_flexslider'
        });
        $('.flexslider.woo_flexslider').flexslider({
            animation: "slide",
            direction: "vertical",
            easing: "easeOutBounce",
            controlNav: false,
            directionNav: false,
            animationLoop: false,
            slideshow: false,
            touch: false,
            sync: ".flexslider.woo_flexslider_thumbs"
        });
    }
})