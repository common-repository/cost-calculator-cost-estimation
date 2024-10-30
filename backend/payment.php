<?php
class Calculation_Froms_Payment_Setiings {
    function __construct(){
        add_action("calculation_forms_tab",array($this,"add_tab"));
        add_action("calculation_forms_tab_page",array($this,"add_page"));
        add_action("save_post",array($this,"save_post"));
    }
    
    function add_tab(){
        ?>
        <li>
            <a data-tab=".calculation-forms-content-tab-paypal" href="#"><?php esc_html_e("Payment","calculation-forms") ?></a>
        </li>
        <?php
    }
    function add_page($post){
        $post_id = $post->ID;
        ?>
        <div class="calculation-forms-content-tab calculation-forms-content-tab-paypal hidden">
            <h3><?php esc_html_e("Payment","calculation-forms") ?></h3>
            <?php 
            $data_paypal = get_post_meta( $post_id, '_calculation_form_paypal', true );
            if (  empty( $data_paypal ) ) {
                $data_paypal = array("price"=>"","quantiy"=>1);
            }
            ?>
            <ul>
                <li>
                    <label for=""><?php esc_html_e("Price Field (required)","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("cf_paypal[price]", $data_paypal["price"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Quantity","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("cf_paypal[quantiy]", $data_paypal["quantiy"] );
                    ?>
                </li>
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
        $paypal = array();
        
        if( isset( $_POST['cf_paypal'] ) && is_array($_POST['cf_paypal'])){
            foreach( $_POST['cf_paypal'] as $key => $value ) { 
                $paypal[$key] = sanitize_text_field($value); 
            }
        }
        update_post_meta( $post_id, '_calculation_form_paypal', $paypal );
    }
}
new Calculation_Froms_Payment_Setiings;