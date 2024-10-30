<?php
class Calculation_Forms_Shortcode {
    function __construct(){
        add_shortcode( 'calculation', array($this,'add_shortcode') );
        add_filter( 'calcucation_form_data', array($this,"calcucation_form_data") ,10,3);
    }
    function calcucation_form_data($datas,$form){
        $conditional_data = $datas["conditional"];
        $formula_data = $datas["formula"];
        $formula_datas ="";
        $formula_name ="";
        $id_element_custom = 1;
        foreach($form as $step ){
            foreach($step["datas"] as $row ){
                foreach($row["columns"] as $column ){
                    foreach($column as $element ){
                        $datas_element = $element["data"];
                        if( isset($datas_element["logic"]["value"] ) && $datas_element["logic"]["value"] != "" && $datas_element["logic"]["value"] != "uncheck"){
                            if( isset($datas_element["name"]["value"])) {
                                $conditional_data[$datas_element["name"]["value"]] = json_decode(urldecode($datas_element["logic"]["value"] ),true);
                            }else{
                                $conditional_data["element-custom-".$id_element_custom] = json_decode(urldecode($datas_element["logic"]["value"] ),true);
                            }
                        }
                        if( isset($datas_element["formula"]["value"]) && $datas_element["formula"]["value"] != "" ){
                            $formula_data[$datas_element["name"]["value"]] = $datas_element["formula"]["value"];
                        }
                       $id_element_custom++; 
                    }
                }
            }
        }
        $datas["conditional"]= $conditional_data;
        $datas["formula"]= $formula_data;
        return $datas;
    }
    function add_shortcode($atts, $content){
        global $post;
        global $calcucation_form_id;
        $settings = get_option("calculation_forms_settings");
        $posst_id="";
        if( isset($post->ID) ){
            $posst_id = $post->ID;
        }
        $data = shortcode_atts( array(
                'id' => 0,
                'title' => ''
            ), $atts );
        $form = get_post_meta($data['id'],"_calculation_form",true);
        if( empty($form)){
                esc_html_e( "Not found 404 Form" );
        }else{
            $form = json_decode($form,true);
            if (!isset($form[0]["name"])) {
                 $form= array("name"=>"Step","next"=>"Next","prev"=>"Previous","datas"=>$form);
            }else{
            }
        $calcucation_form_id = $data['id'];
        do_action( "calculation_form_render_form_js", $calcucation_form_id );
        ob_start();
        ?>
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="calculation-form" id="calculation-form-<?php echo esc_attr($calcucation_form_id) ?>" enctype="multipart/form-data">
                <div class="hidden">
                    <input type="hidden" name="action" value="calculation_forms">
                    <input type="hidden" name="form_id" value="<?php echo esc_attr($data['id']) ?>">
                    <input type="hidden" name="post_id" value="<?php echo esc_attr($posst_id) ?>">
                    <input type="hidden" class="total_step" name="total_step" value="<?php echo esc_attr(count($form)) ?>">
                    <input type="hidden" class="current_step" name="current_step" value="1">
                    <?php
                    if( isset($settings["recaptcha_site_key"]) && $settings["recaptcha_site_key"] ){
                        ?>
                        <input class="calculation_g_recaptcha_response" type="hidden" name="calculation_g_recaptcha_response" value="">
                        <?php
                    }
                    $data =array("conditional"=>array(),"formula"=>array()); 
                    $datas = apply_filters("calcucation_form_data",$data,$form);
                    ?>
                    <textarea class="calculation-form-data-js"><?php echo esc_textarea( wp_json_encode($datas) ) ?></textarea>
                    <?php wp_nonce_field( 'calculation_add_user_meta_nonce' ); ?>
                </div>
                <div class="calculation-form-container form__inner">
                    <?php 
                        if(count($form)>1){
                            //multi-step
                             $step_settings = get_post_meta($calcucation_form_id,"_calculation_forms_multistep_style",true);
                             $type = $step_settings["style"];
                             $steps_background = $step_settings["background"];
                             $steps_color = $step_settings["color"];
                             $steps_background_active = $step_settings["background_active"];
                             $steps_color_active = $step_settings["color_active"];
                             $steps_background_completed = $step_settings["background_completed"];
                             $steps_color_completed = $step_settings["color_completed"];
                            $style ='<style type="text/css">';
                   if( $type == 3 || $type == 4  )  {
                    $style .= "#calculation-form-{$calcucation_form_id} .calculation_progressbar li.active .before {
                        border: 2px solid white;
                        box-shadow: 0 0 0 2px {$steps_background_active};
                    }
                    #calculation-form-{$calcucation_form_id} .calculation_step_completed span,#calculation-form-{$calcucation_form_id} .active span{
                        font-size: 18px;
                    }";
                   } 
                    $style .="#calculation-form-{$calcucation_form_id} .calculation_progressbar li span.before, #calculation-form-{$calcucation_form_id} .calculation_progressbar li .after{
                            background-color:{$steps_background} !important;    
                        }
                        #calculation-form-{$calcucation_form_id} .calculation_progressbar li {
                            color:{$steps_color} !important;    
                        }
                        #calculation-form-{$calcucation_form_id} .calculation_progressbar li .cf-content-s{
                            color:{$steps_background} !important;    
                        }
                        #calculation-form-{$calcucation_form_id} .calculation_progressbar li.active .before{
                            background: {$steps_background_active} !important;  
                        }
                        #calculation-form-{$calcucation_form_id} .calculation_progressbar li.active {
                            color:{$steps_color_active} !important;    
                        }
                        #calculation-form-{$calcucation_form_id} .calculation_progressbar li.calculation_step_completed .before, #calculation-form-{$calcucation_form_id} .calculation_progressbar li.calculation_step_completed + li .after{
                            background: {$steps_background_completed} !important;
                            
                        }
                        #calculation-form-{$calcucation_form_id} li.calculation_step_completed {
                            color:{$steps_color_completed} !important;    
                        }
                       ";
                       if( $type == 2 || $type == 4  ){
                            $style .= "#calculation-form-{$calcucation_form_id} .calculation_progressbar li span.before {
                                 border-radius: 7px !important;;       
                            }";
                       } 

                    $style .="</style>";
                    printf($style);
                            ?>
                            <ul class="calculation_progressbar calculation_progressbar-<?php echo esc_attr($type) ?>">
                                <?php 
                                $i=0;
                                foreach($form as $step){
                                   
                                    $first_class    = 'calculation_step_first';
                                    $last_class     = 'calculation_step_last';
                                    $complete_class = 'calculation_step_completed';
                                    $previous_class = 'calculation_step_previous';
                                    $next_class     = 'calculation_step_next';
                                    $pending_class  = 'calculation_step_pending';
                                    $after ="";
                                    $step_number= 1;
                                    $page = 3;
                                    $id ="";
                                    $name = $step["name"]; 
                                    $class ="";
                                    if( $i != 0 ) {
                                        $after = "<span class='after'></span>";
                                    }else{
                                        $class = "active";
                                    }
                                    if( $type == 1 || $type == 2) {
                                        // &#10004; -> check
                                        if($i == 0) {
                                            $before_content = "✓";
                                        }else{
                                            $before_content = "X";   
                                        }
                                        
                                    }else{
                                        $before_content = $i + 1;
                                    }
                                    printf("<li data-tab='%s' id='%s' class='%s'><span class='before'>%s</span><span class='cf-content-s'>%s</span>%s</li>",$i+1,$id,$class,$before_content,$name,$after);
                                    $i++;
                                } ?>
                            </ul>
                            <?php
                        }
                    ?>
                    <div class="calculation-form-row-container">
                        <?php
                        $i_tab=1;
                        foreach( $form as $tab_step ){ 
                            $class = "";
                            if( $i_tab != 1 ){
                               $class ="hidden"; 
                            } 
                            printf('<div class="calculation-container-tab-step calculation-container-tab-step-%s %s">',$i_tab,$class);
                            
                            foreach($tab_step["datas"] as $row) {
                                printf('<div class="calculation-container-row calculation-row-container-%s">',$row["type"]);
                                $i=1;
                                foreach( $row["columns"] as $column ){
                                    $class_row = "calculation-row";
                                    switch($row["type"]){
                                        case "row3":
                                            if( $i == 0){
                                                $class_row .=" calculation-row-2";
                                            }
                                            break;
                                        case "row4":
                                            if( $i == 1){
                                                $class_row .=" calculation-row-2";
                                            }
                                            break;
                                    }
                                    printf('<div class="%s">',$class_row);
                                    foreach( $column as $elements ){
                                        do_action("calculation_form_element",$elements,$i);
                                        $i++;
                                    }
                                 printf("</div>");
                                }
                                printf("</div>"); 
                            }
                            $i_tab++;
                            printf("</div>"); 
                        }
                         ?>
                    </div>
                </div>
                <?php do_action("calcucation_form_after_form");
                 if(count($form)>1){ 
                        ?>
                        <div class="multistep-nav">
                            <div class="multistep-nav-left">
                                <a href="#" class="multistep-button-first hidden"><?php esc_html_e("First","calculation-forms") ?></a>
                                <a href="#" class="multistep-button-prev hidden"><?php esc_html_e("Previous","calculation-forms") ?></a>
                            </div>
                            <div class="multistep-nav-right">
                                 <a href="#" class="multistep-button-next"><?php esc_html_e("Next","calculation-forms") ?></span></a>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </form>
        <?php
        }
        return ob_get_clean();
    }
}
new Calculation_Forms_Shortcode;