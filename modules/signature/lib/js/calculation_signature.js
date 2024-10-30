( function( $ ) {
	"use strict";
	jQuery(document).ready(function($) {
		$( ".calculation_signature_render" ).each(function( index ) {
			var data_id = $(this).data("id");
			var background = $(this).data("background");
			var color = $(this).data("color");
			var data = $('input[name ="'+data_id+'"]').val();
			var name = $(this).data("name");
			if( name != "" ){
				name =true;
			}else{
				name =false;
			}
			$(this).signature(
				 	{
				 	color: color,
				 	background: background,
				 	guideline: name,
				 	syncFormat: "PNG",
				 	syncField: $('input[name ="'+data_id+'"]'),
				 	name: name,
				 	change: function(){

				 	}
			 	});	
			if( data !="" ) {
				$(this).signature('draw', data);
			}
		});
		$("body").on("click",".calculation_signature_clear img",function(){
			$(this).closest(".calculation-element").find(".calculation_signature_render").signature('clear');
		})
		$("body").on("change",".calculation_signature_name",function(){
			var name = $(this).val();
			$(this).closest(".calculation-element").find(".calculation_signature_render").signature('setname');
		})
		$("body").on("mouseleave",".calculation_signature_name",function(){
			var name = $(this).val();
			if( name != ""){
				$(this).closest(".ccalculation-element").find(".calculation_signature_render").signature('setname');
			}
		})
	})
} )( jQuery );