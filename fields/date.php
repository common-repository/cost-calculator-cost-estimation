<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_date",15);
function calculation_forn_builder_block_date(){
    ?>
    <li data-type="date">
        <div class="cfbuilder-element" data-type="date">
            <i class="dashicons-before icon-calendar"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Date","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_date_load" );
function calculation_form_block_date_load($type){
    $name = wp_unique_id("date-");
    $type["block"]["date"]["builder"] = '
<div class="cfbuilder-field" data-type="date">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Date","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input class="cfbuilder-field-date" data-visible="visible" data-style="0" type="date" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["date"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-date","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-date","type"=>"input","type_tool"=>"text"),
        "placeholder" =>array("tool"=>"#cfbuilder__toolbar_placeholder","element"=>".cfbuilder-field-date","type"=>"input","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-date","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-date","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-date","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-date","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-date","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-date","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}