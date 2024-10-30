<?php
add_action("calculation_form_tab_block_advanced_v2","calculation_forn_builder_block_recaptcha",24);
function calculation_forn_builder_block_recaptcha(){
	?>
	<li data-type="recaptcha">
		<div class="cfbuilder-element" data-type="recaptcha">
            <i class="dashicons-before icon-doc-text"></i>
            <div class="cfbuilder-tool-text"><?php esc_html_e("reCAPTCHA","calculation-forms") ?></div>
        </div>
    </li>
	<?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_recaptcha_load" );
function calculation_form_block_recaptcha_load($type){
    $type["block"]["recaptcha"]["builder"] = '
<div class="cfbuilder-field" data-type="recaptcha">
    <div class="cfbuilder-field-container">
        <div class="cfbuilder-field-container-element">
            <div class="cfbuilder-field-recaptcha">'.esc_html__("reCAPTCHA","calculation-forms").'</div>
        </div>
    </div>
</div>';
    $type["block"]["recaptcha"]["data"]= [
                "label" =>array("tool"=>"#cfbuilder__toolbar_label","element"=>".cfbuilder-field-label","type"=>"html","type_tool"=>"text"),
    ];
    return $type; 
}