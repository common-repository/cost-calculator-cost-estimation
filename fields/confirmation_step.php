<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_confirmation",25);
function calculation_forn_builder_block_confirmation(){
    ?>
    <li data-type="confirmation">
        <div class="cfbuilder-element" data-type="confirmation">
            <i class="calculationforms-font icon-list-alt"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Confirmation Form","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}

add_filter( 'calculation_form_block_html', "calculation_form_block_confirmation_load" );
function calculation_form_block_confirmation_load($type){
    $name = wp_unique_id("confirmation-");
    $type["block"]["confirmation"]["builder"] = '
<div class="cfbuilder-field" data-type="confirmation">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Confirmation Form","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-payment="paypal" data-visible="visible" data-style="0" class="cfbuilder-field-payment" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["confirmation"]["data"]= [
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-file","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-file","type"=>"data","data"=>"id","type_tool"=>"text"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-payment","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_confirmation","calculation_form_block_confirmation_render",10,4);
function calculation_form_block_confirmation_render($content,$type,$elements,$data_attr){
   $content = '<div class="calculation-forms-confirmation-step"></div>';
    return $content;
}