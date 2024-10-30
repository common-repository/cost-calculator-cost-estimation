(function($) {
    "use strict";
    $( document ).ready( function () { 
        $.fn.calcucation_form_logic = function(){
            var form = this;
            var calcucation_form_data = $(".calculation-form-data-js",form).val();
            calcucation_form_data = JSON.parse(calcucation_form_data);
            $.each(calcucation_form_data.conditional, function( index, logic ) {
                 var checks = [];
                 $.each(logic.conditional, function( index, conditional ) { 
                    var name = conditional.name;
                    var current_value = form.get_value_by_name(conditional.name);
                    switch( conditional.rule ){
                        case "is":
                            if( current_value == conditional.value ){
                                checks.push(true);
                            }
                            break;
                        case "isnot":
                            if( current_value != conditional.value ){
                                checks.push(true);
                            }
                            break;
                        case ">":
                            if( current_value > conditional.value ){
                                checks.push(true);
                            }
                            break;
                        case '<':
                            if( current_value < conditional.value ){
                                checks.push(true);
                            }
                            break;
                        case "contains":
                            if( current_value.includes(conditional.value)  ){
                                checks.push(true);
                            }
                            break;
                        case "starts_with":
                            if( current_value.startsWith(conditional.value)  ){
                                checks.push(true);
                            }
                            break;
                        case "ends_with":
                            if( current_value.endsWith(conditional.value)  ){
                                checks.push(true);
                            }
                            break;
                    }
                 })
                 if( logic.logic == "all"){
                    if( checks.length ==  logic.conditional.length ){
                        if( logic.type != "show" ){ 
                            $('.element-'+index).addClass("hidden");
                            $('.calculation-element.'+index).addClass("hidden");
                        }else{
                            $('.element-'+index).removeClass("hidden");
                            $('.calculation-element.'+index).removeClass("hidden");
                        }
                    }else{
                        if( logic.type == "show" ){ 
                            $('.element-'+index).addClass("hidden");
                            $('.calculation-element.'+index).addClass("hidden");
                        }else{
                            $('.element-'+index).removeClass("hidden");
                            $('.calculation-element.'+index).removeClass("hidden");
                        }
                    }
                 }else{
                    if( checks.length >0 ){
                        if( logic.type != "show" ){ 
                            $('.element-'+logic.index).addClass("hidden");
                            $('.calculation-element.'+index).addClass("hidden");
                        }else{
                            $('.element-'+index).removeClass("hidden");
                            $('.calculation-element.'+index).removeClass("hidden");
                        }
                    }else{
                        if( logic.type == "show" ){ 
                            $('.element-'+index).addClass("hidden");
                            $('.calculation-element.'+index).addClass("hidden");
                        }else{
                            $('.element-'+index).removeClass("hidden");
                            $('.calculation-element.'+index).removeClass("hidden");
                        }
                    }
                 }
            });
        }
        $("body").on("change",".calculation-form input, .calculation-form select",function(){
            var form = $(this).closest("form");
            form.calcucation_form_logic();
        })
        $( ".calculation-form" ).each(function( index ) {
            $(this).calcucation_form_logic();
        });
    })
})(jQuery);