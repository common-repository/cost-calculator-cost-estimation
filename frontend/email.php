<?php
class Calculation_Forms_Email {
	private $form_id;
	private $datas;
	function __construct(){
		add_filter("calculation_form_send_email_submit",array($this,"send"),10,4);
		add_action("calculation_form_payment_success",array($this,"send_after"));
	}
	function send($datas, $form, $form_id,$mail_attachments){
		$this->form_id = $form_id;
		$this->datas = $datas;
		$emails = get_post_meta($form_id,"_calculation_form_mails",true);
		//var_dump($emails);
		$emails = $this->calcalation_shortcode_to_text_wp($emails);
		$headers = array('Content-Type: text/html; charset=UTF-8');
		if($emails["from"] != ""){
			$headers[] = 'From: '.$emails["from"];
		}
		$headers[] = $emails["headers"];
		if (filter_var($emails["to"], FILTER_VALIDATE_EMAIL)) {

		  wp_mail($emails["to"],$emails["subject"],$this->template($emails["body"]),$headers,$mail_attachments);
		}
	}
	function send_after($submission_id){
		$content_post = get_post($submission_id);
		$content = $content_post->post_content;
    	$datas = json_decode($content,true);
    	$form_id = get_post_meta($submission_id,"_form_id",true);
    	$mail_attachments = get_post_meta($submission_id,"_form_id_mail_attachments",true);
    	$this->send($datas,null,$form_id,$mail_attachments);
	}
	function calcalation_shortcode_to_text_wp($emails){
		$new_emails = [];
		$shortcodes = [];
		$all ="";
		$shortcodes["_site_admin_email"] = get_option("admin_email");
		$shortcodes["_site_title"] = get_option("blogname");
		$shortcodes["_site_url"] = get_option("siteurl");
		$shortcodes["_user_ip"] = $this->get_the_user_ip();
		$pattern = get_shortcode_atts_regex();
		$datas = $this->datas;
		foreach( $datas as $k=>$v ){
			$label = Calculation_Forms_Process::get_data_element($k,"label",$this->form_id);
			if( is_array($v) ){
				$all .= '<div>'.$label."</div><div>".implode("|",$v)."</div>";
			}else{
				$all .= '<div>'.$label."</div><div>".$v."</div>";
			}
		}
		$shortcodes["all"] = $all;
		$shortcodes = array_merge($shortcodes,$datas);
		foreach($emails as $key => $text ){
			preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $text, $matches );
			if ( is_array( $matches[1] ) ) {
				foreach ( $matches[1] as $match ) {
					$pattern = Calculation_Forms_Process::pattern_shortcode($match);
					$value = "";
					if( isset($shortcodes[$match])){
						$value = $shortcodes[$match];
						if( is_array($value) ){ 
							$text = preg_replace( "/$pattern/", implode("|",$value), $text );
						}else{
							$text = preg_replace( "/$pattern/", $value, $text );
						}
					}
				}
			}
			$new_emails[$key]= $text;
		}
		return $new_emails;
	}
	function get_the_user_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	        if (array_key_exists($key, $_SERVER) === true) {
	            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
	                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
	                    return $ip;
	                }
	            }
	        }
	    }
	}
	function template($data){
		$email = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Demystifying Email Design</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body'.$data.'</body><html>';
		return $email;
	}
}
new Calculation_Forms_Email();