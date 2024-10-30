    (function($) {
    "use strict";
    $( document ).ready( function () { 
        var calculation_forms_check_save = false;
        $('body').on("click",".calculation-forms-download-pdf",function(e){
            e.preventDefault();
            
         })
        $('body').on("click",".calculation-forms-tab a",function(e){
            e.preventDefault();
            $(".calculation-forms-tab a").removeClass("active");
            $(this).addClass("active");
            $(this).closest(".calculation-forms-tab-main-item").children().addClass("active");
            var tab = $(this).data("tab");
            $(".calculation-forms-content-tab").addClass("hidden");
            $(tab).removeClass("hidden");
         })
        $('body').on("click",".cfbuilder-sidebar-data h4, .cfbuilder-sidebar-tool-settings h4",function(e){
            e.preventDefault();
            $(this).toggleClass("active");
            $(this).next().slideToggle('normal');
            if( $(this).hasClass("active")){
                $(this).find("i").removeClass("icon-down-open").addClass("icon-up-open");
            }else{
                $(this).find("i").removeClass("icon-up-open").addClass("icon-down-open");
            }
         })
        $('body').on("click",".cfbuilder-close-locgic",function(e){
            e.preventDefault();
            $(this).closest(".cfbuilder-popup").addClass("hidden");
         })
        $('body').on("click",".tool-filed-tab",function(e){
            e.preventDefault();
            $(".cfbuilder-sidebar-content").addClass("hidden");
            var tab = $(this).data("tab");
            $(".tool-filed-tab").removeClass('active');
            $(this).addClass("active");
            $("."+tab).removeClass("hidden");
            if( tab == "cfbuilder-sidebar-tool" && $(".cfbuilder-field.cfbuilder_active").length < 1){
                $(".cfbuilder-sidebar-tool").addClass("hidden");
            }
         })
        $('body').on("click",".calculation-forms-button-add-name",function(e){
            e.preventDefault();
            var html ="<option>Choose</option>";
            $(".cfbuilder-field input, .cfbuilder-field textarea, .cfbuilder-field select").each(function( index ) {
                var name = $(this).attr("name");
                html +='<option value="'+name+'">'+name+'</option>';
            });
            $(this).closest(".calculation-forms-button-container").find("select").remove();
            $(this).closest(".calculation-forms-button-container").append('<select class="calculation-forms-button-add-select">'+html+'</select');
         })
        $('body').on("change",".calculation-forms-button-add-select",function(e){
            var value = $(this).closest(".calculation-forms-button-container").find("input").val();
            value += $(this).val();
            $(this).closest(".calculation-forms-button-container").find("input").val(value);
            $(this).remove();
         })
        $('body').on("click","#calculation-forms-logic-enable",function(e){
            if( $(this).is(':checked') ){
                $(".cfbuilder-popup-content").removeClass("hidden");
                $(".cfbuilder__toolbar_element_logic_input").prop("checked",false);
            }else{
                $(".cfbuilder-popup-content").addClass("hidden");
                $(".cfbuilder__toolbar_element_logic_input").prop("checked",true);
            }
            $('.cfbuilder__toolbar_element_logic_input').trigger("click");
         })
        $('body').on("click",".cfbuilder__toolbar_element_logic_button",function(e){
            e.preventDefault();
            e.stopPropagation();
            $('html, body').animate({
                scrollTop: $("#form-builder-main").offset().top
            }, 500);
            var html ='';
            $(".cfbuilder-popup").toggleClass("hidden");
            var datas = $(".cfbuilder__toolbar_element_logic_input").data("logic");
            if( $(".cfbuilder__toolbar_element_logic_input").is(':checked') ){ 
                $("#calculation-forms-logic-enable").prop("checked",false);
            }else{
                $("#calculation-forms-logic-enable").prop("checked",true);
            }
            $('#calculation-forms-logic-enable').trigger("click");
            if( datas == ""){
                html = $.cfbuilder_get_logic_html({"name":"","rule":"is","value":""});
            }else{
                 datas= JSON.parse(decodeURIComponent(datas));
                 console.log(datas);
                 var type = datas.type;
                 $("#calculation-forms-logic-type").val(datas.type);
                 $("#calculation-forms-logic-logic").val(datas.logic);
                 $.each(datas.conditional, function( index, data ) {
                 html += $.cfbuilder_get_logic_html(data);
                });
            }
            $(".cfbuilder-popup-layout").html(html);
         })
        $.cfbuilder_get_logic_html = function(conditional){
            var names = $.cfbuilder_get_all_name();
            var name_logic_html = "";
            $.each(names, function( index, name ) {
                var selected_s = "";
                if( conditional.name == name ){
                    selected_s = 'selected';
                }
                name_logic_html += '<option '+selected_s+' value="'+name+'">'+name+'</option>';
            });
            var rules ={"is":"is","isnot":"is not",">":"greater than","<":"less than","contains":"contains","starts_with": "starts with","ends_with":"ends with"};
            var html = '<div class="calculation-forms-logic-item" >';
                html += '<select class="calculation-forms-logic-name">';
                        html += name_logic_html;
                    html += '</select>';
                    html += '<select class="calculation-forms-logic-rule">';
                    $.each(rules, function( key, rule ) {
                        var selected_s = "";
                        if( conditional.rule == key ){
                            selected_s = 'selected';
                        }
                        html += '<option '+selected_s+' value="'+key+'">'+rule+'</option>';
                    });
                    html += '</select>';
                    html += '<input type="text" class="calculation-forms-logic-value" value="'+conditional.value+'">';
                    html += '<div class="cfbuilder-popup-layout-settings">';
                        html += '<button class="cfbuilder-popup-add">+</button>';
                        html += '<button class="cfbuilder-popup-minus">-</button>';
                    html += '</div>';
                html += '</div>';
                return html;
        }
        $('body').on("change",".cfbuilder-popup-content select",function(e){
            $.cfbuilder_change_logic();
         })
        $('body').on("keyup",".cfbuilder-popup-content input",function(e){
            $.cfbuilder_change_logic();
         })
        $('body').on("click",".cfbuilder-popup-layout-settings .cfbuilder-popup-add",function(e){
            e.preventDefault();
            var data = $(this).closest(".calculation-forms-logic-item").clone();
            $(this).closest(".cfbuilder-popup-layout").append(data);
            $(this).closest(".cfbuilder-popup-layout").find('input').first().keyup();
         })
        $('body').on("click",".cfbuilder-popup-layout-settings .cfbuilder-popup-minus",function(e){
            e.preventDefault();
            var count_opt = $(this).closest(".cfbuilder-popup-layout").find(".calculation-forms-logic-item").length;
            if( count_opt < 2 ){
                alert("You should disable conditional logic");
            }else{
               $(this).closest(".calculation-forms-logic-item").remove();
               $(this).closest(".cfbuilder-popup-layout").find('input').first().keyup();
            }
         })
        $.cfbuilder_change_logic = function(){
            var type = $("#calculation-forms-logic-type").val();
            var logic = $("#calculation-forms-logic-logic").val();
            var conditional = [];
            $(".calculation-forms-logic-item").each(function() {
                var name = $(this).find(".calculation-forms-logic-name").val();
                var rule = $(this).find(".calculation-forms-logic-rule").val();
                var value = $(this).find(".calculation-forms-logic-value").val();
                conditional.push({name: name,rule: rule, value: value});
            });
            var data = {"type":type,"logic":logic,"conditional":conditional};
            var data = encodeURIComponent(JSON.stringify(data));
            $(".cfbuilder__toolbar_element_logic_input").data("logic",data).change();
        }
         $.cfbuilder_load_type = function (type,elements,email) { 
             var html = $(calculation_forms["block"][type]["builder"]); 
             return $(html);
          }
        $.fn.cfbuilder_row_droppable = function () { 
            $(this).draggable({
              helper: function () {
                    var type = $(this).data("type");
                    var html = $.cfbuilder_load_type(type);
                    html.find(".cfbuilder-row").addClass("builder-row-empty");
                    html.find(".cfbuilder-row").cfbuilder_element_sortable();
                    return html.removeAttr('style').css({width: 'auto',height: 'auto'});
                },  
              start: function (e, ui) {
                  ui.helper.addClass('cfbuilderemail-temp');
              },
              stop: function (e, ui) {
                 ui.helper.removeClass('cfbuilderemail-temp');
                 $.cfbuilder_remove_empty();
              },
              cursorAt: {left: 40, top: 15},
              connectToSortable: ".cfbuilder-content-body",
              revert : 0,
            });
        }
         $.fn.cfbuilder_row_sortable = function () {
            $(this).sortable({
              revert: "invalid",
              placeholder: 'cfbuilder-row-insert',
              start: function (ev, ui) {
                  ui.helper.addClass('cfbuider-email-dragging');
              },
              stop: function (ev, ui) {  
                  ui.item.removeClass('cfbuider-email-dragging');
                  $.cfbuilder_remove_empty();
              },
              handle: ".cfDragHandle",
              revert : 0,
            });
        }
        $.fn.cfbuilder_element_droppable = function () { 
            $(this).draggable({
              helper: function () {
                    var type = $(this).data("type");
                    $( this ).removeClass('cfbuilder-row-empty');
                    var html = $.cfbuilder_load_type(type);
                    return html.removeAttr('style').css({width: 'auto',height: 'auto'});
                },
              cursor: "move",
              cancel: ".disable-sort-item",
              cursorAt: {left: 40, top: 15},
              start: function (e, ui) {
                  ui.helper.addClass('cfbuilderemail-temp');
              },
              stop: function (e, ui) {
                 ui.helper.removeClass('cfbuilderemail-temp');
                 $.cfbuilder_remove_empty();
              },
              connectToSortable: ".cfbuilder-row",
              revert : 0,
              cursorAt: {left: 40, top: 15},
            });
        }
        $.fn.cfbuilder_element_sortable = function () { 
            $(this).sortable({
              connectWith: '.cfbuilder-row',
              revert: "invalid",
              placeholder: 'cfbuilder-row-insert',
              column: '',
              tolerance: "pointer",
              handle: ".cfDragHandle",
              revert : 0,
              start: function (ev, ui) {
                    ui.helper.addClass('cfbuider-email-dragging');
                    this.column = ui.helper.closest('.builder-row');
                },
              stop: function (ev, ui) { 
                ui.item.removeClass('cfbuider-email-dragging');
                if (ui.item.closest(".cfbuilder-row").find('.cfbuilder-elements').length) {
                    ui.item.closest(".cfbuilder-row").removeClass('cfbuilder-row-empty');
                }
                if (!(this.column.find('.cfbuilder-elements').length)) {
                    this.column.addClass('cfbuilder-row-empty');
                }
              },
            });
        }
        $( ".cfbuilder-content-body" ).cfbuilder_row_sortable();
        $( ".cfbuilder-tab-row li" ).cfbuilder_row_droppable();
        $( ".cfbuilder-tab-element li" ).cfbuilder_element_droppable();
        $( ".cfbuilder-row" ).cfbuilder_element_sortable(); 
        $('body').on("click",".cfbuilder-container-row",function(e){
            e.preventDefault();
            var toolbar= $('<div class="cfbuilder__toolbar">' +
            '<div class="cfDragHandle"><i class="calculationform-icon icon-menu"></i></div>' +
            '<div class="cfEdit"><i class="calculationform-icon icon-pencil"></i></div>' +
            '<div class="cfDuplicate"><i class="calculationform-icon icon-docs"></i></div>' +
            '<div class="cfDelete"><i class="calculationform-icon icon-trash-empty"></i></div>' +
            '</div>');
            $(this).append(toolbar);
            $(".cfbuilder-container-row").removeClass("cfbuilder_active");
            $(this).addClass("cfbuilder_active");
         })
        $("body").on('mouseenter', '.cfbuilder-container-row', function() {
           $(this).addClass('cfbuilder_hover');
        });
        $("body").on('mouseleave', '.cfbuilder-container-row', function() {
            $(this).removeClass('cfbuilder_hover');
        });
        $('body').on("click",".cfbuilder-container-row",function(e){ 
        })
        $(window).on("click.Bst", function(event){       
                    if ( 
            $(".calculation-forms-builder").has(event.target).length == 0 //checks if descendants of $box was clicked
            &&
            !$(".calculation-forms-builder").is(event.target) //checks if the $box itself was clicked
          ){
                        $("div").remove(".cfbuilder__toolbar");
                        $("div").removeClass("cfbuilder_active");
                        $(".cfbuilder-sidebar-tool").addClass("hidden");
                        $(".cfbuilder-sidebar-data").removeClass("hidden");
                    } else {
                    }
            });
        $('body').on("click",".cfbuilder-field",function(e){ 
            e.preventDefault();
             e.stopPropagation();
            $(".cfbuilder__toolbar").remove();
            var toolbar= $('<div class="cfbuilder__toolbar">' +
            '<div class="cfDragHandle"><i class="calculationform-icon icon-menu"></i></div>' +
            '<div class="cfEdit"><i class="calculationform-icon icon-pencil"></i></div>' +
            '<div class="cfDuplicate"><i class="calculationform-icon icon-docs"></i></div>' +
            '<div class="cfDelete"><i class="calculationform-icon icon-trash-empty"></i></div>' +
            '</div>');
            $(this).append(toolbar);
            $(".cfbuilder-field").removeClass("cfbuilder_active");
            $(this).addClass("cfbuilder_active");
            $(".cfbuilder-sidebar-data").addClass("hidden");
            $(".tool-filed-tab-settings").click();
            $(this).cfbuilder_load_editor();
            $.cfbuilder_update_merge_tag();
        })
        $('body').on("keyup",".cfbuilder-sidebar-tool input, .cfbuilder-sidebar-tool textarea",function(e){ 
            e.preventDefault();
            e.stopPropagation();
            $(this).cfbuilder_set_element();
        })
        $('body').on("change",".cfbuilder-sidebar-tool input:checkbox, .cfbuilder-sidebar-tool select, .cfbuilder-sidebar-tool input:radio",function(e){ 
            e.preventDefault();
            e.stopPropagation();
            $(this).cfbuilder_set_element();
        })
        $.fn.cfbuilder_set_element = function(){
            var data = $(this).val();
            var element = $(".cfbuilder-field.cfbuilder_active");
            if( element.length < 1 ){
                //set step 
                var step = $(".calculation-forms-step-header li.active");
                if( step.length > 0 ){
                    var style = $("#cfbuilder__toolbar_multi-step-type select").val();
                    var step_name = $("#cfbuilder__toolbar_multi-step-name input").val();
                    var step_next = $("#cfbuilder__toolbar_multi-step-next-button input").val();
                    var prev_next = $("#cfbuilder__toolbar_multi-step-next-button input").val();
                    $(".calculation-forms-step-header li.active a").html(step_name);
                    $(".calculation-forms-step-tab:not(.hidden)").data("next",step_next);
                    $(".calculation-forms-step-tab:not(.hidden)").data("name",step_name);
                    $(".calculation-forms-step-tab:not(.hidden)").data("prev",prev_next);
                }
                return;
            }
            var type = element.data("type");
            var data_attrs = calculation_forms["block"][type]["data"];
            var type_tool = $(this).attr("type");
            if( $(this).closest(".cfbuilder__toolbar_element").data("name") == 'choose'){
                type_tool = "choose";
            }
            var key = $(this).closest(".cfbuilder__toolbar_element").attr("data-name");
            var elm = data_attrs[key]["element"];
            var elm_type = data_attrs[key]["type"];
            switch ( type_tool){
                case "checkbox":
                    if( $(this).data("logic_check") == "ok" ){
                        if( $(this).is(':checked')) {
                            data = $(this).data("logic");
                        }else{
                            data = "uncheck";
                        }  
                     }else{
                        if( $(this).is(':checked')) {
                            data = "checked";
                        }else{
                            data = "uncheck";
                        }   
                     }    
                    break;
                case "choose":
                    var data_arr = [];
                    var i = 0;
                    $($(this).closest(".cfbuilder__toolbar_element").find("li")).each(function(){
                        if(i != 0){
                            if( $(this).find(".choose-default").is(":checked")){
                               var default_check= "checked";
                            }else{
                                var default_check ="";
                            }
                            var label = $(this).find(".choose-label").val();
                            var value = $(this).find(".choose-value").val();
                            var data = $(this).find(".choose-data").val();
                            var key_array = i - 1;
                          data_arr.push({"default_check":default_check,"label":label,"value": value,"data":data});
                        }
                        i++;
                    });
                    data = encodeURIComponent(JSON.stringify(data_arr));
                    break;
            }
            switch( elm_type ) {
                case "input":
                    element.find(elm).val(data);
                    break;
                case "html":
                    element.find(elm).html(data);
                    break;
                case "class":
                    element.find(elm).attr("data-class",data);
                    break;
                case "data":
                    element.find(elm).attr(data_attrs[key]["data"],data);
                    switch ( type ) {
                        case "select":
                        case "checkbox":
                        case "radio":
                            if( key == "choose" ){
                                var data_selects= JSON.parse(decodeURIComponent(data));
                                var options ="";
                                $.each(data_selects, function(index, data_select) {
                                    var selected ="";
                                    if( data_select.default_check== "checked"){
                                        selected = 'selected';
                                    }
                                    if( type =="select" ){
                                        options += '<option '+selected+' value="'+data_select.value+'">'+data_select.label+'</option>';
                                    } else if( type == "checkbox"){
                                        options += '<div class="cfbuilder-field-checkbox-inner"><input '+data_select.default_check+' type="checkbox" > <label for="">'+data_select.label+'</label></div>';
                                    }else if( type == "radio" ){
                                       options += '<div class="cfbuilder-field-radio-inner"><input '+data_select.default_check+' type="radio" > <label for="">'+data_select.label+'</label></div>';
                                    }
                                });
                                element.find(elm).html(options);
                            }
                            break;
                    }
                    //checkbox and radio add name
                    if( key == "name"){ 
                        if (type == "checkbox" || type == "radio") {
                           builder_element.find(elm).find("input").attr("name",value_el_inner);  
                           element.find(elm).find("input").attr('name',data);
                        }
                    }   
                    break;
                default:
                    element.find(elm).attr(elm_type,data);
                    break;
            }   
        }
        $('body').on("click",".cfDelete",function(e){
             e.preventDefault();
             e.stopPropagation();
             $(".cfbuilder-sidebar-tool").addClass('hidden');
             $(".cfbuilder-sidebar-data").removeClass('hidden');
             $(".tool-filed-tab-add").click();
             if(  $(this).closest(".cfbuilder-field").length < 1 ){
                $(this).closest(".cfbuilder-container-row").remove();
             }else{
                $(this).closest('.cfbuilder-field').remove();
             }
        })
        $('body').on("click",".cfDuplicate",function(e){
             e.preventDefault();
             e.stopPropagation();
             if(  $(this).closest(".cfbuilder-field").length > 0 ){
                var main_item = $(this).closest('.cfbuilder-field');
                var newItem = main_item.clone(true).removeClass("cfbuilder_active");
                newItem.find(".cfbuilder__toolbar").remove();
                main_item.after(newItem);
             }else{
                var main_item = $(this).closest('.cfbuilder-container-row');
                var newItem = main_item.clone(true).removeClass("cfbuilder_active").find(".cfbuilder-field").removeClass("cfbuilder_active");
                newItem.find(".cfbuilder__toolbar").remove();
                main_item.after(newItem);
             }   
        })
        $('body').on("click",".choices-add",function(e){
             e.preventDefault();
             e.stopPropagation();
             var data = $(this).closest("li").clone().html();
             $(this).closest("ul").append("<li>"+data+"</li>");
              $(this).closest("ul").find("[type=text]").first().keyup();
        })
        $('body').on("click",".choices-minus",function(e){
             e.preventDefault();
             e.stopPropagation();
             var count_opt = $(this).closest("ul").find("li").length;
             if( count_opt <2 ){
                alert("required 1 option");
             }else{
                $(this).closest("li").remove();
                $(this).closest("ul").find("[type=text]").first().keyup();
             }
        })
   $( 'body.post-type-calculation_forms #publish' ).click(function(){
        calculation_forms_check_save = false;
        var data = $.cfbuilder_save();
        $("#calculation_forms_data").val(data);
        if(calculation_forms_check_save){
           return false; 
        }
    });
$.load_builder_form = function(){
        var html = "";
        var data_json = $("#calculation_forms_data").val();
        if( data_json =="" || typeof data_json === "undefined"){
                   return;
                }
        var content = JSON.parse(data_json);
        var steps = {};
        if (typeof content[0]["name"] === "undefined") {
             steps[0]= {"name":"Step","next":"Next","prev":"Previous","datas":content};
        }else{
            steps =content;
        }
        var headers ="";
        var html = $("<div class='cfbuilder-content-body-container'></div>");
        var i_step = 0;
        var i_class ="";
        var content_class ="";
        $.each(steps, function(step_id, step) { 
            var datas = step["datas"];
            if( i_step == 0 ){
                i_class = "active";
            }else{
                content_class = "hidden";
                i_class ="";
            }
            i_step++;
            headers +='<li class="'+i_class+'" ><a data-id="'+( step_id + 1)+'" href="#">'+step["name"]+'</li>';
            var html_step =$('<div data-name="'+step["name"]+'" data-next="'+step["next"]+'" data-prev="'+step["prev"]+'" class="calculation-forms-step-tab calculation-forms-step-tab-'+( step_id + 1)+' '+content_class+'"></div>');
            var html_container =$('<div class="cfbuilder-content-body"></div>');
            console.log(step);
            $.each(datas, function(index, value) {
                var type = value["type"];
                var builder_row = $(calculation_forms["block"][type]["builder"]);
                var eq = 0;
                 $.each(value["columns"], function(index1, value1) { 
                        var col = builder_row.find(".cfbuilder-row:eq("+0+")");
                        $.each(value1, function(index2, value2) {
                            var type_el = value2["type"];
                            var builder_element = $(calculation_forms["block"][type_el]["builder"]);
                            var datas = value2["data"];
                            var data_attrs = calculation_forms["block"][type_el]["data"];
                            $.each(datas, function(index3, value3){
                                var type_el_inner = value3["type"];
                                var value_el_inner = value3["value"];
                                if (index3 in data_attrs) {
                                     var elm = data_attrs[index3]["element"];   
                                }else{
                                    return;
                                }
                                switch( type_el_inner ) {
                                    case "input":
                                    case "textarea":
                                        builder_element.find(elm).val(value_el_inner);
                                        break;
                                    case "html":
                                        builder_element.find(elm).html(value_el_inner);
                                        break;
                                    case "class":
                                         builder_element.find(elm).attr("data-class",value_el_inner);
                                        break;
                                    case "data":
                                        builder_element.find(elm).attr(data_attrs[index3]["data"],value_el_inner);
                                        switch( type_el){
                                            case "select":
                                            case "checkbox":
                                            case "radio":
                                                if( index3 == "choose"){
                                                    var data_selects= JSON.parse(decodeURIComponent(value_el_inner));
                                                    var options ="";
                                                    $.each(data_selects, function(index, data_select) {
                                                        var selected ="";
                                                        if( data_select.default_check== "checked"){
                                                            selected = 'selected';
                                                        }
                                                        if( type_el == "select" ){
                                                          options += '<option '+selected+' value="'+data_select.value+'">'+data_select.label+'</option>';  
                                                        }else if( type_el == "checkbox" ){
                                                           options += '<div class="cfbuilder-field-checkbox-inner"><input name="'+datas["name"]["value"]+'" '+data_select.default_check+' type="checkbox" > <label for="">'+data_select.label+'</label></div>';
                                                        }else if( type_el == "radio" ){
                                                           options += '<div class="cfbuilder-field-radio-inner"><input name="'+datas["name"]["value"]+'" '+data_select.default_check+' type="radio" > <label for="">'+data_select.label+'</label></div>';
                                                        }
                                                    });
                                                    builder_element.find(elm).html(options);
                                                }
                                                break;
                                        }
                                        break;
                                    default:
                                        builder_element.find(elm).attr(type_el,value_el_inner);
                                        break;
                                }
                            })
                            col.append(builder_element);
                        });
                         builder_row.append(col).find("div").removeClass("cfbuilder-row-empty");
                         eq++;
                 });
                 html_container.append(builder_row);
                 html_step.append(html_container);
            });
            html.append(html_step);
        })
        html.find(".cfbuilder-content-body").cfbuilder_row_sortable();
        html.find( ".cfbuilder-row" ).cfbuilder_element_sortable();
        headers +='<li><a data-id="0" href="#">+</a></li>';
        $(".calculation-forms-step-header ul").html(headers);
        $(".calculation-forms-step-container").html(html);         
   };
$.load_builder_form();
   $.cfbuilder_save = function(){
        var tabs ={};
        $(".calculation-forms-step-tab").each(function(tab_index,tab){
             var datas = {};
             var names = [];
            $(tab).find(".cfbuilder-container-row").each(function(index,row){
                var type = $(this).data("type");
                datas[index] = {type: type, columns: {}};
                $(row).find(".cfbuilder-row").each(function(index1,row1){
                    datas[index]["columns"][index1]={};             
                    $(row1).find(".cfbuilder-field").each(function(index2,row2){
                      var field = $(this);
                      var type_el = $(row2).data("type");
                    var data = calculation_forms["block"][type_el]["data"];
                      datas[index]["columns"][index1][index2]= {};
                      datas[index]["columns"][index1][index2]["data"]= {};
                      datas[index]["columns"][index1][index2]["type"]= type_el;
                      $.each(data, function( index3, value3 ) {
                         var type_el_inner = value3["type"];
                         var data_el_inner = value3["element"];
                         var data_value = "";
                         if(index3 == "name"){
                            var name = field.find(data_el_inner).attr("name");
                            if( name !== undefined ){
                                if( !names.includes(name) ){
                                    names.push(name);
                                }else{
                                   alert("Double Name: " + name);
                                   calculation_forms_check_save = true;
                                }
                            }
                         }
                         switch( type_el_inner ) {
                            case "input":
                            case "textarea":
                               data_value = field.find(data_el_inner).val();
                                break;
                            case "html":
                                data_value = field.find(data_el_inner).html();
                                break;
                            case "data":
                                data_value = field.find(data_el_inner).attr(value3["data"]);
                                break;
                            case "class":
                                data_value = field.find(data_el_inner).attr("data-class");
                             case "id":
                                data_value = field.find(data_el_inner).attr("id");
                                break;
                            default:
                                data_value = field.find(data_el_inner).attr(type_el_inner);
                                break;
                         }
                         if( data_value == undefined ){
                            data_value = "";
                         }
                         datas[index]["columns"][index1][index2]["data"][index3] = {"type":type_el_inner,"value":data_value};
                        });
                    });
                    }); 
            })
            var tab_name = $(this).data("name");
            var tab_prev = $(this).data("prev");
            var tab_next = $(this).data("next");
            if (typeof tab_name === "undefined") {
                tab_name = "Step";
            }
            if (typeof tab_prev === "undefined") {
                tab_prev = "Previous";
            }
            if (typeof tab_next === "undefined") {
                tab_next = "Next";
            }
            tabs[tab_index] = {"name":tab_name,"next":tab_next,"prev":tab_prev,"datas":datas};
        })
        return JSON.stringify(tabs);
   }
        $.fn.cfbuilder_load_editor = function(){
            var type = $(this).data("type");
            var element = (this);
            var data_attrs = calculation_forms["block"][type]["data"];
            //Show eidtor
            $(".cfbuilder__toolbar_element").addClass("hidden");
            $(".cfbuilder-sidebar-tool h3").html(type);
             $.each( data_attrs, function( key, value ) {
                //show
                $(value["tool"]).removeClass("hidden");
                $(value["tool"]).attr("data-name",key);
                var data_value = element.cfbuilder_get_element(key,value);
                $.cfbuilder_set_tool(value["type_tool"],value["tool"],data_value);
            }); 
        }
        $.fn.cfbuilder_get_element = function(name,data_attrs){
            var value = "";
            var type = data_attrs["type"];
            var data_class = data_attrs["element"];
            switch (type) {
                case "html":
                    value = $(this).find(data_class).html();
                    break;
                case "input":
                    value = $(this).find(data_class).val();
                    break;
                case "class":    
                    value = (this).find(data_class).attr("data-class");  
                    break;
                case "id":
                    value = (this).find(data_class).attr("id");    
                    break;
                case "placeholder":
                    value = (this).find(data_class).attr("placeholder");  
                    break;
                case "data":
                    value = (this).find(data_class).attr(data_attrs["data"]);  
                    break;
            }
            return value;
        }
        $.cfbuilder_set_tool = function(type,data_id,value){
            switch (type){
                case "text":
                    $(data_id).find("input").val(value);
                    break;
                case "textarea":
                    $(data_id).find("textarea").val(value);
                    break;
                case "checkbox":
                    if( value == "checked" ){
                        $(data_id).find("input").prop('checked', true);
                    }else{
                        $(data_id).find("input").prop('checked', false);
                    }
                    break;
                case "radio":
                    $(data_id).find("input[value="+value+"]").prop('checked', true);
                    break;
                case "select":
                    $(data_id).find("select").val(value);
                    break;
                case "choose":
                    var data_selects= JSON.parse(decodeURIComponent(value));
                        var options ="";
                        $.each(data_selects, function(index, data_select) {
                            var selected ="";
                            if( data_select.default_check== "checked"){
                                selected = 'checked';
                            }
                            options += '<li>';
                            options += '<div class="choose-conatiner-default"><input class="choose-default" '+selected+' type="radio" name="choices"></div>';
                            options += '<div class="choose-conatiner-label"><input class="choose-label" type="text" value="'+data_select.label+'"></div>';
                            options += '<div class="choose-conatiner-value"><input class="choose-value" type="text" value="'+data_select.value+'"></div>';
                            options += '<div class="choose-conatiner-data hidden"><input class="choose-data" type="text" value="'+data_select.data+'"></div>';
                            options += '<div class="choose-conatiner-action"><div class="choices-add">+</div><div class="choices-minus">-</div></div></li>';
                        });
                     $(data_id).find(".choose-datas").html(options);
                    break;
                case "logic":
                    if( value == "uncheck" || value === undefined || value == ""){
                        $(data_id).find("input").prop('checked', false);
                        $(data_id).find("input").data("logic","");
                    }else{
                        $(data_id).find("input").prop('checked', true);
                        $(data_id).find("input").data("logic",value);
                    }
                    break;
            }
        }
        $.cfbuilder_remove_empty = function() {
            $( ".cfbuilder-row" ).each(function( index ) {
                    var elm = $(this);
                    var check = $(this).find(".cfbuilder-field").length;
                    if( check > 0 ){
                      elm.removeClass("cfbuilder-row-empty");
                    }else{
                       elm.addClass("cfbuilder-row-empty"); 
                    }
            });
        }
        $.cfbuilder_update_merge_tag = function(){
            $(".cfbuilder__toolbar_element-insert-tag").each(function( index ) {
                var html = '<option value="">Insert Merge Tag</option>';
                var type = $(this).data("type");
                var current = $(this).data("current");
                var current_name = $('.cfbuilder-field.cfbuilder_active').find("input").attr("name");
                var names = $.cfbuilder_get_all_name(current_name,type,current_name);
                $.each(names, function( index, name ) {
                     html +='<option value="['+name+']">['+name+']</option>';
                });
                $(this).html(html);
            })    
        }
        $.cfbuilder_get_all_name = function(current = false, type="all", current_name = ""){
            var names = [];
            $(".cfbuilder-field input, .cfbuilder-field textarea, .cfbuilder-field select").each(function( index ) {
                var name = $(this).attr("name");
                if( type != "all"){
                    var check_type = $(this).closest(".cfbuilder-field").data("type");
                    if( type ==  check_type){
                        if( !names.includes(name)){
                            names.push(name);
                            if( current_name == name){
                                return false;
                            }
                        }
                    }
                }else{
                    if( !names.includes(name)){
                        names.push(name);
                        if( current_name == name){
                            return false;
                        }
                    }
                }
            });
            return names;
        }
        $("body").on("change",".cfbuilder__toolbar_element-insert-tag",function(e){
            var value = $(this).val();
            $(this).closest(".cfbuilder__toolbar_element").find("textarea").insertAtCaret(value).trigger( "keyup" );
        })
        $('body').on('click', '.calculation-form-import', function(e){
            e.preventDefault();
                var button = $(this),
                    custom_uploader = wp.media({
                title: 'Import template',
                library : {
                    type : [ 'json',"text"]
                },
                button: {
                    text: 'Import template' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function() { // it also has "open" and "close" events 
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $.getJSON(attachment.url, function(data){
                    $("#calculation_forms_data").val(data);
                    $.load_builder_form();
                }).fail(function(){
                  alert("Error");
                });
            })
            .open();
        });
        $("body").on("click",".calculation-form-export",function(e){
            e.preventDefault;
            $("<a />", {
                "download": "email_template.json",
                "href" : "data:text/plain;charset=utf-8," + encodeURIComponent(JSON.stringify($("#calculation_forms_data").val()))
              }).appendTo("body")
              .click(function() {
                 $(this).remove()
              })[0].click();
        })
        $('body').on("click",".calculation-forms-step-header a",function(e){
            e.preventDefault();
            $(".calculation-forms-step-header li").removeClass("active");
            var id = $(this).data("id");
            if(id == 0 ){
                var tag_id = Math.floor(Math.random() * 9999);
                $(this).closest("li").before("<li><a data-id='"+tag_id+"' href='#'>Step</a><li/>");
                var html_step = '<div class="calculation-forms-step-tab calculation-forms-step-tab-'+tag_id+' hidden">';
                html_step +='<div class="cfbuilder-content-body" ><div class="cfbuilder-container-row" data-type="row1">';
                html_step +='<div class="cfbuilder-row cfbuilder-row-empty"></div></div> </div></div>';
                 html_step = $(html_step);
                 html_step.find(".cfbuilder-content-body").cfbuilder_row_sortable();
                html_step.find( ".cfbuilder-row" ).cfbuilder_element_sortable();
                $(".calculation-forms-step-container").append(html_step);
            }else{
                $(this).closest("li").addClass('active');
                $("div").remove(".cfbuilder__toolbar");
                $("div").removeClass("cfbuilder_active");
                $(".cfbuilder-sidebar-tool h3").html("Multi-step settings");
                $(".tool-filed-tab-settings").click();
                $(".cfbuilder__toolbar_element").addClass("hidden");
                $("#cfbuilder__toolbar_multi-step-type,.cfbuilder-sidebar-tool").removeClass("hidden");
                $("#cfbuilder__toolbar_multi-step-type,#cfbuilder__toolbar_multi-step-name,#cfbuilder__toolbar_multi-step-remove").removeClass("hidden");
                $("#cfbuilder__toolbar_multi-step-color,#cfbuilder__toolbar_multi-step-color-active,#cfbuilder__toolbar_multi-step-color-completed").removeClass("hidden");
                $("#cfbuilder__toolbar_multi-step-background,#cfbuilder__toolbar_multi-step-background-active,#cfbuilder__toolbar_multi-step-background-completed").removeClass("hidden");
                var name = $(this).html();
                var next = $(".calculation-forms-step-tab:not(.hidden)").data("next");
                var prev = $(".calculation-forms-step-tab:not(.hidden)").data("prev");
                var tag_id = $(this).data("id");
                $("#cfbuilder__toolbar_multi-step-name input").val(name);
                $("#cfbuilder__toolbar_multi-step-next-button input").val(next);
                $("#cfbuilder__toolbar_multi-step-prev-button input").val(prev);
                $(".calculation-forms-step-tab").addClass("hidden");
                $(".calculation-forms-step-tab-"+tag_id).removeClass("hidden");
            }
         })
        $('body').on("click",".calculation-forms-remove-step",function(e){
            e.preventDefault();
            var count_step = $(".calculation-forms-step-header li").length;
            if( count_step > 0 ) {
                var id = $(".calculation-forms-step-header li.active a").data("id");
                if (typeof id === "undefined") {
                    alert("Choose Step");
                }else{
                   $(".calculation-forms-step-header li.active, .calculation-forms-step-tab-"+id).remove(); 
                }
                

            }else{
                alert("No Step");
            }
            
            
         })
        $(function() {
            $('.wp_color').wpColorPicker();
        });
        $.fn.insertAtCaret = function(myValue){
            this.each(function() {
              if (document.selection) {
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
              } else if (this.selectionStart || this.selectionStart == '0') {
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos) +
                  myValue + this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
              } else {
                this.value += myValue;
                this.focus();
              }
            });
            return this;
        }
    })
})(jQuery);