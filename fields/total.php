<?php
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_total",13);
function calculation_forn_builder_block_total(){
    ?>
    <li data-type="total">
        <div class="cfbuilder-element" data-type="total">
            <i class="dashicons-before icon-calc"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Total","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_action("calculation_form_tab_settings_advanced","calculation_forn_builder_block_tool_total");
function calculation_forn_builder_block_tool_total(){
    ?>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_style">
       <label><?php esc_html_e("Style","calculation-forms") ?></label>
        <select>
                <option value="0"><?php esc_html_e("Default","calculation-forms") ?></option>
                <option value="2"><?php esc_html_e("2 Collums","calculation-forms") ?></option>
        </select>
    </div>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_style_total">
       <label><?php esc_html_e("Style","calculation-forms") ?></label>
        <select>
                <option value="0"><?php esc_html_e("Input","calculation-forms") ?></option>
                <option value="1"><?php esc_html_e("Label","calculation-forms") ?></option>
        </select>
    </div>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_total_load" );
function calculation_form_block_total_load($type){
    $name = wp_unique_id("total-");
    $type["block"]["total"]["builder"] = '
<div class="cfbuilder-field" data-type="total">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Total","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-style-total="1" data-visible="visible" data-style="0" data-symbols-position="left"  class="cfbuilder-field-total" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["total"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-total","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "formula" =>array("tool"=>"#cfbuilder__toolbar_formula","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-formula","type_tool"=>"textarea"),
        "data-format" =>array("tool"=>"#cfbuilder__toolbar_number_format","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-format","type_tool"=>"select"),
        "data-symbols" =>array("tool"=>"#cfbuilder__toolbar_number_format_symbols","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-symbols","type_tool"=>"text"),
        "data-symbols-position" =>array("tool"=>"#cfbuilder__toolbar_number_format_symbols_position","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-symbols-position","type_tool"=>"select"),
        "data-decimal" =>array("tool"=>"#cfbuilder__toolbar_number_format_decimal","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-decimal","type_tool"=>"text"),
        "shortcode"=>array("tool"=>"#cfbuilder__toolbar_shortcode","element"=>".cfbuilder-field-total","type"=>"data","data"=>"name","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-total","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-total","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-style","type_tool"=>"select"),
        "style_total" =>array("tool"=>"#cfbuilder__toolbar_style_total","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-style-total","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-total","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
    ];
    return $type; 
}