<?php
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_radio",18);
function calculation_forn_builder_block_radio(){
    ?>
    <li data-type="radio">
        <div class="cfbuilder-element" data-type="radio">
            <i class="dashicons-before icon-dot-circled"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Radio","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_radio_load" );
function calculation_form_block_radio_load($type){
    $name = wp_unique_id("radio-");
    $options = '%5B%7B%22default_check%22%3A%22checked%22%2C%22label%22%3A%22Option%201%22%2C%22value%22%3A%22Option%202%22%2C%22data%22%3A%22%22%7D%2C%7B%22default_check%22%3A%22%22%2C%22label%22%3A%22Option%202%22%2C%22value%22%3A%22Option%202%22%2C%22data%22%3A%22%22%7D%5D';
    $type["block"]["radio"]["builder"] = '
<div class="cfbuilder-field" data-type="radio">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Radio","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <div class="cfbuilder-field-radio" data-visible="visible" data-style="0"  data-choose="'.esc_attr($options).'" data-name="'.esc_attr( $name ).'">
                <div class="cfbuilder-field-radio-inner">
                    <input type="radio" name="'.esc_attr( $name ).'" > <label for="">'.esc_html__("First Choice","calculation-forms").'</label>
                </div>
                <div class="cfbuilder-field-radio-inner">
                    <input type="radio" name="'.esc_attr( $name ).'" > <label for="">'.esc_html__("Second Choice","calculation-forms").'</label>
                </div>
            </div>
        </div>
    </div>
</div>';
    $type["block"]["radio"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "choose" =>array("tool"=>"#cfbuilder__toolbar_choose","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-choose","type_tool"=>"choose"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-radio","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-radio","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}