jQuery(document).ready(function ($) {
    var $form = $('.variations_form');
    $form.on('found_variation', function (event, variation) {
        fixMainImage();
        var listItem = $('.woo_flexslider img[src="' + variation.image_src + '"]').closest('li');
        var index = $('.woo_flexslider li').index(listItem);

        if(index!=-1){
            $('.woo_flexslider_thumbs').flexslider(index);
            $('.woo_flexslider').flexslider(index);
        }
    });

    function fixMainImage() {

        $('div.images img[data-o_src]').each(function () {
            var $i = $(this);
            $i.attr('src', $i.attr('data-o_src'));
        });
        $('div.images a[data-o_href]').each(function () {
            var $i = $(this);
            $i.attr('href', $i.attr('data-o_href'));
        });
    }

    fixMainImage();
});