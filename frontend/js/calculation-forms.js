(function($) {
    "use strict";
    $( document ).ready( function () {
        const { __, _x, _n, _nx } = wp.i18n;
        if(calculations.recaptcha_site_key != ""){
            grecaptcha.ready(function() {
              grecaptcha.execute(calculations.recaptcha_site_key, {action: 'submit'}).then(function(token) {
                  $(".calculation_g_recaptcha_response").val(token);
                  console.log(token);
              });
            });
        }
         $.fn.get_value_by_name = function(name) {
                var type ="";
                $("[name="+name+"]").each(function( index ) {
                        type = $(this).attr("type");
                        return false;
                    })
                    $("[name='"+name+"[]']").each(function( index ) {
                        type = $(this).attr("type");
                        return false;
                    })
                    switch(type) {
                        case "text":
                            if( $("[name="+name+"]").closest(".calculation-element.calculation-number-format").length>0 ){
                                $("[name="+name+"]").autoNumeric();
                                var vl = $("[name="+name+"]").autoNumeric("get");
                            }else{
                                var vl = $("[name="+name+"]").val();
                            }
                            break;
                        case "checkbox":
                            var vl = Number(0);
                            $("[name='"+name+"[]']:checked").each(function() {
                               vl += Number($(this).val());
                             });
                            break;
                        case "radio":
                            var vl = $("[name="+name+"]:checked").val();
                            break;
                        default:
                            var vl = $("[name="+name+"]").val();
                            break;
                    }
               return vl;
        };
        $.fn.calculation_format_number = function(name) {
            var check = $(this).data("format");
            if( check == "decimal_dot"){
                $(this).formatCurrencyLive({"symbol":"$", roundToDecimalPlace: 2 });
            }else if( check == "decimal_comma"){
                var decimalSymbol =",";
                var digitGroupSymbol =".";
                $(this).formatCurrencyLive({"symbol":"$", roundToDecimalPlace: 2,"symbol":"","decimalSymbol":decimalSymbol,"digitGroupSymbol":digitGroupSymbol });
            }
        };


        $("body").on("calculation_form_submit",".calculation-form", function(e) { 
            e.preventDefault();
            if(calculations.recaptcha_site_key != ""){
                grecaptcha.ready(function() {
                  grecaptcha.execute(calculations.recaptcha_site_key, {action: 'submit'}).then(function(token) {
                      $(".calculation_g_recaptcha_response",this).val(token);
                  });
                });
            }
            var form = $(this);
           var formData = new FormData(this);
            $('input[type=submit]', this).attr('disabled', 'disabled');
            $(".calculation-field-submit").after('<span class="calculation-spinner"></span>');
            var url = form.attr('action');
            $(".calculation-field-validate-message,.calculation-forms-response-message",form).remove();
            $(".alculation-element",form).removeClass(".calculation-field-validate");
            $.ajax({
                   type: "POST",
                   url: calculations.ajax_url,
                   data: formData,
                   cache: false,
                   contentType: false,
                   processData: false,
                   success: function(data){ 
                       var data = data.data;
                       if( $.calcucation_form_validURL(data.redirect) ) {
                            window.location.replace(data.redirect);
                       }
                       if( data.step ){
                            //next step
                            var current_step = form.find(".current_step").val();
                            var total_step = form.find(".total_step").val();
                            current_step = parseInt(current_step) +1;
                            $(".multistep-button-prev",form).removeClass("hidden");
                            $(".multistep-button-first",form).removeClass("hidden");
                            $(".calculation-container-tab-step",form).addClass("hidden");
                            $(".calculation-container-tab-step-"+current_step,form).removeClass("hidden");
                            $(".current_step",form).val( current_step);
                            var i = 1;
                            $(".calculation_progressbar li",form).removeClass("active calculation_step_completed")
                            $(".calculation_progressbar li",form).each( function(){
                                if(i < current_step){
                                    $(this).addClass("calculation_step_completed");
                                }if( i == current_step){
                                    $(this).addClass("active");
                                }
                                i++;
                            })
                            if( current_step == total_step ){ 
                              $(".multistep-button-next",form).html(__( 'Submit', 'calculation-forms' ) );  
                            }
                            if( current_step > total_step ){
                              form.append('<div class="calculation-forms-response-message">'+data.message+'</div/>');  
                            }
                            if( $(".calculation_progressbar-1",form).length > 0 || $(".calculation_progressbar-2",form).length  ){
                                    var i = 1;
                                    $(".calculation_progressbar li",form).each( function(){ 
                                        if( i <= current_step) {
                                            $(this).find(".before").html("✓");
                                        }else{
                                            $(this).find(".before").html("X");
                                        }
                                        i++;
                                    })

                            }
                       }else{
                            form.append('<div class="calculation-forms-response-message">'+data.message+'</div/>');
                       }
                       
                       
                       if( data.status == "validation_failed" ){
                            for(var k in data.invalid_fields) {
                               $(".calculation-element.element-"+k,form).addClass("calculation-field-validate");
                               $(".calculation-element.element-"+k,form).append('<div class="calculation-field-validate-message">'+data.invalid_fields[k]+'</div>');
                               if( $(".calculation-element.element-"+k,form).length < 1 ) {
                                    $(".calculation-element.element-type-"+k,form).addClass("calculation-field-validate");
                                    $(".calculation-element.element-type-"+k,form).append('<div class="calculation-field-validate-message">'+data.invalid_fields[k]+'</div>');  
                               }
                            }
                       }
                       if(  data.status == "success"  ){
                            $(".calculation-form").trigger( "calculation_form_success", data); 
                            $(".calculation-form-container",form).addClass("hidden");
                            $(".multistep-nav",form).addClass("hidden");
                       }
                        $('input[type=submit]', form).removeAttr("disabled");   
                        $(".calculation-spinner",form).remove();
                    },
                    error:( function(jqXHR, textStatus, error) {
                        $('input[type=submit]', form).removeAttr("disabled");   
                        $(".calculation-spinner",form).remove();
                        form.append('<div class="calculation-forms-response-message">There was an error. Please try again later.<br>'+jqXHR.responseText+'</div/>');
                    })
            });
        });

        $("body").on("submit",".calculation-form",function(e){
            e.preventDefault();
            
            var form = $(this);
           $( ".calculation-form").trigger( "calculation_form_submit", form); 
        });
        $("body").on("click",".calculation-swatches_images img",function(e){
            $(this).closest("ul").find("li").removeClass("active");
            $(this).closest("li").addClass("active");
            $(this).closest("li").find("input").prop('checked', true);
        })

         $("body").on("click",".multistep-button-first", function(e) { 
            e.preventDefault()
            var form = $(this).closest("form");
            $(".current_step",form).val(1);
            $(".calculation_progressbar li",form).removeClass("calculation_step_completed active");
            $(".calculation_progressbar li:first-child",form).addClass("active");
            $(".calculation-container-tab-step",form).addClass("hidden");
            $(".calculation-container-tab-step-1",form).removeClass("hidden");
            $(this).addClass("hidden");
            $(".multistep-button-prev",form).addClass("hidden");
            if( $(".calculation_progressbar-1",form).length > 0 || $(".calculation_progressbar-2",form).length  ){
                    var i = 1;
                    $(".calculation_progressbar li",form).each( function(){ 
                        if( i <= current_step) {
                            $(this).find(".before").html("✓");
                        }else{
                            $(this).find(".before").html("X");
                        }
                        i++;
                    })

            }
         })

         $("body").on("click",".multistep-button-prev", function(e) {
            e.preventDefault() 
            var form = $(this).closest("form");
            var total_step = $(".total_step",form).val();
            var current_step = $(".current_step",form).val();
            current_step = parseInt(current_step) - 1;
            if( current_step < 2 ){
                $(".multistep-button-prev",form).addClass("hidden");
                $(".multistep-button-first",form).addClass("hidden");
            }
            $(".current_step",form).val(current_step);
            $(".calculation_progressbar li",form).removeClass("calculation_step_completed active");
            $(".calculation-container-tab-step",form).addClass("hidden");
            $(".calculation-container-tab-step-"+current_step,form).removeClass("hidden");
            var i = 1;
            $(".calculation_progressbar li",form).each( function(){ 
                if( i < current_step) {
                    $(this).addClass("calculation_step_completed");
                }else if( i == current_step){
                    $(this).addClass("active");
                }else{
                    return false;
                }
                i++;
            })
            if( $(".calculation_progressbar-1",form).length > 0 || $(".calculation_progressbar-2",form).length  ){
                    var i = 1;
                    $(".calculation_progressbar li",form).each( function(){ 
                        if( i <= current_step) {
                            $(this).find(".before").html("✓");
                        }else{
                            $(this).find(".before").html("X");
                        }
                        i++;
                    })

            }
         })
         $("body").on("click",".multistep-button-next", function(e) {
            e.preventDefault(); 
            var form = $(this).closest("form");
            var current_step = form.find(".current_step").val();
            $(this).before('<span class="calculation-spinner"></span>');
            form.submit();
         })

        $(".calculation-number-format input").autoNumeric();
        $("body").on("click",".calculation-number-format input",function(){
            $(this).autoNumeric();
            var data = $(this).autoNumeric("get");
            $(this).val(data);
        })
        $.calcucation_form_urlParams = function(){
            var url = window.location.href;
            let params = new URLSearchParams(location.search)
            if( params.get("calculation_paypal_ipn") ){
                params.delete('calculation_paypal_ipn');
                params.delete('token');
                history.replaceState(null, '', '?' + params + location.hash)
            }
        }
        $.calcucation_form_urlParams(); 
         $.calcucation_form_validURL = function (str) {
          var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
          return !!pattern.test(str);
        }
    })
})(jQuery);
