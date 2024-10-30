(function($) {
    "use strict";
    $( document ).ready( function () { 
         $("body").on("click",".multistep-button-next", function(e) {
            //e.preventDefault(); 
            var form = $(this).closest("form");
            if( $(".calculation-forms-confirmation-step",form).length > 0 ) {
               var step_comfirm_html = '<div class="calculation-container-step-confirm">';
               var cout_tab = $(".total_step", form).val() - 2;
               $( ".calculation_progressbar li" ,form).each(function( index ) {
                  if( index > cout_tab ){
                     return;
                  }
                  step_comfirm_html +='<div class="calculation-step-confirm-title">'+ $( this ).find(".cf-content-s").text() +'</div>';
                  var tab_name = $(this).data("tab");
                  tab_name = ".calculation-container-tab-step-"+tab_name;
                  var name_tab = [];
                  form.find( tab_name + " input," + tab_name + " select," + tab_name +" textarea" ).each(function( index, joc ) {
                     if ($(this).attr("name") != "" && typeof $(this).attr("name") != 'undefined') { 
                        var name = $(this).attr("name").replace("[]", "");
                        if( name_tab.indexOf(name) < 0 ) {
                           name_tab.push(name);
                           var value = $(this).closest(".calculation-element").find(".calculation-element-label").html();
                           if(value  === undefined || value == "" ) {
                              value = name
                           }
                           var type =$(this).attr("type");
                           var data ="";
                           if( type == "radio" ){
                                 var chkArray = [];
                                 $("input[name="+name+"]:checked").each(function() {
                                    chkArray.push($(this).val());
                                 });
                                 data = chkArray.join(',') ;
                           } else if(type == "checkbox"){
                                 var chkArray = [];
                                 $('input[name="'+name+'[]"]:checked').each(function() {
                                    chkArray.push($(this).val());
                                 });
                                 data = chkArray.join(',') ;
                           } else{
                              data = $(this).val();
                           }
                           if(data.trim() != "") { 
                              if( name.search("repeater") !== 0 ) {
                                 step_comfirm_html +='<div class="calculation-step-confirm-item"><div class="calculation-step-confirm-name">'+ value+': </div><div class="calculation-step-confirm-value">'+ data +'</div></div>';
                              }
                           }
                        } 
                     }     
                  })
               });
               step_comfirm_html +="</div>";
               $(".calculation-forms-confirmation-step",form).html(step_comfirm_html);
            }
         })
    })
})(jQuery);