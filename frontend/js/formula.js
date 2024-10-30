(function($) {
    "use strict";
    $( document ).ready( function () { 
        $.fn.calcucation_form_fomulas = function(){
            var reg = [];
            var match;
            var form = this;
            var calcucation_form_data = $(".calculation-form-data-js",form).val();
            calcucation_form_data = JSON.parse(calcucation_form_data);
            $("input,select",form).each(function () { 
                var name = $(this).attr("name");
                name = String(name);
                name = name.replace("[]", '');
                reg.push(name);
            })
            reg = $.calcucation_form_fomulas_remove_duplicates(reg);
            var field_regexp = new RegExp( '('+reg.join("|")+')');
            $.each(calcucation_form_data.formula, function (index, eq){
                eq = eq.replace(/(\r\n|\n|\r)/gm, "");
                eq = eq.replace(/\s/gm, "");
                while ( match = field_regexp.exec( eq ) ){ 
                    var type = "";
                    var vl = form.get_value_by_name(match[0]);
                     var reg_inner = new RegExp("\\["+match[0] + "\](?!\\d)","gm");
                    eq = eq.replace( reg_inner, vl ); 
                }
                eq = $.calcucation_form_fomulas_days(eq);
                eq = $.calcucation_form_fomulas_months(eq);
                eq = $.calcucation_form_fomulas_years(eq);
                eq = $.calcucation_form_fomulas_floor(eq);
                eq = $.calcucation_form_fomulas_mod(eq);
                eq = $.calcucation_form_fomulas_elseif(eq);
               
                try{
                    var total = mexp.eval( eq ); // Evaluate the final equation
                }
                catch(e)
                {
                     total = 0;
                }
                total = $.calcucation_form_fomulas_round(total,index);
                if( $('[name="'+index+'"]').closest(".calculation-element").hasClass("calculation-number-format") ){
                    $('[name="'+index+'"]').autoNumeric();
                    $('[name="'+index+'"]').autoNumeric("set",total);
                }else{
                    $('[name="'+index+'"]').val(total);
                }
                $.calcucation_form_fomulas_connect();
            });
        }
        $.calcucation_form_fomulas_round = function(num,name) {
            var a = $('[name="'+name+'"]').data("m-dec");
            if( a != "" ){
                return num
            }else{
                return +(Math.round(num + "e+2")  + "e-2");
            }    
        }
        $.calcucation_form_fomulas_connect = function(){
            $(".calculation-connect-formula").each(function( index ) { 
                var name = $(this).data("name");
                $(".calculation-connect-formula-"+name).html($('[name="'+name+'"]').val());
            })
        }
        $.calcucation_form_fomulas_elseif = function(x){ 
            var re = /if\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    return $.calcucation_form_fomulas_if(x);
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_elseif(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_if = function(x){
            x = x.replace(/[if()]/g, '');
            var data = x.split(",");
            try {
                  if(eval(data[0])){
                      return mexp.eval(data[1]);
                  }else{
                      return mexp.eval(data[2]);
                  }
            } catch (e) {
               return 0;
            }               
        }
        $.calcucation_form_fomulas_days = function(x){ 
            var re = /days\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[days()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.calcucation_form_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.calcucation_form_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return $.calcucation_form_fomulas_datediff(day_end,day_start);
                      }
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_days(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_months = function(x){ 
            var re = /months\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[months()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.calcucation_form_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.calcucation_form_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return day_start.getMonth() - day_end.getMonth() +  (12 * (day_start.getFullYear() - day_end.getFullYear()))
                      }
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_months(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_years = function(x){ 
            var re = /years\(([^()]*)\)/gm;
            console.log(x);
            x = x.replace( re,function (x) {
                console.log(x);
                     x = x.replace(/[years()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.calcucation_form_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.calcucation_form_fomulas_parse_date(day_start1);
                     console.log(day_start);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        console.log("ok");
                        return day_start.getFullYear() - day_end.getFullYear();
                      }
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_years(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_floor = function(x){ 
            var re = /floor\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x);
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_floor(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_mod = function(x){ 
            var re = /mod\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[mod()]/g, '');
                    var datas = x.split(",");
                     return  datas[0] % datas[1];
                });
            if( x.match(re) ){
                x = $.calcucation_form_fomulas_floor(x);
            }
            return x;
        }
        $.calcucation_form_fomulas_parse_date = function(str,split = "-"){
            return new Date(str);
        }
        $.calcucation_form_fomulas_datediff = function(first, second){
            return Math.round((second-first)/(1000*60*60*24));
        }
        $.calcucation_form_fomulas_remove_duplicates = function(arr){
            var obj = {};
            var ret_arr = [];
            for (var i = 0; i < arr.length; i++) {
                obj[arr[i]] = true;
            }
            for (var key in obj) {
                if( "_wpnonce" == key || "undefined" == key  || "_wp_http_referer" == key || "action" == key  ){
                }else {
                    if(key !=""){
                        ret_arr.push(key +"(?!\\d)");
                    }
                }
            }
            return ret_arr;
        }
        $('body').on("change",".calculation-form input,.calculation-form select",function(e){
            var form = $(this).closest("form");
            form.calcucation_form_fomulas();
        })
        $( ".calculation-form" ).each(function( index ) {
            $(this).calcucation_form_fomulas();
        });
    })
})(jQuery);