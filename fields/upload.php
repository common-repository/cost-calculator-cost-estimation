<?php
add_action("calculation_form_tab_block_common","calculation_forn_builder_block_file",13);
function calculation_forn_builder_block_file(){
    ?>
    <li data-type="file">
        <div class="cfbuilder-element" data-type="file">
            <i class="dashicons-before icon-upload-cloud"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("File Upload","calculation-forms") ?></div>
        </div>
    </li>
    <?php
}
add_filter( 'calculation_form_tab_settings_general', "calculation_forn_builder_block_tool_file" );
function calculation_forn_builder_block_tool_file(){
    ?>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_file_multiple">
       <label><?php esc_html_e("Multiple Files","calculation-forms") ?></label>
        <input type="checkbox" value="1">
    </div>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_file_extensions">
       <label><?php esc_html_e("Allowed file extensions","calculation-forms") ?></label>
        <input type="text">
        <?php esc_html_e("Separated with commas (i.e. jpg, gif, png, pdf)","calculation-forms") ?>
    </div>
    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_file_size">
       <label><?php esc_html_e("Allowed file extensions","calculation-forms") ?></label>
        <input type="text">
    </div>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_file_load" );
function calculation_form_block_file_load($type){
    $name = wp_unique_id("file-");
    $type["block"]["file"]["builder"] = '
<div class="cfbuilder-field" data-type="file">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("File","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input data-visible="visible" data-style="0"  class="cfbuilder-field-file" type="file" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["file"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-file","type"=>"data","data"=>"name","type_tool"=>"text"),
        "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
        "file_multiple" =>array("tool"=>"#cfbuilder__toolbar_file_multiple","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-file_multiple","type_tool"=>"checkbox"),
        "file_extension" =>array("tool"=>"#cfbuilder__toolbar_file_extensions","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-file_extension","type_tool"=>"text"),
        "file_size" =>array("tool"=>"#cfbuilder__toolbar_file_size","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-file_size","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-file","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-file","type"=>"data","data"=>"id","type_tool"=>"text"),
        "required" =>array("tool"=>"#cfbuilder__toolbar_required","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-required","type_tool"=>"checkbox"),
        "visible" =>array("tool"=>"#cfbuilder__toolbar_visible","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-visible","type_tool"=>"select"),
        "logic" =>array("tool"=>"#cfbuilder__toolbar_logic","element"=>".cfbuilder-field-file","type"=>"data","data"=>"data-logic","type_tool"=>"logic"),
    ];
    return $type; 
}
function calculation_forms_upload_dir( $dirs ) {
    $dirs['subdir'] = '/calculation-forms';
    $dirs['path'] = $dirs['basedir'] . '/calculation-forms';
    $dirs['url'] = $dirs['baseurl'] . '/calculation-forms';
    return $dirs;
}