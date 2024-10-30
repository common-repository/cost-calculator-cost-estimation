<?php
class Calculation_Froms_Settings_Mailchimp {
    function __construct(){
        add_action("calculation_forms_tab_settings",array($this,"add_tab"));
        add_action("calculation_forms_tab_page",array($this,"add_page"));
        add_action("save_post",array($this,"save_post"));
        add_action("calculation_form_submit_success",array($this,"submit"),10,3);
        add_filter("calculation_forms_settings_menu",array($this,"add_menu_settings"));
        add_action("calculation_forms_settings_render_mailchimp",array($this,"settings_page_render"));
        add_action( 'admin_init', array($this,'register_settings') );
    }
    function add_menu_settings($datas){
        $datas["mailchimp"] = array("label"=>__("Mailchimp","calculation-forms"));
        return $datas;
    }
    function submit($datas, $form, $form_id){
        $list = get_post_meta( $form_id, '_calculation_forms_mailchimp', true );
        if( $list != ""){
            $merge_fields = get_post_meta( $form_id, '_calculation_forms_mailchimp_merge_fields', true );
            if( is_array($merge_fields)){
                $api_key = get_option("calculation_forms_settings_mailchimp");
                $email = $datas[$merge_fields['email']];
                unset($merge_fields["email"]);
                $fields = array();
                foreach($merge_fields as $key=>$value){
                    
                    if($value != ""){
                        $fields[strtoupper($key)] = $datas[$value];
                    }
                }
                Calculation_Froms_Settings_Mailchimp_API::add_subscribed($api_key,$list,$email,$fields);
            }
        }
    }
    function register_settings(){
        if(isset($_POST['calculation_forms_settings_mailchimp']) && $_POST['calculation_forms_settings_mailchimp']){
            $key = sanitize_text_field( $_POST['calculation_forms_settings_mailchimp'] );
            $lists = Calculation_Froms_Settings_Mailchimp_API::get_list($key);
            update_option("_calculation_forms_mailchimp_lists",$lists);
        }
        register_setting( 'calculation_forms_settings_mailchimp', 'calculation_forms_settings_mailchimp' );
    }
    function settings_page_render(){
        $datas = get_option("calculation_forms_settings_mailchimp","");
        ?>
        <div class="calculation_forms_settings_title"><?php esc_html_e("Mailchimp Settings","calculation-forms") ?></div>
        <form method="post" action="options.php">
            <?php settings_fields( 'calculation_forms_settings_mailchimp' ); ?>
            <?php do_settings_sections( 'calculation_forms_settings_mailchimp' ); ?>
            <table class="form-table">
              <tr valign="top">
                    <th scope="row"><?php esc_html_e("MailChimp API Key:","calculation-forms") ?> </th>
                    <td>
                        <input name="calculation_forms_settings_mailchimp" type="text" value="<?php echo esc_attr($datas) ?>" class="regular-text">
                        <a href="https://dev.wall-f.com/calculator/?page_id=324" target="_bank" class="button button-primary"><?php esc_html_e("Find your Mailchimp API here","calculation-forms") ?></a>
                    </td>
               </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php
    }
    function add_page($post){
        $post_id = $post->ID;
        ?>
        <div class="calculation-forms-content-tab calculation-forms-content-tab-style calculation-forms-content-tab-mailchimp hidden">
            <h3><?php esc_html_e("Mailchimp","calculation-forms") ?></h3>
            <?php 
            $id = get_post_meta( $post_id, '_calculation_forms_mailchimp', true );
            $merge_fields = get_post_meta( $post_id, '_calculation_forms_mailchimp_merge_fields', true );
            if(!is_array($merge_fields)){
                $merge_fields = array("email"=>"","fname"=>"","lname"=>"","phone"=>"","address"=>"");
            }
            $api_key = get_option("calculation_forms_settings_mailchimp");
            if( $api_key != "" ){
            ?>
            <ul>
                <li>
                    <label for=""><?php esc_html_e("Chooose List","calculation-forms") ?></label>
                    <select name="calculation_forms_mailchimp">
                        <option value="0">-------</option>
                        <?php $lists = get_option("_calculation_forms_mailchimp_lists",array());
                        foreach($lists as $key => $value){
                            ?>
                            <option <?php selected($id,$key) ?> value="<?php esc_attr_e($key) ?>"><?php esc_html_e($value) ?></option>
                            <?php
                        } ?>
                    </select>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Email","calculation-forms") ?>*</label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_mailchimp_merge_fields[email]", $merge_fields["email"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("First Name","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_mailchimp_merge_fields[fname]", $merge_fields["fname"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Last Name","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_mailchimp_merge_fields[lname]", $merge_fields["lname"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Phone","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_mailchimp_merge_fields[phone]", $merge_fields["phone"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Address","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_mailchimp_merge_fields[address]", $merge_fields["address"] );
                    ?>
                </li>
            </ul>
        <?php
    }else{
        esc_html_e("Please setup API in settings","calculation-forms");
    }
    ?>
           </div>
        <?php
    }
    function save_post($post_id){
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
        update_post_meta( $post_id, '_calculation_forms_mailchimp', sanitize_text_field($_POST["calculation_forms_mailchimp"]));
         update_post_meta( $post_id, '_calculation_forms_mailchimp_merge_fields', $_POST["calculation_forms_mailchimp_merge_fields"]);
    }
    function add_tab(){
        ?>
        <li>
            <a data-tab=".calculation-forms-content-tab-mailchimp" href="#"><?php esc_html_e("Mailchip","calculation-forms") ?></a>
        </li>
        <?php
    }
}
new Calculation_Froms_Settings_Mailchimp;