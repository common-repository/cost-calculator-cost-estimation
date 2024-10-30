(function($) {
    "use strict";
    $( document ).ready( function () { 
         $("body").on("calculation_form_success",".calculation-form", function(e) { 
            ga('send', 'event', 'Contact Form', 'submit');
         })
    })
})(jQuery);