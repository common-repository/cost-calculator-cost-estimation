<?php
add_action("calculation_form_element","calculation_form_elemen_text",10,2);
function calculation_form_elemen_text($elements,$name_custom_id){
    $name ="";
    $class ="";
    $class_new = array();
    $id= "";
    $label="";
    $label_html="";
    $default ="";
    $placeholder ="";
    $required ="";
    $logic ="";
    $chooses ="";
    $format ="";
    $type = $elements["type"]; 
    $attrs = "";
    $style = 0; 
    $style_total ="";
    $settings = get_option("calculation_forms_settings",array());  
    foreach( $elements["data"] as $key=> $data ){ 
        switch( $key ){
            case "name":
                if($type == "checkbox"){
                    $name = $data["value"]."[]";
                }else{
                   $name = $data["value"]; 
                }
                break;
            case "choose":
                $options = json_decode( urldecode($data["value"]),true );
                switch( $type ) {
                    case "select":
                        foreach( $options as $option ){
                            $default_check ="";
                            if( $option["default_check"] == "checked" ){
                                $default_check ="selected";
                            }
                            $chooses .='<option '.esc_attr($default_check).' value="'.esc_attr($option["value"]).'">'.esc_attr($option["label"]).'</option>';
                        }
                        break;
                    case "radio":
                    case "checkbox":
                        $chooses = $options;
                        break;              
                }
                break;
            case "visible":
                if( $data["value"] =="hidden" ) {
                    $class_new[]= "hidden";
                }
                break;
            case "file_multiple":
                if($data["value"] == "checked"){
                   $name = $name."[]";
                   $attrs .=" multiple";
                }
                break;
            case "style":
                $style = $data["value"]; 
                break;
            case "style_total":
                $style_total = $data["value"]; 
                break;
            default:
                if( str_contains($key,"data-") ){
                    switch( $key ){
                        case "data-format":
                            if($data["value"] == "decimal_comma"){
                                $attrs .= " ". 'data-a-dec="," data-a-sep="."';
                                $class_new[] ="calculation-number-format";
                            } elseif($data["value"] == "decimal_dot"){
                                $class_new[] ="calculation-number-format";
                            }
                            break;
                        case "data-symbols":
                            if($data["value"] != ""){
                                $class_new[] ="calculation-number-format";
                                $attrs .= " ". 'data-a-sign="'.esc_attr($data["value"]).'"'; 
                            }
                            break;
                        case "data-symbols-position":
                            if( $data["value"] =="right" ) {
                                $attrs .= " ". 'data-p-sign="s"';
                            }
                            break;
                        case "data-decimal":
                            $decimal ="";
                            if( $data["value"] == "" ){
                                $decimal = 2;
                            }else{
                                if( $data["value"] > 0) {
                                    $class_new[] ="calculation-number-format";
                                    $decimal = $data["value"];
                                }
                            }
                            $attrs.=" ".'data-m-dec="'.esc_html($decimal).'"';
                            break;
                        default:
                          $attrs .=" ".esc_html($key).'="'.esc_html($data["value"]).'"';  
                          break;
                    }
                }else{                  
                    if(isset($$key)){
                        $$key = $data["value"];
                    }
                }
                break;
        }
    }
    if($label != ""){
        $label_html = sprintf('<div class="calculation-element-label">%1s</div>',esc_html($label));
    }
    $class_new = array_unique($class_new);
    if(is_array($class_new) && count($class_new)> 0 ) {
        $class .=implode(" ",$class_new);
    }
    if( $id == ""){
        $id = "element-custom-".esc_attr($name_custom_id);
    }
    $allowed_html_form = array(
        'input' => array(
            'type'      => array(),
            'name'      => array(),
            'value'     => array(),
            'data'      => array(),
            'checked'   => array(),
            'class'     => array(),
            'id'        => array(),
        ),
        'option' => array(
            'name'      => array(),
            'value'     => array(),
            'data'      => array(),
            'selected'   => array()
        ),
        'label' => array(
            'data'      => array(),
            'for'       => array(),
            'class'     => array(),
            'id'        => array(),
        ),
        'div' => array(
            'data'      => array(),
            'class'     => array(),
            'id'        => array(),
        ),
    );
    do_action( "calculation_form_render_element_js", $type );
    switch( $type ){
        case "text":
        case "date":
        case "number":
        case "total":            
        case "email":
        case "submit":
        case "slider":
        case "file":
             $type_input = "text";
             if( $type == "submit" ){
                $type_input = "submit";
             }elseif( $type == "date"){
                $type_input = "date";
             } elseif( $type == "total"){
                $attrs .= " readonly";
             }
             elseif( $type == "file"){
                $type_input = "file";
             }
             $class_name = preg_replace("/\[\]/","",$name);
             printf ('<div id="%1$s" class="calculation-element %2$s element-%11$s element-type-%9$s element-style-%12$s element-style-total-%13$s" >
                %3$s
                <div class="calculation-field-element">
                    <input placeholder="%4$s" class="calculation-field-%9$s" type="%5$s" name="%6$s" value="%7$s" %10$s >
                </div>
            </div>',esc_attr($id),esc_attr($class),wp_kses_post($label_html,"post"),esc_html($placeholder),esc_html($type_input),esc_attr($name),esc_attr($default),esc_html($format),esc_attr($type),wp_kses_post($attrs),esc_attr($class_name),$style,$style_total);
            break;
        case "select":
            printf ('<div id="%1$s" class="calculation-element %2$s element-%4$s element-type-%6$s element-style-%7$s" >
                %3$s
                <div class="calculation-field-element">
                    <select name="%4$s">
                        %5$s
                    </select>
                </div>
            </div>',esc_attr($id),esc_attr($class),wp_kses_post($label_html),esc_attr($name),wp_kses($chooses,$allowed_html_form),esc_attr($type),esc_attr($style));
            break;
        case 'radio':
        case 'checkbox':
            $html ="";
            foreach( $options as $option ){
                $html .='<div class="calculation-element-checkbox__item"><input '.esc_attr($option["default_check"]).' name="'.esc_attr($name).'" type="'.esc_attr($type).'" value="'.esc_attr($option["value"]).'"><label for="'.esc_attr($name).'">'.esc_attr($option["label"]).'</label></div>';
            }
            $class_name = preg_replace('/\[]/', "", $name);
            printf ('<div id="%1$s" class="calculation-element %2$s element-%5$s element-type-%6$s element-style-%7$s" >
                %3$s
                <div class="calculation-field-element">
                    %4$s
                </div>
            </div>',esc_attr($id),esc_attr($class),wp_kses_post($label_html),wp_kses($html,$allowed_html_form),esc_attr($class_name),esc_attr($type),esc_attr($style));
            break;
        case "recaptcha_v2":
            printf ('<div id="%2$s" class="calculation-element calculation-element-recaptcha element-custom-%4$s element-%3$s element-type-%5$s" >
                %1$s
                <div class="calculation-field-element">
                    <div class="calculation-field-recaptcha g-recaptcha" data-sitekey="%6$s" >'.esc_html_e("reCAPTCHA","calculation-forms").'</div>
                    <script src="https://www.google.com/recaptcha/api.js"></script>
                </div>
            </div>',esc_attr($label_html),esc_attr($id),esc_attr($class),esc_attr($name_custom_id),esc_attr($type),esc_attr($settings["recaptcha_site_key"]));
            break;
        case "recaptcha":
            //v3
            break;
        case "html":
            preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $default, $matches );
            if ( is_array( $matches[1] ) ) {
                foreach ( $matches[1] as $match ) { 
                    $pattern = Calculation_Forms_Process::pattern_shortcode($match);
                    $value = '<span class="calculation-connect-formula calculation-connect-formula-'.esc_attr($match).'" data-name="'.esc_attr($match).'"></span>';
                    $default = preg_replace( "/$pattern/", $value, $default );
                }
            }
            printf ('<div id="%2$s" class="calculation-element calculation-element-html element-custom-%4$s %3$s element-type-%5$s" >
                <div class="calculation-field-element">
                    %1$s
                </div>
            </div>',wp_kses_post($default),esc_attr($id),esc_attr($class),esc_attr($name_custom_id),esc_attr($type));
            break;
        default:
            $content ="";
            $content = apply_filters("calculation_form_render_input_".$type,$content,$type,$elements,$attrs,$name,$default);
            $content = apply_filters("calculation_form_render_input",$content,$type,$elements,$attrs,$name);
            $class_name = preg_replace("/\[\]/","",$name);
             printf ('<div id="%1$s" class="calculation-element %2$s element-%3$s element-type-%4$s element-style-%7$s " >
                %5$s
                <div class="calculation-field-element">
                    %6$s
                </div>
            </div>',esc_attr($id),esc_attr($class),esc_attr($class_name),esc_attr($type),wp_kses_post($label_html,"post"),$content,esc_attr($style));
            break;
    }   
}