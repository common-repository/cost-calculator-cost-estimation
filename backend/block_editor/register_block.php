<?php
add_action( 'init', 'calculation_form_init_block_editor', 10, 0 );
function calculation_form_init_block_editor() {
    wp_register_script(
        'calculation-forms-block-editor',
        CALCULATION_FORMS_PLUGIN_URL. "backend/block_editor/index.js",
        array(
            'wp-components',
            'wp-compose',
            'wp-blocks',
            'wp-element',
            'wp-i18n',
        ),
        "1.0"
    );
    register_block_type(
        'calculation-forms/selector',
        array(
            'editor_script' => 'calculation-forms-block-editor',
        )
    );
}
add_action( 'wp_ajax_calculation_forms_get_lists', 'calculation_forms_get_lists' );
function calculation_forms_get_lists(){
    $the_query = new WP_Query(array("post_type"=>"calculation_forms"));
    $datas = array();
    $datas[] = array("id"=>"","title"=>"-------");
    if( $the_query->have_posts()){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
           $datas[] = array("id"=>get_the_ID(),"title"=>get_the_title());
        }
    }
    wp_reset_postdata();
    wp_send_json($datas);
    die();
}