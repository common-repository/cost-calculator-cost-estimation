<?php
add_action("calculation_form_tab_block_advanced","calculation_forn_builder_block_hidden",23);
function calculation_forn_builder_block_hidden(){
	?>
	<li data-type="hidden">
		<div class="cfbuilder-element" data-type="hidden">
            <i class="dashicons-before icon-eye-off"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("Hidden","calculation-forms") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_hidden_load" );
function calculation_form_block_hidden_load($type){
    $name = wp_unique_id("hidden-");
    $type["block"]["hidden"]["builder"] = '
<div class="cfbuilder-field" data-type="hidden">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-label">'.esc_html__("Hidden Field","calculation-forms").'</div>
        <div class="cfbuilder-field-container-element">
            <input class="cfbuilder-field-hidden cfbuilder-field-disabled" type="text" name="'.esc_attr( $name ).'" value="">
        </div>
    </div>
</div>';
    $type["block"]["hidden"]["data"]= [
        "name"=>array("tool"=>"#cfbuilder__toolbar_name","element"=>".cfbuilder-field-hidden","type"=>"data","data"=>"name","type_tool"=>"text"),
        "default" =>array("tool"=>"#cfbuilder__toolbar_default","element"=>".cfbuilder-field-hidden","type"=>"input","type_tool"=>"text"),
        "class" =>array("tool"=>"#cfbuilder__toolbar_class","element"=>".cfbuilder-field-hidden","type"=>"class","type_tool"=>"text"),
        "id" =>array("tool"=>"#cfbuilder__toolbar_id","element"=>".cfbuilder-field-hidden","type"=>"data","data"=>"id","type_tool"=>"text"),
    ];
    return $type; 
}