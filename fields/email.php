<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_email",14);
function calculation_forn_builder_block_email(){
    ?>
    <li data-type="email">
        <div class="cfbuilder-element" data-type="email">
            <i class="dashicons-before dashicons-email"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Email","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_email_load" );
function calculation_form_block_email_load($type){
    $name = wp_unique_id("email-");
    $type["block"]["email"]["builder"] = '
<div class="cfbuilder-field" data-type="email">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Email","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input class="cfbuilder-field-email" data-visible="visible" data-style="0" type="email" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["email"]["validate"] = array("required");
    $type["block"]["email"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-email","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-email","type"=>"input","type_tool"=>"text"),
        "placeholder" =>array("tool"=>"#cfbuilder__toolbar_placeholder","element"=>".cfbuilder-field-email","type"=>"input","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-email","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-email","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-email","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-email","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-email","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-email","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}