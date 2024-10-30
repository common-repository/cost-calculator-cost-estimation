<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_coupon",24);
function calculation_forn_builder_block_coupon(){
    ?>
    <li data-type="coupon">
        <div class="cfbuilder-element" data-type="coupon">
            <i class="calculationforms-font icon-tag-1"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Coupon","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}

add_filter( 'calculation_form_block_html', "calculation_form_block_coupon_load" );
function calculation_form_block_coupon_load($type){
    $name = wp_unique_id("coupon-");
    $type["block"]["coupon"]["builder"] = '
<div class="cfbuilder-field" data-type="coupon">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Coupon","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input class="cfbuilder-field-coupon" data-visible="visible" data-style="0" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["coupon"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-coupon","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-file","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-file","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-coupon","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_coupon","calculation_form_block_coupon_render",10,4);
function calculation_form_block_coupon_render($content,$type,$elements,$data_attr){
    $content = sprintf('<input type="text" /><a class="calculation-form-button calculation-form-button-coupon" href="#" title="">%1$s</a>',esc_html__("Appy couple","calculation-forms"));
    return $content;
}