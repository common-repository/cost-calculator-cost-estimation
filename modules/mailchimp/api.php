<?php
class Calculation_Froms_Settings_Mailchimp_API {
	function __construct(){
	}
	public static function get_list($api_key){
		$dc = substr( $api_key, strpos( $api_key, '-' ) + 1 ); // datacenter, it is the part of your api key - us5, us8 
		$args = array(
		 	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )
			)
		);
		$response = wp_remote_get( 'https://'.$dc.'.api.mailchimp.com/3.0/lists/?count=9999', $args );
		$body =  wp_remote_retrieve_body( $response );
		$datas = json_decode( $body,true ) ;
		$lists = array();
		foreach( $datas["lists"] as $list ){
			$lists[$list["id"]] = $list["name"] ." (". $list["id"] .")";
		}
		return $lists;
	}
	public static function add_subscribed($api_key,$list_id,$email,$merge_fields){
		$dc = substr( $api_key, strpos( $api_key, '-' ) + 1 ); // datacenter, it is the part of your api key - us5, us8
		$status = "subscribed";
		$args = array(
		 	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )
			),
			'body' => json_encode(array(
    			'email_address' => $email,
				'status'        => $status,
				'merge_fields' => $merge_fields
			))
		);
		$response = wp_remote_post( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/', $args );
	}
}
