<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_swatches_images",16);
function calculation_forn_builder_block_swatches_images(){
    ?>
    <li data-type="swatches_images">
        <div class="cfbuilder-element" data-type="swatches_images">
            <i class="dashicons-before icon-picture"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Swatches Images","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_swatches_images_load" );
function calculation_form_block_swatches_images_load($type){
    $name = wp_unique_id("swatches_images-");
    $options = '%5B%7B%22default_check%22%3A%22checked%22%2C%22label%22%3A%22https%3A%2F%2Fpicsum.photos%2F200%2F300%22%2C%22value%22%3A%22Option%202%22%2C%22data%22%3A%22%22%7D%2C%7B%22default_check%22%3A%22%22%2C%22label%22%3A%22https%3A%2F%2Fpicsum.photos%2F200%2F300%22%2C%22value%22%3A%22Option%202%22%2C%22data%22%3A%22%22%7D%5D';
    $type["block"]["swatches_images"]["builder"] = '
<div class="cfbuilder-field" data-type="swatches_images">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Swatches mages","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <select data-visible="visible" data-style="0" data-choose="'.esc_attr( $options ).'" class="cfbuilder-field-swatches_images" name="'.esc_attr( $name ).'">
                <option selected value="value1">'.esc_html__("Option 1","calculation-forms").'</option>
                <option value="value2">'.esc_html__("Option 2","calculation-forms").'</option>
            </select>
        </div>
    </div>
</div>';
    $type["block"]["swatches_images"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "choose" =>array("tool"=>"#cfbuilder__toolbar_choose","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"data-choose","type_tool"=>"choose"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-swatches_images","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
         "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-swatches_images","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_swatches_images","calculation_form_block_swatches_images_render",10,6);
function calculation_form_block_swatches_images_render($content,$type,$elements,$data_attr,$name,$default){
    $li="";
    foreach( $elements["data"] as $key=> $data ){  
        switch( $key ){ 
            case "choose":
                $options = json_decode( urldecode($data["value"]),true );
                foreach( $options as $option ){
                    $default_check ="";
                    $class ="";
                    if( $option["default_check"] == "checked" ){
                        $default_check ="checked";
                        $class ="active";
                    }
                    $li .='<li class="'.$class.'"><img src="'.esc_url($option["label"]).'" alt="">
                        <input class="hidden" '.$default_check.' name="'.$name.'" type="radio" value="'.esc_attr($option["value"]).'" />
                        </li>';
                }
                break;
        }
    }
    $content = sprintf('<ul class="calculation-swatches_images">%1$s</ul>',$li);
    return $content;
}