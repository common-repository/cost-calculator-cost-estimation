<?php
add_action("calculation_form_tab_block_row","calculation_builder_block_row");
function calculation_builder_block_row(){
    ?>
    <li class="cfbuilder-row-inner"  data-type="row1" >
        <span></span>
    </li>
    <li class="cfbuilder-row-inner" data-type="row2">
        <span></span>
        <span></span>
    </li>
    <li class="cfbuilder-row-inner" data-type="row3">
        <span class="cf-row-2"></span>
        <span></span>
    </li>
    <li class="cfbuilder-row-inner" data-type="row4">
        <span></span>
        <span class="cf-row-2"></span>
    </li>
    <li class="cfbuilder-row-inner" data-type="row5">
        <span></span>
        <span></span>
        <span></span>
    </li>
    <li class="cfbuilder-row-inner" data-type="row6">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </li>
    <?php
}
add_filter( 'calculation_form_block_html', "calculation_form_block_row_load" );
function calculation_form_block_row_load($type){
    $col = array("row1","row2","row3","row4","row5","row6");
    $type["block"]["row1"]["builder"] = '
        <div data-type="row1" class="cfbuilder-container-row cfbuilder-row-container-row1">
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
        </div>';
    $type["block"]["row2"]["builder"]  = '
    <div data-type="row2" class="cfbuilder-container-row cfbuilder-row-container-row2">
            <div class="cfbuilder-row cfbuilder-row-empty1">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty2">
            </div>
        </div>';
    $type["block"]["row3"]["builder"]  = '
    <div data-type="row3" class="cfbuilder-container-row cfbuilder-row-container-row3">
            <div class="cfbuilder-row cfbuilder-row-empty cf-row-2">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
        </div>';    
    $type["block"]["row4"]["builder"]  = '
    <div data-type="row4" class="cfbuilder-container-row cfbuilder-row-container-row4">
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty cf-row-2">
            </div>
        </div>'
        ;  
    $type["block"]["row5"]["builder"]  = '
    <div data-type="row5" class="cfbuilder-container-row cfbuilder-row-container-row5">
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
        </div>';
    $type["block"]["row6"]["builder"]  = '
    <div data-type="row6" class="cfbuilder-container-row cfbuilder-row-container-row6">
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
            <div class="cfbuilder-row cfbuilder-row-empty">
            </div>
        </div>';
    return $type;
}
