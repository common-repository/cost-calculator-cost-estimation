<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_payment",25);
function calculation_forn_builder_block_payment(){
    ?>
    <li data-type="payment">
        <div class="cfbuilder-element" data-type="payment">
            <i class="calculationforms-font icon-paypal"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Payment methods","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_tab_settings_general', "calculation_forn_builder_block_tool_payment" );
function calculation_forn_builder_block_tool_payment(){
    $data = array();
    $data = apply_filters("calculation_form_payment_methods",$data);
    $content ="";
    ?>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_payment_default">
       <label><?php esc_html_e("Payment Default","calculation-forms") ?></label>
        <?php 
        if( count($data) > 0 ){
            $i=0;
            foreach( $data as $name => $value ){
              $checked ="";
              if($i== 0 ){
                 $checked = 'checked="checked"';
              }
               $content .= '<div class="calculation-element-checkbox__item"><input '.esc_attr($checked).' id="'.esc_attr($name).'-'.esc_attr($i).'" name="payment" type="radio" value="'.esc_attr($name).'"><label for="'.esc_attr($name).'-'.esc_attr($i).'">'.esc_attr($value).'</label></div>';  
                $i++;
            }
        }else{
            $content = esc_html__("No Payment","calculation-forms");
        }
        echo $content;
        ?>
    </div>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_payment_load" );
function calculation_form_block_payment_load($type){
    $name = wp_unique_id("payment-");
    $type["block"]["payment"]["builder"] = '
<div class="cfbuilder-field" data-type="payment">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Payment methods","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-payment="paypal" data-visible="visible" data-style="0" class="cfbuilder-field-payment" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["payment"]["data"]= [
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "payment" =>array("tool"=>"#cfbuilder__toolbar_payment_default","element"=>".cfbuilder-field-payment","type"=>"data","data"=>"data-payment","type_tool"=>"radio"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-file","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-file","type"=>"data","data"=>"id","type_tool"=>"text"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
        "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-payment","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_payment","calculation_form_block_payment_render",10,4);
function calculation_form_block_payment_render($content,$type,$elements,$data_attr){
    global $calcucation_form_id;
    $data = array();
    $data = apply_filters("calculation_form_payment_methods",$data);
    $default = "";
    foreach( $elements["data"] as $key=> $data_value ){  
        switch( $key ){ 
            case "payment":
                $default = $data_value["value"];
                break;
        }
    }
    if( count($data) > 0 ){
        $i=0;
        foreach( $data as $name => $value ){
          $card ="";
          $class = "";
          if( $default == $name){
            $class = "active";
          }
          if( $name == "stripe" ){
            $card ='
                    <div class="card-element">
                    </div>
                    <div class="card-errors" role="alert"></div>
                    <input type="hidden" name="strip_token" class="strip_token" />
                    ';
                $content .= '<div class="calculation-element-radio__item calculation-element-payment__item '.$class.'"><input '.checked($default,$name,false).' id="'.esc_attr($name).'-'.esc_attr($i).'" name="payment" type="radio" value="'.esc_attr($name).'"><label for="'.esc_attr($name).'-'.esc_attr($i).'"><img alt="Stripe" src="'.CALCULATION_FORMS_PLUGIN_URL.'frontend/images/stripe.png" alt=""></label>'.$card.'</div>';      
          }else{
                $content .= '<div class="calculation-element-radio__item calculation-element-payment__item '.$class.'"><input '.checked($default,$name,false).' id="'.esc_attr($name).'-'.esc_attr($i).'" name="payment" type="radio" value="'.esc_attr($name).'"><label for="'.esc_attr($name).'-'.esc_attr($i).'">'.esc_attr($value).'</label>'.$card.'</div>';  
          }
           
            $i++;
        }
    }else{
        $content = esc_html__("No Payment","calculation-forms");
    }
    $paypal = get_post_meta($calcucation_form_id,"_calculation_form_paypal",true);
    if( $paypal["price"] == "" ) {
        $content = esc_html__("Choose Payment Price","calculation-forms");
    }
    return $content;
}