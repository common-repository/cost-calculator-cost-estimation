<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
if(!class_exists('Rednumber_Check_Purchase_Code')){
	class Rednumber_Check_Purchase_Code {
		protected $data;
		public function __construct( $data ) { 
			$defaults = array(
				"plugin"=>false,
				"id"=>false,
				"bundle"=>false,
			);
			$args = wp_parse_args( $data, $defaults );

			$this->data = $args;	
			add_filter( 'plugin_action_links_' . $this->data["plugin"] , array( $this, 'add_action' ) );
			add_action( 'wp_ajax_rednumber_check_purchase_code', array($this,'check_purchase_code_ajax') );
			add_action( 'wp_ajax_rednumber_check_purchase_code_remove', array($this,'check_purchase_remove_code_ajax') );
			add_action('admin_enqueue_scripts', array($this,'add_js'));
		}
		function add_js(){
			wp_enqueue_script('rednumber_check_purchase_code', plugins_url('rednumber_check_purchase_code.js', __FILE__),array("jquery"));
		}
		function add_action($links){
			$check = get_option( '_redmuber_item_'.$this->data["id"] );
			$class_1 = "";
			$class_2 = "";
			if( $check =="ok" ){
				$class_1 = "hidden";
			}else{
				$class_2 = "hidden";
			}
			$mylinks = array(
			        '<div class="rednumber-purchase-container rednumber-purchase-container_form '.$class_1.'">'.esc_html__("Purchase Code:","rednumber").' <input data-id="'.$this->data["id"].'" type="text"><a href="#" class="button button-primary rednumber-active">'.esc_html__("Active","rednumber").'</a></div>
			         <div class="rednumber-purchase-container rednumber-purchase-container_show '.$class_2.'">Purchased: '.get_option( '_redmuber_item_'.$this->data["id"]."_code" ).' <a data-id="'.$this->data["id"].'" href="#" class="rednumber-remove">'.esc_html__("Remove","rednumber").'</a></div>',
			    );
		    return array_merge( $links, $mylinks );
		}
		function check_purchase_code_ajax(){
			$code = sanitize_text_field($_POST["code"]);
			$id = sanitize_text_field($_POST["id"]);
			$status = $this->check_purchase_code($code,$id);
			if( $status == "ok"){
				update_option( '_redmuber_item_'.$id, "ok" );
				update_option( '_redmuber_item_'.$id."_code", $code );
			}
			echo esc_attr($status);
			die();
		}
		function check_purchase_remove_code_ajax(){
			$id = sanitize_text_field($_POST["id"]);
			delete_option('_redmuber_item_'.$id);
			delete_option('_redmuber_item_'.$id."_code");
			die();
		}
		function check_purchase_code($code,$id_item){
			$personalToken = "uzAMx8rZ3FRV0ecu8t1pXNWG0d0NA6qL";
			$userAgent = "Purchase code verification";
			
			if (!preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code)) {
			    return "Invalid code";
			}
			$ch = curl_init();
			curl_setopt_array($ch, array(
			    CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_TIMEOUT => 20,
			    
			    CURLOPT_HTTPHEADER => array(
			        "Authorization: Bearer {$personalToken}",
			        "User-Agent: {$userAgent}"
			    )
			));
			$response = curl_exec($ch);
			if (curl_errno($ch) > 0) { 
			    return "Error connecting to API: " . curl_error($ch);
			}
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($responseCode === 404) {
			    return "The purchase code was invalid";
			}
			if ($responseCode !== 200) {
			    return "Failed to validate code due to an error: HTTP {$responseCode}";
			}
			$body = json_decode($response);
			if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
			    return "Error parsing response";
			}
			$id = $body->item->id; 
			if( $id == $id_item){
				return "ok";
			}else{
				if( $this->data["bundle"] == true && $id == "20405247" ){
					return "ok";
				}else{
					return "The purchase code was invalid";
				}
				
			}
		}
	}
}