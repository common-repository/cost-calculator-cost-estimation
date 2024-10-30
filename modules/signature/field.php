<?php
add_action("calculation_form_render_element_js","calculation_form_render_js_signature" );
function calculation_form_render_js_signature($type){
    switch($type){
        case "signature":
           wp_enqueue_script("calculation_signature_lib",CALCULATION_FORMS_PLUGIN_URL."modules/signature/lib/js/jquery.signature.js",array('jquery',"jquery-ui-core","jquery-ui-widget","jquery-ui-mouse"));
           wp_enqueue_script("jquery-ui-touch-punch",CALCULATION_FORMS_PLUGIN_URL."modules/signature/lib/js/jquery.ui.touch-punch.min.js",array('jquery',"jquery-ui-core","jquery-ui-widget","jquery-ui-mouse"));
           wp_enqueue_script("calculation_signature",CALCULATION_FORMS_PLUGIN_URL."modules/signature/lib/js/calculation_signature.js",array("jquery","calculation_signature_lib"),time());
           wp_enqueue_style("calculation_signature",CALCULATION_FORMS_PLUGIN_URL."modules/signature/lib/css/jquery.signature.css",array( ),time());
            break;
    }
}
add_filter("calculation_form_mail_attachments","calculation_add_signature_email_attachments",10,3);
function calculation_add_signature_email_attachments($mail_attachments,$datas, $form){
    $upload_dir = wp_upload_dir();
    $path_main = $upload_dir['basedir'] . '/calculation-forms/signature/'; 
    foreach($datas as $key => $value ){
         $type = Calculation_Forms_Process::get_data_element_type($key,$form);
        if( $type == "signature" ){
            if( $value != ""){
                $mail_attachments[]=$path_main.$value;
            }
        }
    }
    return $mail_attachments;
}
add_filter("calculation_form_data_value","calculation_upload_signature",10,3);
function calculation_upload_signature($value,$type, $form){
    if( $type == "signature" && $value != ""){

        $upload_dir = wp_upload_dir();
        $path_main = $upload_dir['basedir'] . '/calculation-forms/signature/';  
        if ( ! file_exists( $path_main ) ) {
            wp_mkdir_p( $path_main );
        }
        if( preg_match("/data\:image\/(.*)\;base64/",$value) ) {
             $name = uniqid() . 'signature.png';
             $path = $path_main . $name;
             $img = str_replace('data:image/png;base64,', '', $value);
            $img = str_replace(' ', '+', $img);
            $img = base64_decode($img);
             $success = file_put_contents($path, $img);   
             return $name;  ;
        }
    }
    return $value;
}
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_signature",16);
function calculation_forn_builder_block_signature(){
    ?>
    <li data-type="signature">
        <div class="cfbuilder-element" data-type="signature">
            <i class="dashicons-before icon-toggle-on"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Signature","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_tab_settings_general', "calculation_forn_builder_block_tool_signature" );
function calculation_forn_builder_block_tool_signature(){
    ?>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_signature_name">
       <label><?php esc_html_e("Input Full Name","calculation-forms") ?></label>
        <input type="checkbox" value="yes">
    </div>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_signature_width">
       <label><?php esc_html_e("Width (px)","calculation-forms") ?></label>
        <input type="text">
    </div>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_signature_height">
       <label><?php esc_html_e("Height (px)","calculation-forms") ?></label>
        <input type="text">
    </div>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_signature_load" );
function calculation_form_block_signature_load($type){
    $name = wp_unique_id("signature-");
    $type["block"]["signature"]["builder"] = '
<div class="cfbuilder-field" data-type="signature">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Signature","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-signature_width="400" data-signature_height="200"  data-signature_name="yes" data-visible="visible" data-style="0" class="cfbuilder-field-signature" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["signature"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "signature_name" =>array("tool"=>"#cfbuilder__toolbar_signature_name","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-signature_name","type_tool"=>"checkbox"),
        "signature_width" =>array("tool"=>"#cfbuilder__toolbar_signature_width","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-signature_width","type_tool"=>"text"),
        "signature_height" =>array("tool"=>"#cfbuilder__toolbar_signature_height","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-signature_height","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-signature","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
         "style" =>array("tool"=>"#cfbuilder__toolbar_style","element"=>".cfbuilder-field-signature","type"=>"data","data"=>"data-style","type_tool"=>"select"),
    ];
    return $type; 
}
add_filter("calculation_form_render_input_signature","calculation_form_block_signature_render",10,6);
function calculation_form_block_signature_render($content,$type,$elements,$data_attr,$name,$default){
    $li="";
    $full_name ="";
    $width = 400;
    $height= 200;
    $data_name ="";
    foreach( $elements["data"] as $key=> $data ){  
        switch( $key ){ 
            case "signature_name":
                if( $data["value"] == "checked"){
                   $full_name ="<input type='text'placeholder='".esc_html__( 'Enter Full Name', "calculation-forms" )."' class='calculation_signature_name' />"; 
                   $data_name ="yes";
                }
                break;
            case "signature_width":
                $width = $data["value"];
                break;
            case "signature_height":
                $height = $data["value"];
                break;
        }
    }
    $name_input = '<input type="hidden" name="'.$name.'" value="" >';
    $attr ='data-id="'.$name.'" data-name="'.$data_name.'"';
    $html_clear ="<div class='calculation_signature_clear'><img src='".CALCULATION_FORMS_PLUGIN_URL."modules/signature/lib/images/remove-icon.png' alt='' /></div>";
    $content ="<div style='max-width:400px' class='calculation_signature-container calculation_signature-container-{$name}'>
                {$html_clear}
                <div class='calculation_signature_render calculation_signature-{$name}' {$attr} style='width: {$width}px; height: {$height}px;'>
                </div>
                {$full_name}
                {$name_input}
             </div>";
    return $content;
}