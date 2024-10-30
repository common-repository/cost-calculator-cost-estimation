<?php
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_switch",13);
function calculation_forn_builder_block_switch(){
    ?>
    <li data-type="switch">
        <div class="cfbuilder-element" data-type="switch">
            <i class="dashicons-before icon-toggle-on"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Switch","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_tab_settings_general', "calculation_forn_builder_block_tool_swicth" );
function calculation_forn_builder_block_tool_swicth(){
    ?>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_switch_default">
       <label><?php esc_html_e("Default checked","calculation-forms") ?></label>
        <input type="checkbox" value="1">
    </div>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_switch_load" );
function calculation_form_block_switch_load($type){
    $name = wp_unique_id("switch-");
    $type["block"]["switch"]["builder"] = '
<div class="cfbuilder-field" data-type="switch">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Switch","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-visible="visible" data-style="0" class="cfbuilder-field-switch" type="checkbox" name="'.esc_attr( $name ).'" value="1">
        </div>
    </div>
</div>';
    $type["block"]["switch"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-switch","type"=>"input","type_tool"=>"text"),
        "default_check" =>array("tool"=>"#cfbuilder__toolbar_switch_default","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"data-default","type_tool"=>"checkbox"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-switch","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"data-style","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-switch","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_switch","calculation_form_block_switch_render",10,6);
function calculation_form_block_switch_render($content,$type,$elements,$data_attr,$name,$default){
    $checked ="";
    $style ="";
    foreach( $elements["data"] as $key=> $data ){  
        switch( $key ){ 
            case "default_check":
                if($data["value"] == "checked"){ 
                    $checked = "checked";
                }
                break;
            case "style":
                $style = $data["value"];
                break;
        }
    }
    $content = sprintf('<label class="button-switch element-style-%4$s">
              <input name="%1$s" type="checkbox" value="%2$s" %3$s >
              <span></span>
            </label>',esc_attr($name),esc_attr($default),esc_attr($checked), esc_attr($style));
    return $content;
}