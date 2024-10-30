<?php
class Calculation_Froms_Settings_Zapier {
    function __construct(){
        add_action("calculation_forms_tab_settings",array($this,"add_tab"));
        add_action("calculation_forms_tab_page",array($this,"add_page"));
        add_action("save_post",array($this,"save_post"));
        add_action("calculation_form_submit_success",array($this,"submit"),10,3);
    }
    function submit($datas, $form, $form_id){
        $url = get_post_meta( $form_id, '_calculation_forms_zapier', true );
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            wp_remote_post($url, array('body' => $datas));
        }
    }
    function add_page($post){
        $post_id = $post->ID;
        ?>
        <div class="calculation-forms-content-tab calculation-forms-content-tab-style calculation-forms-content-tab-zapier hidden">
            <h3><?php esc_html_e("Zapier","calculation-forms") ?></h3>
            <?php 
            $datas = get_post_meta( $post_id, '_calculation_forms_zapier', true );
            if (  empty( $datas ) ) {
                $datas = "";
            }
            ?>
            <ul>
                <li>
                    <label for=""><?php esc_html_e("Webhook URL","calculation-forms") ?></label>
                    <input type="text" name="calculation_forms_zapier" value="<?php echo esc_url($datas) ?>">
                </li>
            </ul>
            </ul>
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
        update_post_meta( $post_id, '_calculation_forms_zapier', sanitize_text_field($_POST["calculation_forms_zapier"]));
    }
    function add_tab(){
        ?>
        <li>
            <a data-tab=".calculation-forms-content-tab-zapier" href="#"><?php esc_html_e("Zapier","calculation-forms") ?></a>
        </li>
        <?php
    }
}
new Calculation_Froms_Settings_Zapier;