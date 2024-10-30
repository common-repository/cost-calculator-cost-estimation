<?php
class Calculation_Froms_Settings_Stripe {
	function __construct(){
		add_action("calculation_forms_settings_payment",array($this,"page_settings"));
		add_action("calculation_form_render_element_js",array($this,"calculation_form_render_js") );
		add_filter("calculation_form_payment_methods",array($this,"add_methods"));
		add_filter("calculation_form_validates",array($this,"stripe_proces"),10,4);
	}
	function stripe_proces($calculation_form_validates,$datas,$form,$form_id){

		if( isset($_POST["payment"]) && $_POST["payment"] =="stripe") {
			if( isset($_POST["strip_token"]) && $_POST["strip_token"] != "" ){
				$token = sanitize_text_field($_POST["strip_token"]);
				$settings = get_option("calculation_forms_settings");
				$paypal = get_post_meta($form_id,"_calculation_form_paypal",true);
				if( is_numeric($paypal["price"])){
		    		$price = $paypal["price"];
		    	}else{
		    		$name_price = $paypal["price"];
			    	$price = $datas[$name_price];
		    	}
		    	$price = $price*100;
				$key = $settings["stripe_secret_key"];
				$stripe = new \Stripe\StripeClient($key);
				$charge = $stripe->charges->create(
				    array(
				        'amount' => $price,
				        'currency' => $settings["currencies"],
				        'source' => $token
				    )
				);
				if(  $charge->status == "succeeded"){

				}else{
					$calculation_form_validates["payment"] = "Payment Error";
				}
			}else{
				$calculation_form_validates["payment"] = "Payment required";
			}
		}
		return $calculation_form_validates;
	}
	function add_methods($data){
        $data["stripe"]=__("Stripe","calculation-forms");
        return $data;
    }
    function calculation_form_render_js($type){
		switch($type){
			case "payment":
				$options = get_option("calculation_forms_settings");
				if( isset($options["stripe_key"]) && $options["stripe_key"] !=""){
					wp_enqueue_script("stripe","https://js.stripe.com/v3/",array("jquery"),"3.0");
					wp_enqueue_script("calculation_stripe",CALCULATION_FORMS_PLUGIN_URL ."modules/stripe/stripe-payment.js",array("jquery"));
					$settings = get_option("calculation_forms_settings",array());
					if( isset($settings["stripe_key"])){
						wp_localize_script("calculation_stripe",'calculation_stripe',array("key"=>$settings["stripe_key"]));
					}
					
				}
				break;
		}
	}
	function page_settings($datas){
		$stripe_key ="";
		$stripe_secret_key ="";
		if( isset($datas["stripe_key"])){
			$stripe_key = $datas["stripe_key"];
		}
		if( isset($datas["stripe_secret_key"])){
			$stripe_secret_key = $datas["stripe_secret_key"];
		}
		?>
		 <h3><?php esc_html_e("Stripe Settings","calculation-forms") ?></h3>
		    <table class="form-table">
		      <tr valign="top">
			        <th scope="row"><?php esc_html_e("Client ID","calculation-forms") ?> </th>
			        <td>
			        	<input name="calculation_forms_settings[stripe_key]" type="text" value="<?php echo esc_attr($stripe_key) ?>" class="regular-text">
			        	<a href="https://dashboard.stripe.com/apikeys" target="_blank"><?php esc_html__("Get key","calculation-forms") ?></a>
			        </td>
		       </tr>
		       <tr valign="top">
			        <th scope="row"><?php esc_html_e("Secret Key","calculation-forms") ?> </th>
			        <td>
			        	<input name="calculation_forms_settings[stripe_secret_key]" type="text" value="<?php echo esc_attr($stripe_secret_key) ?>" class="regular-text">
			        </td>
		       </tr>
		    </table>
		<?php
	}
}
new Calculation_Froms_Settings_Stripe;