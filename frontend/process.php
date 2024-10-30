<?php
global $calculation_form;
use NXP\MathExecutor;
class Calculation_Forms_Process{
	private $form;
	private $name ="";
	private $value ="";
	private $validate ="request";
	function __construct(){
		add_action( 'wp_ajax_calculation_forms', array($this,"process_ajax") );
		add_action( 'wp_ajax_nopriv_calculation_forms', array($this,"process_ajax") );
		add_action( 'admin_post_nopriv_calculation_forms', array($this,"form_submit"));
		add_action( 'admin_post_calculation_forms', array($this,"form_submit"));
		add_action( 'calculation_form_payment_success', array($this,"payment_success"),10,3);
		add_action( 'calculation_form_payment_failed', array($this,"payment_failed"));
	}
	function payment_success($submission,$total_price,$currency_code){
		update_post_meta( $submission, '_payment', "completed" );
		update_post_meta( $submission, '_payment_total', $total_price);
		update_post_meta( $submission, '_payment_currency_code', $currency_code);
	}
	function payment_failed($submission){
		update_post_meta( $submission, '_payment', "failed" );
	}
	function form_submit(){
		$result = $this->process();
		$url ="";
		if(isset($result["redirect"]) && $result["redirect"] !="" ) {
			$url = $result["redirect"];		
		}else{
			if( isset($_SERVER["HTTP_REFERER"]) ){
				$url = $_SERVER['HTTP_REFERER'];
				$url = add_query_arg(array("status"=>$result["status"],"message"=>$result["message"],"submission_id"=>$result["submission_id"]),$url);
			}
		}
		wp_safe_redirect(
			    esc_url($url)
			);	
	}
	function process_ajax(){
		$result = $this->process();
		wp_send_json_success($result);
		wp_die();
	}
	public static function cusotm_data_step($form){
		if (!isset($form[0]["name"])) {
             $form= array("name"=>"Step","next"=>"Next","prev"=>"Previous","datas"=>$datas_form);
        }
        $datas = array();
        foreach( $form as $data ){
        	foreach( $data["datas"] as $value ){ 
        			$datas[] = $value;
        	}
        }
        return $datas;
	}
	function process(){
		$emails = array();
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'calculation_add_user_meta_nonce' ) ) {
	        wp_die("Error");
	    }
	    $form_id = sanitize_text_field($_POST['form_id']);
	    $validates_check = array();
	    $datas = array();
	    $logics = array();
	    $hidden_names = array();
	    $form = get_post_meta( $form_id, '_calculation_form', true );
	   	$form = json_decode($form,true);
	   	//$form = Calculation_Forms_Process::cusotm_data_step($form);
	   	$this->form = $form;
	   	$all_names = array();
	   	$options = get_option("calculation_forms_settings",array());
	   	$validates = array("text"=>array("required"),
	   						"number"=>array("required","number"),
	   						"total"=>array("required"),
	   						"slider"=>array("required"),
	   						"date"=>array("required"),
	   						"email"=>array("required"),
	   						"checkbox"=>array("required"),
	   						"radio"=>array("required"),
	   						"select"=>array("required"),
	   						"switch"=>array("required"),
	   						"file"=>array("required","file_extension","file_size"),
	   						"email"=>array("required","email"),
	   					);
	    $validates = apply_filters("calculation_form_validates_type",$validates);
	    $total_step = count($form);
	    $current_step = sanitize_text_field($_POST["current_step"]);
	    $i_step=1;
	    foreach( $form as $tab_step ){ 
	    	if( $i_step > $current_step){
	    		break;
	    	}
		    foreach( $tab_step["datas"] as $row){
		    	foreach( $row["columns"] as $column){
		    		foreach( $column as $elements){
		    			$type = $elements["type"];
		    			if( isset($elements["data"]["name"]["value"]) && $elements["data"]["name"]["value"] !="" ){
		    				$all_names[] = array("type"=>$type,"name"=>$elements["data"]["name"]["value"]);
		    			};
		    			if( isset( $validates[$type] ) ){	
		    				if( $type == "email"){
		    					$name = $elements["data"]["name"]["value"];
		    					if( !in_array("email",$validates_check ) ){
		    						$validates_check[$elements["data"]["name"]["value"]][]= "email";
		    					}	
			    			}	
		    				foreach( $elements["data"] as $key=>$value ){
		    					if (in_array($key, $validates[$type])) {
		    						//checkbox
		    						if( $key == "required"){
		    							if( isset($value["value"]) && $value["value"] == "checked" ){
									    	$validates_check[$elements["data"]["name"]["value"]][]= $key;
									    }
		    						}else{
		    							//text
		    							$validates_check[$elements["data"]["name"]["value"]][]= $key;
		    						}
								}
		    				}
		    			}
		    			//logic
		    			if( isset($elements["data"]["logic"]["value"]) && $elements["data"]["logic"]["value"] != "" && $elements["data"]["logic"]["value"] != "uncheck"){
		    				$logics[$name = $elements["data"]["name"]["value"]] = json_decode( urldecode($elements["data"]["logic"]["value"]),true );
		    			}
		    			if( isset($elements["data"]["name"]["value"])) {
	    					$name = $elements["data"]["name"]["value"];
	    					if( isset($elements["data"]["formula"]["value"]) && $elements["data"]["formula"]["value"] !="" ) {
		    					$executor = new MathExecutor();
		    					$formula = $elements["data"]["formula"]["value"];
		    					$formula = trim( preg_replace( '/\s+/', ' ', $formula ) );
								preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $formula, $matches );
								if ( is_array( $matches[1] ) ) {
									foreach ( $matches[1] as $match ) {
										$pattern = Calculation_Forms_Process::pattern_shortcode($match);
										$value = sanitize_text_field($_POST[$match]);
										if( is_array($value)) {
											$temp = 0;
											foreach($value as $vl ){
												$temp += $vl;
											}
											$value = $temp;
										}
										$number_format = $this->get_data_element($match,"format",$form_id);
										if( $number_format == "decimal_dot"){
											$value = preg_replace( "/,/", "", $value );
										}elseif( $number_format == "decimal_comma"){
											$value = preg_replace( "/\./", "", $value );
											$value = preg_replace( "/,/", ".", $value );
										}
										if( !strpos($value,"-") ){
											preg_match_all('!\d+\.?\d+!', $value ,$match1);
											if( isset($match1[0][0]) ){
												$value = $match1[0][0];
												if(isset($match1[0][1]) ){
													$value .=".".$match1[0][1];
												}
											}
										}
										if($value == ""){
											$value = 0;
										}
										$formula = preg_replace( "/$pattern/", $value, $formula );
									}
								}
								$formula = preg_replace_callback( "/days\(([^()]*)\)/", 
									function($match2) {
										$datas = explode(",", $match2[1]);
										if( $datas[0] == "now" ){
											$day1 = $date = date('Y-m-d');
										}else{
											$day1 = $datas[0];
										}
										if( $datas[1] == "now" ){
											$day2 = $date = date('Y-m-d');
										}else{
											$day2 = $datas[1];
										}
										return $this->s_datediff("d",$day1,$day2);
									 }, 
								$formula );
								$formula = preg_replace_callback( "/years\(([^()]*)\)/", 
									function($match2) {
										$datas = explode(",", $match2[1]);
										if( $datas[0] == "now" ){
											$day1 = $date = date('Y-m-d');
										}else{
											$day1 = $datas[0];
										}
										if( $datas[1] == "now" ){
											$day2 = $date = date('Y-m-d');
										}else{
											$day2 = $datas[1];
										}
										return $this->s_datediff("y",$day1,$day2);
									 }, 
								$formula );
								$formula = preg_replace_callback( "/months\(([^()]*)\)/", 
									function($match2) {
										$datas = explode(",", $match2[1]);
										if( $datas[0] == "now" ){
											$day1 = $date = date('Y-m-d');
										}else{
											$day1 = $datas[0];
										}
										if( $datas[1] == "now" ){
											$day2 = $date = date('Y-m-d');
										}else{
											$day2 = $datas[1];
										}
										return $this->s_datediff("m",$datas[0],$datas[1]);
									 }, 
								$formula );
								$formula = str_replace(array("mod"),array("fmod"),$formula);
								$datas[$name] = apply_filters("calculation_form_data_value",$executor->execute($formula),$type,$form);
		    				}else{
		    					if( isset($_POST[$name]) ){
		    						$datas[$name] = apply_filters("calculation_form_data_value", sanitize_text_field($_POST[$name]),$type,$form);
		    					}else{
		    						$datas[$name] = apply_filters("calculation_form_data_value","",$type,$form);
		    					}
		    				}
	    				}
		    		}
		    	}
		    }
		    $i_step++;
		}
	    $data_messages = get_post_meta( $form_id, '_calculation_form_messages', true );
	    foreach( $logics as $name => $logic ){
	    	$checks = array();
	    	foreach($logic["conditional"] as $key => $conditional ){
	    		$current_value = $datas[$conditional["name"]];
	    		switch( $conditional["rule"] ){
	    			case "is":
	    				if( $current_value == $conditional["value"] ){
	    					$checks[] = true;
	    				}
	    				break;
	    			case "isnot":
	    				if( $current_value != $conditional["value"] ){
	    					$checks[] = true;
	    				}
	    				break;
	    			case ">":
	    				if( $current_value > $conditional["value"] ){
	    					$checks[] = true;
	    				}
	    				break;
	    			case "<":
	    				if( $current_value < $conditional["value"] ){
	    					$checks[] = true;
	    				}
	    				break;
	    			case "contains":
	    				if( str_contains($current_value,$conditional["value"]) ){
	    					$checks[] = true;
	    				}
	    				break;
	    			case "starts_with":
	    				if (startsWith($current_value, $conditional["value"])) {
						    $checks[] = true;
						}
	    				break;
	    			case "ends_with":
	    				if (endsWith($current_value, $conditional["value"])) {
						    $checks[] = true;
						}
	    				break;
	    		}
	    	}
	    	if( $logic["logic"] == "all" ){
	    		if( count($checks) == count($logic["conditional"])){
	    			if($logic["type"] != "show"){
	    				$hidden_names[] = $name;
	    			}
	    		}else{
	    			if($logic["type"] == "show"){
	    				$hidden_names[] = $name;
	    			}
	    		}
	    	}else{
	    		if( count($checks) > 0 ){
	    			if($logic["type"] != "show"){
	    				$hidden_names[] = $name;
	    			}
	    		}else{
	    			if($logic["type"] == "show"){
	    				$hidden_names[] = $name;
	    			}
	    		}
	    	}
	    }
	    $validate_return = array();
	    $name_files_upload = array();
	    foreach( $all_names as $element ) {
	    	$name_post = $element["name"];
	    	if( in_array($name_post,$hidden_names)){
	    		continue;
	    	}
	    	if( $element["type"] == "file"){
	    		$value_post = $_FILES[$name_post]["name"];
	    		if( is_array($value_post) ){
	    			$value_post = $value_post[0];
	    		}
	    		$name_files_upload[] = $name_post;
	    	}else{
	    		$value_post = $_POST[$name_post];
	    	}
	    	if (array_key_exists($name_post, $validates_check)) {	
	    		foreach( $validates_check[$name_post] as $validate) {
	    			switch( $validate ) {
						case "required":
							if( trim($value_post) == ""){
								$validate_return[$name_post]= $data_messages["invalid_required"];
							}
							break;
						case "number":
							if( !is_numeric($value_post)){
								$validate_return[$name_post]= $key;
							}
							break;
						case "email":
							if(!filter_var($value_post, FILTER_VALIDATE_EMAIL) && $value_post != ""){
								$validate_return[$name_post]= "email validate";
							}
							break;
						case "file_extension":
							//$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
							$file_type = $_FILES[$name_post]["size"];
							//$validate_return[$name_post]= "ok ex";
							break;
						case "file_size":
							$file_size = $_FILES[$name_post]["size"];
							break;
				    }
	    		}
			}
	    }

	    if ( $current_step == $total_step ) {
			if(isset($_POST["calculation_g_recaptcha_response"])){
				$recaptcha_response = sanitize_text_field($_POST['calculation_g_recaptcha_response']);
				$args = array(
				    'secret'   => $options["recaptcha_secret_key"],
				    'response' => $recaptcha_response,
			    );
				$gcaptcha = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array("body"=>$args) );
				$response  = wp_remote_retrieve_body( $gcaptcha );
				$arrResponse = json_decode($response, true);
				// verify the response
				if($arrResponse["success"] == '1' && $arrResponse["action"] == "submit" && $arrResponse["score"] >= 0.5) {
				} else {
				    $validate_return["submit"]= "reCaptcha validate";
				}
			}
		}
		
		//upload file
		$mail_attachments = array();
		if( count($validate_return) <= 0 ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			add_filter( 'upload_dir', 'calculation_forms_upload_dir' );
			foreach($name_files_upload as $name){
				$upload_overrides = array( 'test_form' => false );
				$files = $_FILES[$name];
				//multifile
				if( is_array($files['name']) ) {
					$rs_name=array();
					foreach ( $files['name'] as $key => $value ) {
					    if ( $files['name'][ $key ] ) {
					        $file = array(
					            'name' => $files['name'][ $key ],
					            'type' => $files['type'][ $key ],
					            'tmp_name' => $files['tmp_name'][ $key ],
					            'error' => $files['error'][ $key ],
					            'size' => $files['size'][ $key ]
					        );
					        $movefile = wp_handle_upload( $file, $upload_overrides );
					        if ( $movefile && ! isset( $movefile['error'] ) ) {
					        	$rs_name[] =  $movefile["url"];
					        	$mail_attachments[] = $movefile["file"];
							} else {
							    $validate_return[$name_post]= $movefile['error'];
							}
					    }
					}
					$datas[$name] = apply_filters("calculation_form_data_value",implode(",", $rs_name),"file",$name);
				}else{
					//single file
					$movefile = wp_handle_upload( $files, $upload_overrides );
					if ( $movefile && ! isset( $movefile['error'] ) ) {
						$datas[$name] = apply_filters("calculation_form_data_value",$movefile["url"],"file",$name);
						$mail_attachments[] = $movefile["file"];
					} else {
					    $validate_return[$name_post]= $movefile['error'];
					}
				}
			}
			remove_filter( 'upload_dir', 'calculation_forms_upload_dir' );
		}
		$data_return =array("form_id"=>$form_id);
		$validate_return = apply_filters("calculation_form_validates",$validate_return,$datas,$form,$form_id);
		//Add methods payment checkout
		if( isset($_POST["payment"]) ){
			$datas["payment"] = sanitize_text_field($_POST["payment"]);
		}
		if( count($validate_return) == 0 ){
			$data_return["step"] = true;
		}else{
			$data_return["step"] = false;
		}
		if ( $current_step < $total_step ) {
			$validate_return["step"]= $current_step;
		}
		if( count($validate_return) >0 ) {
			$data_return["redirect"] =null;
			$data_return["status"] ="validation_failed";
			$data_return["invalid_fields"] = $validate_return;
			$data_return["message"] = $data_messages["validation_error"];
			do_action("calculation_form_submit_fail", $datas, $form, $form_id);
		}else{
			$mail_attachments = apply_filters("calculation_form_mail_attachments",$mail_attachments,$datas, $form, $form_id);
			//Save submissions
			$data_submit = apply_filters("calculation_form_before_submit", $datas, $form, $form_id,$mail_attachments);
			if( isset($data_submit["email_disable"])){
				$data_email = apply_filters("calculation_form_email_disable", $datas, $form, $form_id,$mail_attachments);
			}else{
				$data_email = apply_filters("calculation_form_send_email_submit", $datas, $form, $form_id,$mail_attachments);
			}
			$data_return["status"] ="success";
			$data_return["message"] = $data_messages["mail_sent_ok"];
			$data_return = array_merge($data_submit,$data_return);
			do_action("calculation_form_submit_success", $datas, $form, $form_id);
		}
		return $data_return;
	}
	function startsWith( $haystack, $needle ) {
	     $length = strlen( $needle );
	     return substr( $haystack, 0, $length ) === $needle;
	}
	function endsWith( $haystack, $needle ) {
	    $length = strlen( $needle );
	    if( !$length ) {
	        return true;
	    }
	    return substr( $haystack, -$length ) === $needle;
	}
	public static function get_data_element($name,$data, $form_id= null){
		if( $form_id ){
			$form = get_post_meta( $form_id, '_calculation_form', true );
	   		$form = json_decode($form,true);
	   		//$form = Calculation_Forms_Process::cusotm_data_step($form);
		}else{
			$form = $this->form;
		}
		foreach( $form as $tab_step ){ 	
			foreach( $tab_step["datas"]  as $row){
		    	foreach( $row["columns"] as $column){
		    		foreach( $column as $elements){
		    			if(isset($elements["data"]["name"]["value"])){
		    				if($elements["data"]["name"]["value"] == $name){
		    					if( isset( $elements["data"][$data]["value"] )){
		    						return $elements["data"][$data]["value"];
		    					}
		    				}
		    			}
		    		}
		    	}
		    }
		}
		return "";
	}
	public static function get_data_element_type($name,$form){
		foreach( $form as $tab_step ){ 	
			foreach( $tab_step["datas"]  as $row){
		    	foreach( $row["columns"] as $column){
		    		foreach( $column as $key => $elements){
		    			if(isset($elements["data"]["name"]["value"])){
		    				if($elements["data"]["name"]["value"] == $name){
		    					return $elements["type"];
		    				}
		    			}
		    		}
		    	}
		    }
		}
		return "";
	}
	function validate() {
		$value = $this->value;
		foreach( $this->validate as $validate ){
		}
		return true;
	}
	public static function pattern_shortcode($match){
		$pattern = '\\['                             // Opening bracket.
								. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
								. "($match)"                     // 2: Shortcode name.
								. '(?![\\w-])'                       // Not followed by word character or hyphen.
								. '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
								.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
								.     '(?:'
								.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
								.         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
								.     ')*?'
								. ')'
								. '(?:'
								.     '(\\/)'                        // 4: Self closing tag...
								.     '\\]'                          // ...and closing bracket.
								. '|'
								.     '\\]'                          // Closing bracket.
								.     '(?:'
								.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
								.             '[^\\[]*+'             // Not an opening bracket.
								.             '(?:'
								.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
								.                 '[^\\[]*+'         // Not an opening bracket.
								.             ')*+'
								.         ')'
								.         '\\[\\/\\2\\]'             // Closing shortcode tag.
								.     ')?'
								. ')'
								. '(\\]?)';
		return $pattern;
	}
	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    $interval = date_diff($datetime1, $datetime2);
    return $interval->format($differenceFormat);
}
function s_datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){
       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);
       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
       switch( $str_interval){
           case "y":
               $total = date_format($dt_menor,"Y") - date_format($dt_maior,"Y");
               break;
           case "m":
               $total= date_format($dt_menor,"m") - date_format($dt_maior,"m")  + ( 12 * (date_format($dt_menor,"Y") - date_format($dt_maior,"Y")));
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h":
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i":
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s":
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
   }
}
new Calculation_Forms_Process;