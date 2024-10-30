<?php 
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
class Calculation_Forms_Paypal{
	function __construct(){
		add_filter("calculation_form_submissions_success",array($this,"redirect"),10,3);
		add_action("init",array($this,"paypal_process_ipn"));
		add_filter("calculation_form_payment_methods",array($this,"add_methods"));
		add_action("calculation_forms_settings_payment",array($this,"page_settings"));
	}
	function page_settings($datas){
		$paypal_client ="";
		$paypal_secret_key ="";
		$paypal_sandbox ="";
		if( isset($datas["paypal_client"])){
			$paypal_client = $datas["paypal_client"];
		}
		if( isset($datas["paypal_secret_key"])){
			$paypal_secret_key = $datas["paypal_secret_key"];
		}
		if( isset($datas["paypal_sandbox"])){
			$paypal_sandbox = $datas["paypal_sandbox"];
		}
		?>
		 <h3><?php esc_html_e("Paypal Settings","calculation-forms") ?></h3>
		    <table class="form-table">
		      <tr valign="top">
			        <th scope="row"><?php esc_html_e("Client ID","calculation-forms") ?> </th>
			        <td>
			        	<input name="calculation_forms_settings[paypal_client]" type="text" value="<?php echo esc_attr($paypal_client) ?>" class="regular-text">
			        	<a href="https://developer.paypal.com/developer/accounts" target="_blank"><?php esc_html__("Get ID","calculation-forms") ?></a>
			        </td>
		       </tr>
		       <tr valign="top">
			        <th scope="row"><?php esc_html_e("Secret Key","calculation-forms") ?> </th>
			        <td>
			        	<input name="calculation_forms_settings[paypal_secret_key]" type="text" value="<?php echo esc_attr($paypal_secret_key) ?>" class="regular-text">
			        </td>
		       </tr>
               <tr valign="top">
			        <th scope="row"><?php esc_html_e("Sandbox","calculation-forms") ?> </th>
			        <td>
			        	<input <?php checked( "ok" , $paypal_sandbox )?> name="calculation_forms_settings[paypal_sandbox]" type="checkbox" value="ok" >
			        </td>
		       </tr>
		    </table>
		<?php
	}
	function add_methods($data){
        $data["paypal"]=__("Paypal","calculation-forms");
        return $data;
    }
	function paypal_process_ipn(){
		if (!empty($_GET['calculation_paypal_ipn']) && $_GET['calculation_paypal_ipn'] == '1') {
			$settings = get_option("calculation_forms_settings");
			$message ="";
			if( $settings["paypal_sandbox"] == "ok" ){
				$environment = new SandboxEnvironment($settings["paypal_client"], $settings["paypal_secret_key"]);
			}else{
				$environment = new ProductionEnvironment($settings["paypal_client"], $settings["paypal_secret_key"]);
			}
			$client = new PayPalHttpClient($environment);
			if( isset($_GET['token']) ){
				$token = sanitize_text_field($_GET['token']);
				$request = new OrdersCaptureRequest($token);
				$request->prefer('return=representation');
				try {
				    // Call API with your client and get a response for your call
				    $response = $client->execute($request);
				    if( $response->result->status == "COMPLETED" ){
				    	$product_id = $response->result->purchase_units[0]->reference_id;
				    	$currency_code = $response->result->purchase_units[0]->payments->captures[0]->amount->currency_code;
				    	$total_price = $response->result->purchase_units[0]->payments->captures[0]->amount->value;
				    	$form_id = get_post_meta($product_id,"_form_id",true);
				    	$paypal = get_post_meta($form_id,"_calculation_form_paypal",true);
				    	if( is_numeric($paypal["price"])){
				    		$price = $paypal["price"];
				    	}else{
				    		$name_price = $paypal["price"];
				    		$content_post = get_post($product_id);
							$content = $content_post->post_content;
					    	$datas = json_decode($content,true);
					    	$price = $datas[$name_price];
				    	}
				    	if( $total_price >=  $price ){
				    		do_action("calculation_form_payment_success",$product_id,$total_price,$currency_code);
				    		$message .= "<h3>Payment success</h3>";
				    		$mail_sent_ok = get_post_meta($form_id,"_calculation_form_messages",true);
				    		$message .= $mail_sent_ok["mail_sent_ok"];
				    	}else{
				    		do_action("calculation_form_payment_failed",$product_id);
				    		$message .= "<h3>Payment failed</h3>";
				    		$message .= "You Pay: ". $total_price.$currency_code."<br>";
				    		$message .= "Form Pay: ". $price .$currency_code;
				    	}
				    }
				}catch (HttpException $ex) {
					do_action("calculation_form_payment_failed",$product_id);
				     $message = "ERROR Paypal";
				}
			}
			$message ='<div class="calculation-forms-response-message">'.$message.'</div>';
			add_action('calcucation_form_after_form', 
	           function() use ( $message ) { 
	              	printf($message);
	               });
		}
	}
	function redirect($form_id, $submission_id, $datas_form){
		$paypal = get_post_meta( $form_id, '_calculation_form_paypal', true );
		if( $paypal["price"]==""){
			return;
		}
		if( isset($datas_form["payment"]) && $datas_form["payment"] != "paypal"){
			return;
		}
		$settings = get_option("calculation_forms_settings");
		$data = array();
		if( $settings["paypal_sandbox"] == "ok" ){
			$environment = new SandboxEnvironment($settings["paypal_client"], $settings["paypal_secret_key"]);
		}else{
			$environment = new ProductionEnvironment($settings["paypal_client"], $settings["paypal_secret_key"]);
		}
		$currency = $settings["currencies"];
		if( isset($_POST["post_id"]) && $_POST["post_id"] != "" ){
			$return = get_page_link($_POST["post_id"]);
		}else{
			$return = get_home_url();
		}
		$id = "id";
		$name = get_the_title($form_id);
		if( is_numeric($paypal["price"]) ){
			$price = $paypal["price"];
		}else{
			if( !isset($datas_form[$paypal["price"]])){
				return "";
			}else{
				$price = $datas_form[$paypal["price"]];
			}
		}
		if( !is_numeric($price) ) {
			$price = 0;
		}
		$client = new PayPalHttpClient($environment);	
		$request = new OrdersCreateRequest();
		$request->prefer('return=representation');
		$request->body = [
		                     "intent" => "CAPTURE",
		                     "purchase_units" => [[
		                         "reference_id" => $submission_id,
		                         "amount" => [
		                             "value" => $price,
		                             "currency_code" => $currency
		                         ]
		                     ]],
		                     "application_context" => [
		                          "cancel_url" => add_query_arg("calculation_paypal_ipn",2,$return),
		                          "return_url" => add_query_arg("calculation_paypal_ipn",1,$return),
		                     ] 
		                 ];
		try {
		    // Call API with your client and get a response for your call
		    $response = $client->execute($request);
		    $checkoutUrl = $response->result->links[1]->href;
		}catch (HttpException $ex) {
		}
		update_post_meta( $submission_id, '_payment', "pending payment" );
        return $checkoutUrl;
	}
}
new Calculation_Forms_Paypal;