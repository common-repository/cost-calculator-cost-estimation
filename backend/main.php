<?php
class Calculation_Froms_Backend {
    function __construct(){
        add_action( 'init', array($this,'custom_post_type') );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action('admin_enqueue_scripts', array($this,'add_lib'));
        add_action( 'save_post',array( $this, 'save' ) );
        add_filter( 'manage_calculation_forms_posts_columns',  array($this,"add_colunms"),10000,2);
        add_action( 'manage_calculation_forms_posts_custom_column' , array($this,'custom_column'), 10, 2 );
    }
    function add_colunms($columns) {
        $new = array();
        unset($columns['date']); 
        $columns['shortcode'] = esc_html__( 'Shortcode', "calculation-forms" );
        $columns['date'] = esc_html__( 'Date');
        return $columns;
    }
    function custom_column( $column, $post_id ) {
        switch ( $column ) {
            case 'shortcode' :
                ?>
               <input type="text" onfocus="this.select();" readonly="readonly" value='[calculation id="<?php echo esc_attr($post_id) ?>" title="<?php echo esc_html(get_the_title( $post_id )) ?>"]' class="large-text code">
                <?php
                break;
        }
    }
    public static function load_text_name($name,$value=""){
        ?>
        <div class="calculation-forms-button-container">
            <input type="text" name="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($value) ?>" >
            <div class="calculation-forms-button-add-name">{..}</div>
        </div>
        <?php
    }
    function add_lib() {
        $ver = time();
        wp_enqueue_style('calculation_forms_font',CALCULATION_FORMS_PLUGIN_URL."backend/fonts/css/calculationforms.css");
        wp_enqueue_style('calculation_forms', CALCULATION_FORMS_PLUGIN_URL."backend/css/cf-style.css",array("calculation_forms_font"),$ver);   
        wp_enqueue_script('calculation_forms_main', CALCULATION_FORMS_PLUGIN_URL."backend/js/main.js",array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-draggable","jquery-ui-droppable","wp-color-picker"),$ver);
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' ); 
    }
    function custom_post_type() {
        register_post_type('calculation_forms',
            array(
                'labels'      => array(
                    'name'          => esc_html__( 'Calculation Forms', 'calculation-forms' ),
                    'singular_name' => esc_html__( 'Calculation', 'calculation-forms' ),
                ),
                'public'      => true,
                'has_archive' => true,
                'rewrite'     => array( 'slug' => 'calculation-forms' ),
                'supports'    =>array('title'),
                'menu_icon'   => CALCULATION_FORMS_PLUGIN_URL."backend/images/calculator.svg"
            )
        );
        register_post_type('cf_submissions',
            array(
                'labels'      => array(
                    'name'          => esc_html__( 'Entries', 'calculation-forms' ),
                    'singular_name' => esc_html__( 'Entries', 'calculation-forms' ),
                ),
                'public'      => true,
                'has_archive' => true,
                'rewrite'     => array( 'slug' => 'calculation-forms-submissions' ),
                'supports'    =>array('title'),
                'show_in_menu'=> "edit.php?post_type=calculation_forms",
            )
        );
    }
    function add_meta_boxes() {
        add_meta_box(
            'form-builder-main',
            esc_html__( 'Builder Form', 'calculation-forms' ),
            array( $this, 'form_builder_main' ),
            'calculation_forms',
            'normal',
            'default'
        );
    }
    function form_builder_main($post ) {
        $post_id= $post->ID;
        wp_nonce_field( 'calculation_forms_box_nonce', 'calculation_forms_box_nonce' );
        $mails = get_post_meta( $post_id, '_calculation_form_mails', true );
        $step = get_post_meta( $post_id, '_calculation_forms_multistep_style', true );

        if( !is_array($step) ){
            $step = array("style"=>1,"background"=>"#808080","color"=>"#fff","background_active"=>"#55b776","color_active"=>"#fff","background_completed"=>"#3491C4","color_completed"=>"#fff" );
        }
        $form_data = get_post_meta( $post_id, '_calculation_form', true );
        if( empty($form_data)){
            $form_data = '{"0":{"type":"row1","columns":{"0":{"0":{"data":{"name":{"type":"data","value":"number-1"},"label":{"type":"html","value":"Number 1"},"default":{"type":"input","value":""},"placeholder":{"type":"input","value":""},"format":{"type":"data","value":""},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":""},"logic":{"type":"data","value":""}},"type":"number"},"1":{"data":{"name":{"type":"data","value":"number-2"},"label":{"type":"html","value":"Number 2"},"default":{"type":"input","value":""},"placeholder":{"type":"input","value":""},"format":{"type":"data","value":""},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":""},"logic":{"type":"data","value":""}},"type":"number"},"2":{"data":{"name":{"type":"data","value":"total-10"},"label":{"type":"html","value":"Total"},"formula":{"type":"data","value":"[number-1]+[number-2]"},"format":{"type":"data","value":""},"shortcode":{"type":"data","value":"total-10"},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":""},"logic":{"type":"data","value":""}},"type":"total"},"3":{"data":{"name":{"type":"data","value":"your-name"},"label":{"type":"html","value":"Your Name"},"default":{"type":"input","value":""},"placeholder":{"type":"input","value":""},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":"checked"},"logic":{"type":"data","value":"uncheck"}},"type":"text"},"4":{"data":{"name":{"type":"data","value":"your-email"},"label":{"type":"html","value":"Your Email"},"default":{"type":"input","value":""},"placeholder":{"type":"input","value":""},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":"checked"},"logic":{"type":"data","value":"uncheck"}},"type":"email"},"5":{"data":{"name":{"type":"data","value":"your-subject"},"label":{"type":"html","value":"Subject"},"default":{"type":"input","value":""},"placeholder":{"type":"input","value":""},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"required":{"type":"data","value":"checked"},"logic":{"type":"data","value":"uncheck"}},"type":"text"},"6":{"data":{"default":{"type":"input","value":"Submit"},"class":{"type":"class","value":""},"id":{"type":"data","value":""},"logic":{"type":"data","value":"uncheck"}},"type":"submit"}}}}}';
        }
        ?>
        <div class="calculation-forms-builder">
            <div class="calculation-forms-tab">
                <ul>
                    <li class="calculation-forms-tab-main-item">
                        <a data-tab=".calculation-forms-content-tab-form" class="active" href="#"><?php esc_html_e("Form","calculation-forms") ?></a>       
                    </li>
                    <li class="calculation-forms-tab-main-item">
                        <a  data-tab=".calculation-forms-content-tab-email"class="" href="#"><?php esc_html_e("Settings","calculation-forms") ?></a>
                        <ul>
                            <li><a  data-tab=".calculation-forms-content-tab-email"class="" href="#"><?php esc_html_e("Email","calculation-forms") ?></a></li>
                            <?php do_action("calculation_forms_tab_settings") ?>
                        </ul>
                    </li>
                    <li class="calculation-forms-tab-main-item">
                        <a data-tab=".calculation-forms-content-tab-messages" href="#"><?php esc_html_e("Messages","calculation-forms") ?></a>
                    </li>
                    <?php do_action("calculation_forms_tab") ?>
                </ul>
                <div class="hr"></div>
            </div>
            <div class="calculation-forms-container">
                <div class="calculation-forms-content-tab calculation-forms-content-tab-form">
                    <div class="cfbuilder-content">
                        <div class="cfbuilder-container-tab">
                            <div class="cfbuilder-content-tab">
                                <div class="calculation-forms-step-header">
                                    <ul>
                                        <li class="active"><a data-id="1" href="#"><?php esc_html_e("Step 1") ?></a></li>
                                        <li><a data-id="0" href="#">+</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="calculation-forms-step-container">
                                <div class="calculation-forms-step-tab calculation-forms-step-tab-1">
                                    <div class="cfbuilder-content-body">
                                         <div class="cfbuilder-container-row" data-type="row1">
                                            <div class="cfbuilder-row cfbuilder-row-empty">
                                            </div>
                                         </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cfbuilder-sidebar">
                        <div class="header-tool-bar">
                            <div data-tab="cfbuilder-sidebar-data" class="active tool-filed-tab-add tool-filed-tab"><?php esc_html_e("Add Fields","calculation-forms") ?></div>
                            <div data-tab="cfbuilder-sidebar-tool" class="tool-filed-tab-settings tool-filed-tab"><?php esc_html_e("Filed Settings","calculation-forms") ?></div>
                        </div>
                        <div class="cfbuilder-sidebar-data cfbuilder-sidebar-content">
                            <?php 
                            $show = true;
                            
                            if( $show ):
                            ?>
                            <h4 class="active"><?php esc_html_e("Layout","calculation-forms") ?>
                            <i class="calculationform-icon icon-up-open"></i>            
                            </h4>
                            <ul class="cfbuilder-tab-row default_hide">
                                <?php do_action("calculation_form_tab_block_row") ?>
                            </ul>
                            <h4><?php esc_html_e("Common Fields","calculation-forms") ?>
                                <i class="calculationform-icon icon-down-open"></i>             
                            </h4>
                            <ul class="cfbuilder-tab-element">
                                <?php do_action("calculation_form_tab_block_common") ?>
                            </ul>
                            <h4 class="calculationform-last-child"><?php esc_html_e("Advanced Fields","calculation-forms") ?>
                            <i class="calculationform-icon icon-down-open"></i>             
                            </h4>
                            <ul class="cfbuilder-tab-element">
                                <?php do_action("calculation_form_tab_block_advanced") ?>
                            </ul>
                        <?php endif; ?>
                        </div>
                        <div class="cfbuilder-sidebar-tool cfbuilder-sidebar-content hidden">
                            <h3><?php esc_html_e("Type","calculation-forms") ?></h3>
                            <div class="cfbuilder-sidebar-tool-settings">
                            <h4><?php esc_html_e("General","calculation-forms") ?>
                               <i class="calculationform-icon icon-down-open"></i>             
                           </h4>
                            <div class="cfbuilder-sidebar-content-general">
                               <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_name">
                                   <label><?php esc_html_e("Field Name","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_label">
                                   <label><?php esc_html_e("Field Label","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_default">
                                   <label><?php esc_html_e("Default","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_default_textarea">
                                   <label><?php esc_html_e("Default","calculation-forms") ?></label>
                                   <select class="cfbuilder__toolbar_element-insert-tag" data-type="all" data-current="false">
                                       <option><?php esc_html_e("Insert Merge Tag","calculation-forms") ?></option>
                                   </select>
                                    <textarea></textarea>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_min">
                                   <label><?php esc_html_e("Min","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                 <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_max">
                                   <label><?php esc_html_e("Max","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_from">
                                   <label><?php esc_html_e("From","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_prefix">
                                   <label><?php esc_html_e("Prefix","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_postfix">
                                   <label><?php esc_html_e("Postfix","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_custom_value">
                                   <label><?php esc_html_e("Custom Values","calculation-forms") ?></label>
                                    <textarea placeholder="value1,value2,value3"></textarea>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_choose">
                                   <label><?php esc_html_e("Choices","calculation-forms") ?></label>
                                    <ul class="choose-header choose-tool">
                                        <li>
                                            <div class="choose-conatiner-default"><?php esc_html_e("D","calculation-forms") ?></div>
                                            <div class="choose-conatiner-label"><?php esc_html_e("Label","calculation-forms") ?></div>
                                            <div class="choose-conatiner-value"><?php esc_html_e("Value","calculation-forms") ?></div>
                                            <div class="choose-conatiner-data hidden"><?php esc_html_e("add/remove","calculation-forms") ?></div>
                                            <div class="choose-conatiner-action"><?php esc_html_e("add/remove","calculation-forms") ?></div>
                                        </li>   
                                    </ul>
                                    <ul class="choose-datas choose-tool">
                                    </ul>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_formula">
                                   <label><?php esc_html_e("Formula","calculation-forms") ?></label>
                                   <select class="cfbuilder__toolbar_element-insert-tag" data-type="all" data-current="true">
                                       <option><?php esc_html_e("Insert Merge Tag","calculation-forms") ?></option>
                                   </select> <a href="https://calculator.add-ons.org/document/using-calculations/" target="_blank" title=""><?php esc_html_e("Read the document","calculation-forms") ?></a>
                                    <textarea></textarea>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_required">
                                   <label><?php esc_html_e("Required","calculation-forms") ?></label>
                                    <input type="checkbox" value="1">
                                </div>
                                <?php if(CALCULATION_FORMS_PREMIUM) {
                                ?>
                                <div class="">
                                    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_number_format">
                                       <label><?php esc_html_e("Number Format","calculation-forms") ?></label>
                                        <select>
                                            <option value=""><?php esc_html_e("None","calculation-forms") ?></option>
                                            <option value="decimal_dot"><?php esc_html_e("9,999.99","calculation-forms") ?></option>
                                            <option value="decimal_comma"><?php esc_html_e("9.999,99","calculation-forms") ?></option>
                                        </select>
                                    </div>
                                    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_number_format_decimal">
                                       <label><?php esc_html_e("Decimal","calculation-forms") ?></label>
                                       <input type="text" value="2">
                                    </div>
                                    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_number_format_symbols">
                                       <label><?php esc_html_e("Symbols","calculation-forms") ?></label>
                                       <input type="text">
                                    </div>
                                    <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_number_format_symbols_position">
                                       <label><?php esc_html_e("Symbols position","calculation-forms") ?></label>
                                        <select name="">
                                            <option value="left" selected="selected"><?php esc_html_e("Left","calculation-forms") ?></option>
                                            <option value="right"><?php esc_html_e("Right","calculation-forms") ?></option>
                                        </select>
                                    </div>
                                </div>
                            <?php }else{
                                ?>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_number_format_decimal">
                                   <a href="<?php echo esc_url("https://codecanyon.net/item/calculator-forms-builder/34591796") ?>" target="_blank"><img src="<?php echo esc_url(CALCULATION_FORMS_PLUGIN_URL."backend/images/fromat-pro.png") ?>" alt=""></a>
                                </div>
                                <?php
                            } ?>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-remove">
                                   <a href="#" class="button button-primary calculation-forms-remove-step"><?php esc_html_e("Remove Step","calculation-forms") ?></a>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-name">
                                   <label><?php esc_html_e("Step Name","calculation-forms") ?></label>
                                   <input type="text" value="Step">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-next-button">
                                   <label><?php esc_html_e("Next button","calculation-forms") ?></label>
                                   <input type="text" value="Next">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-prev-button">
                                   <label><?php esc_html_e("Previous button","calculation-forms") ?></label>
                                   <input type="text" value="Previous">
                                </div>

                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-type">
                                   <label><?php esc_html_e("Type","calculation-forms") ?></label>
                                   <select name="calculation_forms_multistep_style[style]">
                                        <option <?php selected(1,$step["style"]) ?> value="1"><?php esc_html_e("Style 1","calculation-forms") ?></option>
                                        <option <?php selected(2,$step["style"]) ?>  value="2"><?php esc_html_e("Style 2","calculation-forms") ?></option>
                                        <option <?php selected(3,$step["style"]) ?>  value="3"><?php esc_html_e("Style 3","calculation-forms") ?></option>
                                        <option <?php selected(4,$step["style"]) ?>  value="4"><?php esc_html_e("Style 4","calculation-forms") ?></option>
                                        <option <?php selected(5,$step["style"]) ?>  value="5"><?php esc_html_e("Hide ProgressBar","calculation-forms") ?></option>
                                    </select>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-background">
                                   <label><?php esc_html_e("Background","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[background]" type="text" class="wp_color" value="<?php echo esc_attr($step["background"]) ?>">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-color">
                                   <label><?php esc_html_e("Color","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[color]" type="text" class="wp_color" value="<?php echo esc_attr($step["color"]) ?>">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-background-active">
                                   <label><?php esc_html_e("Background active","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[background_active]" type="text" class="wp_color" value="<?php echo esc_attr($step["background_active"]) ?>">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-color-active">
                                   <label><?php esc_html_e("Color active","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[color_active]" type="text" class="wp_color" value="<?php echo esc_attr($step["color_active"]) ?>">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-background-completed">
                                   <label><?php esc_html_e("Background completed","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[background_completed]" type="text" class="wp_color" value="<?php echo esc_attr($step["background_completed"]) ?>">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_multi-step-color-completed">
                                   <label><?php esc_html_e("Color completed","calculation-forms") ?></label>
                                   <input name="calculation_forms_multistep_style[color_completed]" type="text" class="wp_color" value="<?php echo esc_attr($step["color_completed"]) ?>">
                                </div>

                                
                               <?php do_action("calculation_form_tab_settings_general") ?>
                            </div>
                            <h4 class="calculationform-last-child"><?php esc_html_e("Advanced","calculation-forms") ?>
                               <i class="calculationform-icon icon-up-open"></i>             
                           </h4>
                            <div class="cfbuilder-sidebar-content-advanced default_hide active">
                               <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_class">
                                   <label><?php esc_html_e("Class","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_id">
                                   <label><?php esc_html_e("ID","calculation-forms") ?></label>
                                    <input type="text">
                                </div>
                                <?php do_action("calculation_form_tab_settings_advanced") ?>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_visible">
                                   <label><?php esc_html_e("Visibility","calculation-forms") ?></label>
                                    <select>
                                        <option value="visible" selected="selected"><?php esc_html_e("Visible","calculation-forms") ?></option>
                                        <option value="hidden"><?php esc_html_e("Hidden","calculation-forms") ?></option>
                                    </select>
                                </div>
                                <div class="cfbuilder__toolbar_element" id="cfbuilder__toolbar_logic">
                                   <label><?php esc_html_e("Conditional Logic","calculation-forms") ?></label>
                                    <input class="cfbuilder__toolbar_element_logic_input" data-logic="" data-logic_check="ok" type="checkbox" value="checked">
                                    <button class="cfbuilder__toolbar_element_logic_button"><?php esc_html_e('Edit',"calculation-forms") ?></button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="calculation-forms-content-tab calculation-forms-content-tab-email hidden">
                    <h3><?php esc_html_e("Email","calculation-forms") ?></h3>
                    <?php 
                     if( empty($mails)  ){
                            $mails = array("to"=>"[_site_admin_email]",
                                            "from" => "[_site_title] ". get_option("admin_email"),
                                            "subject" =>'[_site_title] "[your-subject]"',
                                            "headers" => "Reply-To: [your-email]",
                                            "body"=>"[all]
-- 
This e-mail was sent from a contact form on [_site_title] ([_site_url])"
                                        );
                     }
                    ?>
                    <ul>
                        <li>
                            <label for=""><?php esc_html_e("To","calculation-forms") ?></label>
                            <input type="text" name="cf_mail[to]" value="<?php echo esc_attr($mails["to"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("From","calculation-forms") ?></label>
                            <input type="text" name="cf_mail[from]" value="<?php echo esc_attr($mails["from"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("Subject","calculation-forms") ?></label>
                            <input type="text" name="cf_mail[subject]" value="<?php echo esc_attr($mails["subject"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("Additional headers","calculation-forms") ?></label>
                            <textarea name="cf_mail[headers]"><?php echo esc_textarea($mails["headers"]) ?></textarea>
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("Message body","calculation-forms") ?></label>
                            <textarea name="cf_mail[body]"><?php echo esc_textarea($mails["body"]) ?></textarea>
                        </li>
                    </ul>
                </div>
                <div class="calculation-forms-content-tab calculation-forms-content-tab-messages hidden">
                    <h3><?php esc_html_e("Messages","calculation-forms") ?></h3>
                    <?php 
                    $data_message = get_post_meta( $post_id, '_calculation_form_messages', true );
                     if( empty($data_message)  ){
                            $data_message = array("mail_sent_ok"=>"Thank you for your message. It has been sent.",
                                            "mail_sent_ng" => "There was an error trying to send your message. Please try again later.",
                                            "validation_error" =>'One or more fields have an error. Please check and try again.',
                                            "accept_terms" => "You must accept the terms and conditions before sending your message.",
                                            "invalid_required" => "The field is required.",
                                        );
                     }
                    ?>
                    <ul>
                        <li>
                            <label for=""><?php esc_html_e("Sender's message was sent successfully","calculation-forms") ?></label>
                            <input type="text" name="cf_message[mail_sent_ok]" value="<?php echo esc_attr($data_message["mail_sent_ok"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("Sender's message failed to send","calculation-forms") ?></label>
                            <input type="text" name="cf_message[mail_sent_ng]" value="<?php echo esc_attr($data_message["mail_sent_ng"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("Validation errors occurred","calculation-forms") ?></label>
                            <input type="text" name="cf_message[validation_error]" value="<?php echo esc_attr($data_message["validation_error"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("There are terms that the sender must accept","calculation-forms") ?></label>
                            <input type="text" name="cf_message[accept_terms]" value="<?php echo esc_attr($data_message["accept_terms"]) ?>">
                        </li>
                        <li>
                            <label for=""><?php esc_html_e("There is a field that the sender must fill in","calculation-forms") ?></label>
                            <input type="text" name="cf_message[invalid_required]" value="<?php echo esc_attr($data_message["invalid_required"]) ?>">
                        </li>
                    </ul>
                </div>
                <?php do_action("calculation_forms_tab_page",$post) ?>
            </div>
            <div class="cfbuilder-popup hidden">
                <div class="cfbuilder-popup-header">
                    <div class="cfbuilder-close-locgic">X</div>
                    <h3><?php esc_html_e("Configure Conditional Logic","calculation-forms") ?></h3>
                    <div class="cfbuilder-popup-des">
                        <?php esc_html_e("Conditional logic allows you to change what the user sees depending on the fields they select.","calculation-forms") ?>
                    </div>
                    <div>
                        <?php esc_html_e("Enable Conditional Logic","calculation-forms"); ?>
                        <input id="calculation-forms-logic-enable" data-logic_check="ok" type="checkbox" value="checked">
                    </div>
                </div>
                <div class="cfbuilder-popup-content">
                        <select name="" id="calculation-forms-logic-type">
                            <option value="show"><?php esc_html_e("Show","calculation-forms") ?></option>
                            <option value="hide"><?php esc_html_e("Hide","calculation-forms") ?></option>
                        </select>
                        <?php esc_html_e(" this field if","calculation-forms") ?>
                        <select name="" id="calculation-forms-logic-logic">
                            <option value="all"><?php esc_html_e("All","calculation-forms") ?></option>
                            <option value="any"><?php esc_html_e("Any","calculation-forms") ?></option>
                        </select>
                        <?php esc_html_e("of the following match:","calculation-forms") ?>
                        <div class="cfbuilder-popup-layout" >
                            <div class="calculation-forms-logic-item" id="cfbuilder-popup-layout-data">
                                <select class="calculation-forms-logic-name">
                                    <option value="">Name 1</option>
                                </select>
                                <select >
                                    <option value="is" selected="selected"><?php esc_html_e("is","calculation-forms") ?></option>
                                    <option value="isnot"><?php esc_html_e("is not","calculation-forms") ?></option>
                                    <option value=">"><?php esc_html_e("greater than","calculation-forms") ?></option>
                                    <option value="<"><?php esc_html_e("less than","calculation-forms") ?></option>
                                    <option value="contains"><?php esc_html_e("contains","calculation-forms") ?></option>
                                    <option value="starts_with"><?php esc_html_e("starts with","calculation-forms") ?></option>
                                    <option value="ends_with"><?php esc_html_e("ends with","calculation-forms") ?></option>
                                </select>
                                <input type="text" name="">
                                <div class="cfbuilder-popup-layout-settings">
                                    <button class="cfbuilder-popup-add">+</button>
                                    <button class="cfbuilder-popup-minus">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <textarea name="calculation_forms" id="calculation_forms_data" class="hidden" > <?php echo esc_textarea($form_data)  ?></textarea>
        <script type="text/javascript">
            <?php
                $data =array(); 
                $datas = apply_filters("calculation_form_block_html",$data);
            ?>
            var calculation_forms = <?php echo wp_json_encode($datas) ?>
        </script>
    <?php
  }
  public function save( $post_id ) {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['calculation_forms_box_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['calculation_forms_box_nonce'];
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'calculation_forms_box_nonce' ) ) {
            return $post_id;
        }
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        /* OK, it's safe for us to save the data now. */
        // Sanitize the user input.
        $cf_mail = array();
        $message = array();
        if( isset( $_POST['cf_mail'] ) && is_array($_POST['cf_mail'])){
            foreach( $_POST['cf_mail'] as $key => $value ) {
                $key = sanitize_key($key); 
                $cf_mail[$key] = ($value); 
            }
        }
        if( isset( $_POST['cf_message'] ) && is_array($_POST['cf_message'])){
            foreach( $_POST['cf_message'] as $key => $value ) { 
                $key = sanitize_key($key); 
                $message[$key] = ($value); 
            }
        }
        $calculation_forms = $_POST['calculation_forms'];
        // Update the meta field.
        update_post_meta( $post_id, '_calculation_form_mails', $cf_mail );
        update_post_meta( $post_id, '_calculation_form_messages', $message ); 
        update_post_meta( $post_id, '_calculation_form', $calculation_forms );
        update_post_meta( $post_id, '_calculation_forms_multistep_style', $_POST["calculation_forms_multistep_style"] );
    }
}
new Calculation_Froms_Backend;