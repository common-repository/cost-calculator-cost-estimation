<?php
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_number",12);
function calculation_forn_builder_block_number(){
    ?>
    <li data-type="number">
        <div class="cfbuilder-element" data-type="number">
            <i class="dashicons-before icon-sort-numeric"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Number","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_number_load" );
function calculation_form_block_number_load($type){
    $name = wp_unique_id("number-");
    $type["block"]["number"]["builder"] = '
<div class="cfbuilder-field" data-type="number">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Number","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input class="cfbuilder-field-number" data-visible="visible" data-style="0" data-symbols-position="left"  type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["number"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-number","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-number","type"=>"input","type_tool"=>"text"),
        "placeholder" =>array("tool"=>"#cfbuilder__toolbar_placeholder","element"=>".cfbuilder-field-number","type"=>"input","type_tool"=>"text"),
        "data-format" =>array("tool"=>"#cfbuilder__toolbar_number_format","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-format","type_tool"=>"select"),
        "data-symbols" =>array("tool"=>"#cfbuilder__toolbar_number_format_symbols","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-symbols","type_tool"=>"text"),
        "data-decimal" =>array("tool"=>"#cfbuilder__toolbar_number_format_decimal","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-decimal","type_tool"=>"text"),
        "data-symbols-position" =>array("tool"=>"#cfbuilder__toolbar_number_format_symbols_position","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-symbols-position","type_tool"=>"select"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-number","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-number","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-number","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}