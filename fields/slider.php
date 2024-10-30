<?php
add_action("calculation_form_render_element_js","calculation_form_render_js_slider") ;
function calculation_form_render_js_slider($type){
        switch($type){
            case "slider":
                wp_enqueue_style("ionrangeslider",CALCULATION_FORMS_PLUGIN_URL ."libs/ionrangeslider/css/ion.rangeSlider.min.css",array(),time());
                wp_enqueue_script("ionrangeslider",CALCULATION_FORMS_PLUGIN_URL ."libs/ionrangeslider/js/ion.rangeSlider.min.js");
                wp_enqueue_script("calculation_slider",CALCULATION_FORMS_PLUGIN_URL ."libs/ionrangeslider/js/slider.js",array("jquery"));
                break;
        }
    }
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_slider",12);
function calculation_forn_builder_block_slider(){
    $class ="";
    $text_pro ="";
    if( !CALCULATION_FORMS_PREMIUM ){
        $class = "disable-sort-item";
        $text_pro = __(" (Pro)","calculation-forms");
    }
	?>
	<li data-type="slider" class="<?php echo  esc_attr($class) ?>">
		<div class="cfbuilder-element" data-type="slider">
            <i class="dashicons-before icon-sliders"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Slider","calculation-forms"); echo esc_html($text_pro) ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_slider_load" );
function calculation_form_block_slider_load($type){
    $name = wp_unique_id("slider-");
    $type["block"]["slider"]["builder"] = '
<div class="cfbuilder-field" data-type="slider">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Slider","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-visible="visible" data-style="0"  class="cfbuilder-field-slider" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["slider"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "data-min"=>array("tool"=>"#cfbuilder__toolbar_min","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-min","type_tool"=>"text"),
        "data-max"=>array("tool"=>"#cfbuilder__toolbar_max","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-max","type_tool"=>"text"),
        "data-from"=>array("tool"=>"#cfbuilder__toolbar_from","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-from","type_tool"=>"text"),
        "data-prefix"=>array("tool"=>"#cfbuilder__toolbar_prefix","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-prefix","type_tool"=>"text"),
        "data-postfix"=>array("tool"=>"#cfbuilder__toolbar_postfix","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-postfix","type_tool"=>"text"),
        "data-values"=>array("tool"=>"#cfbuilder__toolbar_custom_value","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-values","type_tool"=>"textarea"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-slider","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"id","type_tool"=>"text"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-slider","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}