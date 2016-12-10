jQuery(document).ready(function ($) {

    //select2
    function handleSelect() {
        if ($(window).innerWidth() > 1024 ) {

            $("select.orderby, .variations select").select2({
                //placeholder: "Select a State",
                allowClear: true,
                minimumResultsForSearch: Infinity
            });

            //$(".big-select input").prop("readonly",true);
        }
    }

    handleSelect();

    $('.variations').on("click", ".reset_variations", function(){
        $('.big-select').select2("val", "");
    });

    $('.variations_form').on('found_variation', function () {
        // parse price woocommerce
        jQuery('.single_variation span.wc_price').each(function(){
            var $this = $(this);
            $this.html($this.html().replace('.','<span>.')+'</span>');
        })
    })
});