<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_submit",100);
function calculation_forn_builder_block_submit(){
    ?>
    <li data-type="submit">
        <div class="cfbuilder-element" data-type="submit">
            <i class="dashicons-before icon-iphone-home"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Submit","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_submit_load" );
function calculation_form_block_submit_load($type){
    $type["block"]["submit"]["builder"] = '
<div class="cfbuilder-field" data-type="submit">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-container-element">
            <button data-visible="visible" data-style="0"  class="cfbuilder-field-submit" type="submit" value="Submit">'.esc_html__("Submit","calculation-forms").'</button>
        </div>
    </div>
</div>';
    $type["block"]["submit"]["validate"] = array("required");
    $type["block"]["submit"]["data"]= [
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-submit","type"=>"input","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-submit","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-submit","type"=>"data","data"=>"id","type_tool"=>"text"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-submit","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-submit","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}