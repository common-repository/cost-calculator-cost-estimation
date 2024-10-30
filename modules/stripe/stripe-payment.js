(function($) {
    "use strict";
    $( document ).ready( function () {
    	if( $('.card-element').length > 0 ){
    		var stripe = Stripe(calculation_stripe.key);
    		var elements = stripe.elements();
	    	var card = elements.create('card');
	    	card.mount('.card-element');
	    	card.addEventListener('change', function(event) {
			  if (event.error) {
			  	$(".card-errors").html(event.error.message);
			  } else {
			   	$(".card-errors").html("");
			   		var form =$(this);
				    stripe.createToken(card).then(function(result) {
					    if (result.error) {
					      $(".card-errors").html(result.error.message);
					      return;
					    } else {
					      $(".strip_token").val(result.token.id);
					      return;
					    }
				  });
			  }
			});

			var payment = $('input[name=payment]:checked').val();
			if( payment == "stripe" ){
				$(".card-element").removeClass('hidden');
			}else{
				$(".card-element").addClass('hidden');
			}
			$("body").on("change","input[name=payment]",function(e){
				var payment = $('input[name=payment]:checked').val();
				if( payment == "stripe" ){
					$(".card-element").removeClass('hidden');
				}else{
					$(".card-element").addClass('hidden');
				}
			})
    	} 
   	})
})(jQuery);
