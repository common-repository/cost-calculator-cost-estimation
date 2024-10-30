<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_html",22);
function calculation_forn_builder_block_html(){
    ?>
    <li data-type="html">
        <div class="cfbuilder-element" data-type="html">
            <i class="dashicons-before icon-paragraph"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("HTML","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_html_load" );
function calculation_form_block_html_load($type){
    $name = wp_unique_id("html-");
    $type["block"]["html"]["builder"] = '
<div class="cfbuilder-field" data-type="html">
    <div class="cfbuilder-click-throughs">   
    </div>
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label"></div>
        <div class="cfbuilder-field-container-element">
            <div class="cfbuilder-field-html">'.esc_html__("Lorem Ipsum is simply dummy text of the printing and typesetting industry","calculation-forms").'</div>
        </div>
    </div>
</div>';
    $type["block"]["html"]["data"]= [
        "default" =>array("tool"=>"#cfbuilder__toolbar_default_textarea","element"=>".cfbuilder-field-html","type"=>"html","type_tool"=>"textarea"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-html","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-html","type"=>"data","data"=>"id","type_tool"=>"text"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-html","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
    ];
    return $type; 
}