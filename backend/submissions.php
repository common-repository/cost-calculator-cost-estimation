<?php 
class Calculation_Forms_Submissions{
    function __construct(){
        add_filter("calculation_form_before_submit",array($this,"save_data"),100,4);
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post',array( $this, 'save' ) );
        add_filter( 'manage_cf_submissions_posts_columns',  array($this,"add_colunms"),100,1);
        add_action( 'manage_cf_submissions_posts_custom_column' , array($this,'custom_column'), 10, 2 );
        add_action( 'restrict_manage_posts', array($this,'add_admin_filters'), 10, 1 );
        add_filter( 'parse_query', array($this,'filter_request_query') , 10);
    }
    public function add_admin_filters( $post_type ){
        if( 'cf_submissions' !== $post_type ){
            return;
        }
        ?>
        <select name="calculation_forms_id">
            <option value="0"><?php esc_html_e("All Forms","calculation-forms") ?></option>
            <?php
                $query = new WP_Query( array( "post_type"=>"calculation_forms",'posts_per_page' => -1 ) );
                while ( $query->have_posts() ) {
                $query->the_post();
                $selected = "";
                if( isset($_REQUEST['calculation_forms_id'])) {
                    $selected = selected(sanitize_key($_REQUEST['calculation_forms_id']),get_the_ID(),false);
                }
                ?>
                <option <?php echo esc_html($selected) ?> value="<?php the_ID() ?>"><?php the_title() ?></option>
                <?php 
            }
                wp_reset_postdata();
             ?>
        </select>
        <?php  
    }
    function filter_request_query($query){
        if( !(is_admin() AND $query->is_main_query()) ){ 
          return $query;
        }
        //we want to modify the query for the targeted custom post and filter option
        if( !('cf_submissions' === $query->query['post_type'] AND isset($_REQUEST['calculation_forms_id']) ) ){
          return $query;
        }
        //for the default value of our filter no modification is required
        if(0 == $_REQUEST['calculation_forms_id']){
          return $query;
        }
        if( isset($_REQUEST['calculation_forms_id'])) {
           $query->query_vars['meta_key'] = '_form_id';
             $query->query_vars['meta_value'] = sanitize_key( $_REQUEST['calculation_forms_id'] );
             $query->query_vars['meta_compare'] = '='; 
        }
        return $query;
      }
    function add_colunms($columns) {
        global $post;
        $columns['payment'] = esc_html__( 'Status Payment', "calculation-forms" );
        $columns['form'] = esc_html__( 'Form', "calculation-forms" );
        return $columns;
    }
    function custom_column( $column, $post_id ) {
        //var_dump($column)
        switch ( $column ) {
            case 'payment' :
                $payment = get_post_meta( $post_id , '_payment' , true ); 
                esc_html_e($payment,"calculation-forms");
                break;
            case 'form' :
                $form_id = get_post_meta( $post_id , '_form_id' , true ); 
                echo esc_html( get_the_title($form_id));
                break;
        }
    }
    function save_data($datas,$form, $form_id, $mail_attachments){
        $title ="";
        $data_return =array();
        $datas["_form_id"] = $form_id;
        foreach( $datas as $data ){
           $title .= $data;
           break;
        }
        $post_id = wp_insert_post(array(
            "post_title"=>$title,
            "post_content"=>json_encode($datas,JSON_UNESCAPED_UNICODE ),
            'post_status'   => 'publish',
            'post_type' => 'cf_submissions'
        ));
        $data_return["submission_id"] = $post_id;
        update_post_meta( $post_id, '_form_id', $form_id );
        update_post_meta( $post_id, '_form_id_mail_attachments', $mail_attachments );
        if( isset($datas["payment"]) && $datas["payment"] == "stripe"){
            update_post_meta( $post_id, '_payment', "completed" );
        }
        $redirect = apply_filters("calculation_form_submissions_success",$form_id, $post_id, $datas,$mail_attachments);
        $data_return["redirect"] = $redirect;
        if (!filter_var($redirect, FILTER_VALIDATE_URL) === FALSE) {
            $data_return["email_disable"] = true;
        }
        return $data_return;
    }
    function add_meta_boxes() {
        add_meta_box(
            'cf_submissions_data',
            esc_html__( 'Datas', 'calculation-forms' ),
            array( $this, 'cf_submissions' ),
            'cf_submissions',
            'normal',
            'default'
        );
    }
    function cf_submissions($post){
        $datas = json_decode($post->post_content,true);
        $post_id = $post->ID;
        wp_nonce_field( 'calculation_forms_submissions_box_nonce', 'calculation_forms_submissions_box_nonce' );
        ?>
        <div>
            <ul>
                <?php foreach($datas as $k=>$v) { 
                    $class ="";
                    if($k == "_form_id"){
                        $class ="hidden";
                    }
                    ?>
                <li class="<?php echo esc_attr($class) ?>">
                    <div class="cf_submissions-lable">
                        <?php 
                        $lable = Calculation_Forms_Process::get_data_element($k,"label",$datas["_form_id"]);
                        echo esc_html( $lable ); ?>
                    </div>
                    <div class="cf_submissions-content-">
                        <?php 
                            if(is_array($v)){
                                foreach($v as $vl){
                                ?>
                            <input type="checkbox" checked name="cf_submissions[<?php echo esc_attr($k) ?>][]" value="<?php echo esc_html( $vl ) ?>"> <?php echo esc_html( $vl ) ?> 
                                <?php
                                }
                            }else{
                                ?>
                            <input type="text" name="cf_submissions[<?php echo esc_attr($k) ?>]" value="<?php echo esc_html( $v ) ?>">  
                                <?php
                            }
                        ?>
                    </div>
                </li>
            <?php }
                $payment = get_post_meta( $post_id , '_payment' , true );
                if( isset($payment) && $payment != ""){
                    $total = get_post_meta( $post_id , '_payment_total' , true ); 
                    $currency = get_post_meta( $post_id , '_payment_currency_code' , true ); 
                ?>
                <li class="">
                    <div class="cf_submissions-lable">
                       <?php esc_html_e("Status Payment","calculation-forms") ?>
                    </div>
                    <div class="cf_submissions-content-">
                            <select name="payment[_payment]">
                                <option value="pending payment"><?php esc_html_e("Pending payment","calculation-forms") ?></option>
                                <option <?php selected($payment,"completed") ?> value="completed"><?php esc_html_e("Completed","calculation-forms") ?></option>
                                <option <?php selected($payment,"failed") ?> value="failed"><?php esc_html_e("Failed","calculation-forms") ?></option>
                            </select>
                    </div>
                </li>
                <li class="">
                    <div class="cf_submissions-lable">
                       <?php esc_html_e("Total","calculation-forms") ?>
                    </div>
                    <div class="cf_submissions-content-">
                            <input type="text" name="payment[_payment_total]" value="<?php echo esc_html( $total ) ?>">
                    </div>
                </li>
                <li class="">
                    <div class="cf_submissions-lable">
                       <?php esc_html_e("Currency","calculation-forms") ?>
                    </div>
                    <div class="cf_submissions-content-">
                        <input type="text" name="payment[_payment_currency_code]" value="<?php echo esc_html( $currency ) ?>">
                    </div>
                </li>
            <?php
                }
             ?>
            </ul>
        </div>
        <?php
    }
     public function save( $post_id ) {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['calculation_forms_submissions_box_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['calculation_forms_submissions_box_nonce'];
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'calculation_forms_submissions_box_nonce' ) ) {
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
        $cf_submissions = array();
        if( isset($_POST['payment'] ) && is_array($_POST['payment'])){
            foreach( $_POST['payment'] as $key=>$value){
                $value = sanitize_text_field($value);
                $key = sanitize_key($key);
                update_post_meta( $post_id, $key, $value );
            }
        }
        if( isset($_POST['cf_submissions']) && is_array($_POST['cf_submissions'] )){
            foreach($_POST['cf_submissions'] as $key=>$value){
                $key = sanitize_key($key); 
                $cf_submissions[$key] = sanitize_text_field($value);
            }
        }
        $cf_submissions = json_encode($cf_submissions,JSON_UNESCAPED_UNICODE );
        remove_action('save_post', array($this,"save"));
        wp_update_post(array("post_content"=>$cf_submissions,"ID"=>$post_id)); 
        add_action('save_post', array($this,"save"));       
    }
}
new Calculation_Forms_Submissions;
