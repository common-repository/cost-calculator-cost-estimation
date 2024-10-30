<?php 
class Calculation_Forms_Coupons {
	function __construct(){
		add_action( 'init', array($this,'custom_post_type') );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post',array( $this, 'save' ) );
	}
	function custom_post_type() {
        register_post_type('cf_coupon',
            array(
                'labels'      => array(
                    'name'          => esc_html__( 'Coupons', 'calculation-forms' ),
                    'singular_name' => esc_html__( 'Coupons', 'calculation-forms' ),
                ),
                'public'      => true,
                'has_archive' => true,
                'rewrite'     => array( 'slug' => 'calculation-forms-coupon' ),
                'supports'    =>array('title'),
                'show_in_menu'=> "edit.php?post_type=calculation_forms"
            )
        );
    }
    function add_meta_boxes() {
        add_meta_box(
            'cf_coupon_data',
            esc_html__( 'Coupons', 'calculation-forms' ),
            array( $this, 'cf_submissions' ),
            'cf_coupon',
            'normal',
            'default'
        );
    }
    function cf_submissions($post){
        $post_id = $post->ID;
        $coupons = get_post_meta( $post_id , '_calculation_form_coupon' , true );
        if(!is_array($coupons)){
        	$coupons = array("type"=>"fixed","amount"=>0,"limit"=>100);
        }
        wp_nonce_field( 'calculation_forms_coupon_box_nonce', 'calculation_forms_coupon_box_nonce' );
        ?>
        <div>
        	<div class="cf-coupon-conatiner">
        		<div class="cf-coupon-title">
        			<?php esc_html_e("Discount type","calculation-forms") ?>
        		</div>
        		<div class="cf-coupon-input">
        			<select name="calculation_form_coupon[type]">
        				<option value="fixed"><?php esc_html_e("Fixed Discount","calculation-forms") ?></option>
        				<option <?php selected($coupons["type"],"percentage") ?> value="percentage"><?php esc_html_e("Percentage Discount","calculation-forms") ?></option>
        			</select>
        		</div>
        	</div>
        	<div class="cf-coupon-conatiner">
        		<div class="cf-coupon-title">
        			<?php esc_html_e("Coupon amount","calculation-forms") ?>
        		</div>
        		<div class="cf-coupon-input">
        			<input type="number" name="calculation_form_coupon[amount]" value="<?php echo esc_attr($coupons["amount"]) ?>">
        		</div>
        	</div>
        	<div class="cf-coupon-conatiner">
        		<div class="cf-coupon-title">
        			<?php esc_html_e("Coupon amount","calculation-forms") ?>
        		</div>
        		<div class="cf-coupon-input">
        			<input type="number" name="calculation_form_coupon[limit]" value="<?php echo esc_attr($coupons["limit"]) ?>">
        		</div>
        	</div>
        </div>
        <?php
    }
         public function save( $post_id ) {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['calculation_forms_coupon_box_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['calculation_forms_coupon_box_nonce'];
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'calculation_forms_coupon_box_nonce' ) ) {
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


        if( isset($_POST['calculation_form_coupon']) && is_array($_POST['calculation_form_coupon'] )){
            $cf_coupon = array();
            foreach($_POST['calculation_form_coupon'] as $key=>$value){
                $key = sanitize_text_field($key); 
                $cf_coupon[$key] = sanitize_text_field($value);
            }
            update_post_meta( $post_id, "_calculation_form_coupon", $cf_coupon );
        }
     
    }
}
new Calculation_Forms_Coupons;